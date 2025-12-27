<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function getSlots(Request $request)
    {
        $date = Carbon::parse($request->input('date'));
        $dayOfWeek = $date->dayOfWeek; // 0=Sun, 1=Mon, ..., 6=Sat

        // Closed Sunday (0) and Monday (1)
        if ($dayOfWeek === 0 || $dayOfWeek === 1) {
            return response()->json([]);
        }

        // Define open hours
        $startHour = ($dayOfWeek === 6) ? 8 : 9; // Sat: 8am, others: 9am
        $endHour = ($dayOfWeek === 6) ? 16 : 18; // Sat: 16pm, others: 18:30pm (handle minutes logic below)
        $endMinute = ($dayOfWeek === 6) ? 0 : 30;

        $startTime = $date->copy()->setTime($startHour, 0);
        $endTime = $date->copy()->setTime($endHour, $endMinute);

        // Fetch existing appointments (Local)
        $localAppointments = Appointment::whereDate('start_time', $date->toDateString())->get();

        // Fetch Google Calendar Events
        $googleEvents = collect();
        try {
            $googleEvents = \Spatie\GoogleCalendar\Event::get(
                $startTime->copy()->subHours(2), // buffer for events starting before open
                $endTime->copy()->addHours(1)
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('GCal Fetch Error: ' . $e->getMessage());
        }

        // Determine duration - Default 30 min if no service selected (fallback)
        $durationMinutes = 30;
        if ($request->has('service_id') && $request->input('service_id')) {
            $service = Service::find($request->input('service_id'));
            if ($service) {
                $durationMinutes = $service->duration_minutes;
            }
        }

        $slots = [];
        $currentSlot = $startTime->copy();

        // 15 min default interval for slots showing
        while ($currentSlot->lt($endTime)) {
            // Calculate potential Appointment End based on Service Duration
            $potentialEnd = $currentSlot->copy()->addMinutes($durationMinutes);

            // Check Local Conflicts
            $isTakenLocal = $localAppointments->contains(function ($appointment) use ($currentSlot, $potentialEnd) {
                return $currentSlot->lt($appointment->end_time) && $potentialEnd->gt($appointment->start_time);
            });

            // Check Google Calendar Conflicts (Double Buffer: -15m ... +15m)
            $isTakenGoogle = $googleEvents->contains(function ($event) use ($currentSlot, $potentialEnd) {
                $blockedStart = $event->startDateTime->copy()->subMinutes(15);
                $blockedEnd = $event->endDateTime->copy()->addMinutes(15);
                return $currentSlot->lt($blockedEnd) && $potentialEnd->gt($blockedStart);
            });

            if (!$isTakenLocal && !$isTakenGoogle) {
                $slots[] = $currentSlot->format('H:i');
            }

            $currentSlot->addMinutes(60);
        }

        return response()->json($slots);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required', // H:i
            'service_id' => 'required|exists:services,id',
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        $start = Carbon::parse($validated['date'] . ' ' . $validated['time']);
        $end = $start->copy()->addMinutes($service->duration_minutes);

        // Double check availability (race condition / robust check)
        $conflict = Appointment::where(function ($query) use ($start, $end) {
            $query->whereBetween('start_time', [$start, $end])
                ->orWhereBetween('end_time', [$start, $end])
                ->orWhere(function ($q) use ($start, $end) {
                    $q->where('start_time', '<', $start)
                        ->where('end_time', '>', $end);
                });
        })->exists();

        if ($conflict) {
            return back()->withErrors(['time' => 'Ce créneau n\'est plus disponible.']);
        }

        // Check Google Calendar Conflicts (+15 min buffer)
        try {
            // Fetch events starting 2 hours before to ensure we catch ongoing/just-finished events
            $googleEvents = \Spatie\GoogleCalendar\Event::get($start->copy()->subHours(2), $end->copy()->addMinutes(15));
            foreach ($googleEvents as $event) {
                // Check overlap logic: (StartA < EndB) and (EndA > StartB)
                // Effective Blocked Range: [EventStart - 15m, EventEnd + 15m]

                $blockedStart = $event->startDateTime->copy()->subMinutes(15);
                $blockedEnd = $event->endDateTime->copy()->addMinutes(15);

                if ($start->lt($blockedEnd) && $end->gt($blockedStart)) {
                    return back()->withErrors(['time' => 'Conflit avec l\'agenda Google (pause 15m avant/après requise).']);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('GCal Store Check Error: ' . $e->getMessage());
        }

        // Create Google Calendar Event
        $googleEventId = null;
        try {
            $event = new \Spatie\GoogleCalendar\Event;
            $event->name = 'StudioF: ' . $service->name . ' - ' . $validated['name'];
            $event->startDateTime = $start;
            $event->endDateTime = $end;
            $event->description = "Cliente: {$validated['name']}\nTel: {$validated['phone']}\nEmail: " . ($validated['email'] ?? 'N/A');

            if (!empty($validated['email'])) {
                $event->addAttendee([
                    'email' => $validated['email'],
                    'displayName' => $validated['name'],
                ]);
            }

            $createdEvent = $event->save();
            $googleEventId = $createdEvent->id;
        } catch (\Exception $e) {
            // Log error but continue booking
            \Illuminate\Support\Facades\Log::error('Google Calendar Error: ' . $e->getMessage());
        }

        // Create Local Appointment
        $appointment = Appointment::create([
            'service_id' => $validated['service_id'],
            'customer_name' => $validated['name'],
            'customer_phone' => $validated['phone'],
            'customer_email' => $validated['email'],
            'start_time' => $start,
            'end_time' => $end,
            'status' => 'confirmed',
            'google_event_id' => $googleEventId,
        ]);

        // Send Firebase Notification (Topic: appointments)
        try {
            $messaging = app('firebase.messaging');
            $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('topic', 'appointments')
                ->withNotification(\Kreait\Firebase\Messaging\Notification::create(
                    'Novo Agendamento',
                    "{$validated['name']} agendou {$service->name} para {$start->format('d/m H:i')}"
                ))
                ->withData([
                    'service_id' => (string) $service->id,
                    'appointment_date' => $start->toIso8601String(),
                ]);

            $messaging->send($message);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Firebase Error: ' . $e->getMessage());
        }

        // Send Email Notification to Admin
        try {
            \Illuminate\Support\Facades\Mail::to('fernandaconde021@gmail.com')->send(new \App\Mail\NewAppointmentAdminNotification($appointment));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Email Error: ' . $e->getMessage());
        }

        return redirect()->route('contact')->with('success', 'Rendez-vous confirmé !');
    }
}
