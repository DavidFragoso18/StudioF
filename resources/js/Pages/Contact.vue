<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref, watch, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    services: Array
});

// State
const bookingStep = ref(1); // 1: Date, 2: Time, 3: Service/Details
const availableSlots = ref([]);
const loadingSlots = ref(false);
const minDate = new Date().toISOString().split('T')[0];

const form = useForm({
    date: '',
    time: '',
    service_id: '',
    name: '',
    phone: '',
    email: ''
});

// Methods for slots fetching
const latestError = ref(null);

const fetchSlots = async () => {
    console.log('Fetching slots for:', form.date, form.service_id);
    latestError.value = null; // Clear error
    
    if (!form.date || !form.service_id) {
        console.warn('Missing date or service_id');
        return;
    }
    
    loadingSlots.value = true;
    form.time = ''; 
    availableSlots.value = [];

    try {
        // Ensure route exists
        const url = route('appointments.slots');
        const response = await axios.post(url, { 
            date: form.date,
            service_id: form.service_id 
        });
        console.log('Slots response:', response.data);
        availableSlots.value = response.data;
    } catch (e) {
        console.error('Error fetching slots:', e);
        latestError.value = e.message || 'Unknown error';
    } finally {
        loadingSlots.value = false;
    }
};

const resetSlots = () => {
    form.time = '';
    availableSlots.value = [];
    if (form.date) {
        fetchSlots();
    }
};

// Calendar Logic
const calendarRefDate = ref(new Date());

const isCurrentMonth = computed(() => {
    const now = new Date();
    return calendarRefDate.value.getMonth() === now.getMonth() && calendarRefDate.value.getFullYear() === now.getFullYear();
});

const currentMonthName = computed(() => {
    return calendarRefDate.value.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
});

const startOfMonthOffset = computed(() => {
    const y = calendarRefDate.value.getFullYear();
    const m = calendarRefDate.value.getMonth();
    const firstDay = new Date(y, m, 1).getDay();
    // JS getDay: 0=Sun, 1=Mon... we want Mon=0, Sun=6
    // If Sun(0) -> 6. If Mon(1) -> 0.
    return firstDay === 0 ? 6 : firstDay - 1;
});

const daysInMonth = computed(() => {
    const y = calendarRefDate.value.getFullYear();
    const m = calendarRefDate.value.getMonth();
    const days = new Date(y, m + 1, 0).getDate();
    const result = [];
    const today = new Date();
    today.setHours(0,0,0,0);

    for (let d = 1; d <= days; d++) {
        const dateObj = new Date(y, m, d);
        const dayOfWeek = dateObj.getDay();
        // Check closed (Sun=0, Mon=1)
        const isClosed = dayOfWeek === 0 || dayOfWeek === 1; 
        const isPast = dateObj < today;
        // Format YYYY-MM-DD manually to avoid locale issues
        const dateString = `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        
        result.push({
            date: d,
            dateString,
            isClosed,
            isPast,
            isToday: dateObj.getTime() === today.getTime()
        });
    }
    return result;
});

const changeMonth = (delta) => {
    const newDate = new Date(calendarRefDate.value);
    newDate.setMonth(newDate.getMonth() + delta);
    calendarRefDate.value = newDate;
};

const selectDate = (dateString) => {
    form.date = dateString;
    // Auto fetch is handled by watch(form.date)
};

// Slot Grouping
const morningSlots = computed(() => availableSlots.value.filter(s => parseInt(s.split(':')[0]) < 12));
const afternoonSlots = computed(() => availableSlots.value.filter(s => {
    const h = parseInt(s.split(':')[0]);
    return h >= 12 && h < 17;
}));
const eveningSlots = computed(() => availableSlots.value.filter(s => parseInt(s.split(':')[0]) >= 17));


const expandedCategory = ref(null);

const toggleCategory = (category) => {
    if (expandedCategory.value === category) {
        expandedCategory.value = null;
    } else {
        expandedCategory.value = category;
    }
};

const selectService = (service) => {
    form.service_id = service.id;
    bookingStep.value = 2; // Next step
    resetSlots();
};

const selectTime = (slot) => {
    form.time = slot;
    bookingStep.value = 3; // Move to details
};

// Auto-fetch slots when date or service changes
watch(() => [form.date, form.service_id], () => {
    if (form.date && form.service_id) {
        fetchSlots();
    }
});

const lastBooking = ref(null);

const confirmBooking = () => {
    // 1. Gather Details
    const service = props.services.find(s => s.id === form.service_id);
    const dateObj = new Date(form.date);
    
    lastBooking.value = {
        name: form.name,
        serviceName: service ? service.name : 'Rendez-vous StudioF',
        serviceDuration: service ? service.duration_minutes : 60,
        date: form.date,
        time: form.time,
        dateFormatted: dateObj.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
    };

    form.post(route('appointments.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            bookingStep.value = 4; // Show Success
            form.reset();
            // scroll top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
};

const submitBooking = confirmBooking; // alias

// Calendar Helpers
const googleCalendarLink = computed(() => {
    if (!lastBooking.value) return '#';
    
    const start = new Date(`${lastBooking.value.date}T${lastBooking.value.time}`);
    const end = new Date(start.getTime() + lastBooking.value.serviceDuration * 60000);
    
    // Format YYYYMMDDTHHMMSSZ (UTC) for Google
    // Or simple YYYYMMDDTHHMMSS without Z for local time (safest for cross-timezone if frontend assumes local)
    // Let's use simple local string format YYYYMMDDTHHMMSS
    const formatTime = (d) => d.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z'; 
    // Wait, ISO is UTC. If user selected 10:00 local, and browser is UTC+1, ISO will be 09:00Z. That is correct.
    
    const dates = `${formatTime(start)}/${formatTime(end)}`;
    const text = encodeURIComponent(`StudioF: ${lastBooking.value.serviceName}`);
    const details = encodeURIComponent("Rendez-vous confirmé chez StudioF.");
    const location = encodeURIComponent("La Prairie 1, 1721 Cournillens, Suíça");
    
    return `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${text}&dates=${dates}&details=${details}&location=${location}`;
});

const downloadIcs = () => {
    if (!lastBooking.value) return;

    const start = new Date(`${lastBooking.value.date}T${lastBooking.value.time}`);
    const end = new Date(start.getTime() + lastBooking.value.serviceDuration * 60000);
    const formatTime = (d) => d.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';

    const icsContent = `BEGIN:VCALENDAR
VERSION:2.0
BEGIN:VEVENT
URL:${window.location.origin}
DTSTART:${formatTime(start)}
DTEND:${formatTime(end)}
SUMMARY:StudioF: ${lastBooking.value.serviceName}
DESCRIPTION:Rendez-vous confirmé.
LOCATION:La Prairie 1, 1721 Cournillens, Suíça
END:VEVENT
END:VCALENDAR`;

    const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
    const link = document.createElement('a');
    link.href = window.URL.createObjectURL(blob);
    link.setAttribute('download', 'rendez-vous-studiof.ics');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

// UI Helpers for Step 3
const getServiceName = (id) => {
    const service = props.services.find(s => s.id === id);
    return service ? service.name : '';
};

const getServicePrice = (id) => {
    const service = props.services.find(s => s.id === id);
    return service ? service.price_display : '';
};

const formatDate = (dateUnformatted) => {
    if (!dateUnformatted) return '';
    const date = new Date(dateUnformatted);
    return date.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
};

const groupedServices = computed(() => {
    // Re-use grouping logic or simpler list
     return props.services.reduce((acc, service) => {
        if (!acc[service.category]) {
            acc[service.category] = [];
        }
        acc[service.category].push(service);
        return acc;
    }, {});
});
</script>

<template>
    <Head title="Contact" />

    <PublicLayout>
        <div class="min-h-screen py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 animate-fade-in delay-100">
                    <h1 class="text-4xl md:text-5xl font-serif text-charcoal mb-6">Contactez <span class="text-mocha">Nous</span></h1>
                    <p class="text-charcoal/60 text-lg">Prenons soin de vous.</p>
                </div>

                <!-- Wizard Container -->
                <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-2xl overflow-hidden relative animate-slide-up mb-24">
                    
                    <!-- Header (Shared across steps) -->
                    <div class="pt-8 pb-4 px-6 text-center border-b border-gray-100 relative">
                        <button v-if="bookingStep > 1" @click="bookingStep--" class="absolute left-6 top-1/2 -translate-y-1/2 p-2 hover:bg-gray-100 rounded-full transition">
                            <svg class="w-6 h-6 text-charcoal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <h2 class="text-2xl font-bold text-charcoal">Nouveau Rendez-vous</h2>
                        <p class="text-gray-400 text-sm mt-1" v-if="bookingStep === 1">Choisissez une prestation</p>
                        <p class="text-gray-400 text-sm mt-1" v-if="bookingStep === 2">Choisissez une date</p>
                        <p class="text-gray-400 text-sm mt-1" v-if="bookingStep === 3">Confirmer le rendez-vous</p>
                    </div>

                    <!-- Step 1: Service List (Accordion Style) -->
                    <div v-if="bookingStep === 1" class="divide-y divide-gray-100">
                        <div v-for="(services, category) in groupedServices" :key="category">
                             <!-- Category Header -->
                             <button 
                                @click="toggleCategory(category)"
                                class="w-full flex justify-between items-center px-6 py-4 bg-gray-50 hover:bg-gray-100 transition text-left"
                             >
                                <span class="font-bold text-charcoal uppercase tracking-widest text-sm">{{ category }}</span>
                                <svg 
                                    class="w-5 h-5 text-gray-400 transform transition duration-300"
                                    :class="expandedCategory === category ? 'rotate-180' : ''"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                             </button>
                             
                             <!-- Collapsible List -->
                             <div v-if="expandedCategory === category" class="animate-slide-down bg-white">
                                 <button 
                                    v-for="service in services" 
                                    :key="service.id" 
                                    @click="selectService(service)"
                                    class="w-full flex justify-between items-center px-6 py-5 hover:bg-mocha/5 transition group text-left border-b border-gray-50 last:border-0"
                                >
                                    <div>
                                        <span class="text-charcoal font-medium text-lg group-hover:text-mocha transition">{{ service.name }}</span>
                                        <span v-if="service.description" class="block text-gray-400 text-xs mt-1">{{ service.description }}</span>
                                    </div>
                                    <span class="text-charcoal font-bold">{{ service.price_display }}</span>
                                 </button>
                             </div>
                        </div>
                    </div>

                    <!-- Step 2: Custom Calendar & Slots -->
                    <div v-if="bookingStep === 2" class="p-6">
                        
                        <!-- Month Navigation -->
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-charcoal capitalize">{{ currentMonthName }}</h3>
                            <div class="flex gap-2">
                                <button @click="changeMonth(-1)" :disabled="isCurrentMonth" class="p-2 rounded-full hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed">
                                    <svg class="w-5 h-5 text-charcoal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <button @click="changeMonth(1)" class="p-2 rounded-full hover:bg-gray-100">
                                    <svg class="w-5 h-5 text-charcoal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Days Grid -->
                        <div class="grid grid-cols-7 gap-1 mb-8 text-center">
                            <!-- Weekday Headers -->
                            <div v-for="day in ['seg', 'ter', 'qua', 'qui', 'ven', 'sam', 'dim']" :key="day" class="text-xs font-bold text-gray-400 uppercase tracking-wide py-2">
                                {{ day }}
                            </div>
                            
                            <!-- Empty fillers for start of month -->
                            <div v-for="n in startOfMonthOffset" :key="`empty-${n}`" class="p-2 text-transparent select-none">.</div>

                            <!-- Calendar Days -->
                             <button 
                                v-for="day in daysInMonth" 
                                :key="day.date" 
                                @click="selectDate(day.dateString)"
                                :disabled="day.isPast || day.isClosed"
                                :class="[
                                    'aspect-square flex items-center justify-center rounded-full text-sm font-medium transition duration-200 relative',
                                    form.date === day.dateString ? 'bg-charcoal text-white shadow-lg scale-105' : 'text-charcoal hover:bg-gray-100',
                                    (day.isPast || day.isClosed) ? 'text-gray-300 cursor-not-allowed hover:bg-transparent' : ''
                                ]"
                            >
                                {{ day.date }}
                                <!-- Dot indicator for today -->
                                <span v-if="day.isToday" class="absolute bottom-1 w-1 h-1 rounded-full" :class="form.date === day.dateString ? 'bg-white' : 'bg-mocha'"></span>
                            </button>
                        </div>

                        <!-- Time Slots (Only if Date Selected) -->
                        <div v-if="form.date" class="animate-fade-in border-t border-gray-100 pt-6">
                            
                             <div v-if="loadingSlots" class="text-center py-8 text-gray-500 flex flex-col items-center">
                                 <svg class="animate-spin h-6 w-6 text-mocha mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                 <span class="text-sm">Chargement...</span>
                             </div>

                             <div v-else-if="availableSlots.length === 0" class="text-center py-8 text-gray-400 text-sm">
                                 <span v-if="!form.service_id">Veuillez sélectionner une prestation.</span>
                                 <span v-else>Aucun créneau disponible ce jour-là.</span>
                             </div>

                             <div v-else class="space-y-6">
                                 <!-- Group: Matin (until 12:00) -->
                                 <div v-if="morningSlots.length > 0">
                                     <h4 class="text-sm font-bold text-gray-400 mb-3 ml-1">Matin</h4>
                                     <div class="grid grid-cols-4 gap-3">
                                         <button v-for="slot in morningSlots" :key="slot" @click="selectTime(slot)" class="py-3 px-2 bg-white border border-gray-200 rounded-xl text-charcoal font-bold text-sm hover:border-mocha hover:text-mocha transition shadow-sm">
                                             {{ slot }}
                                         </button>
                                     </div>
                                 </div>

                                 <!-- Group: Après-midi (12:00 - 17:00) -->
                                 <div v-if="afternoonSlots.length > 0">
                                     <h4 class="text-sm font-bold text-gray-400 mb-3 ml-1">Après-midi</h4>
                                     <div class="grid grid-cols-4 gap-3">
                                         <button v-for="slot in afternoonSlots" :key="slot" @click="selectTime(slot)" class="py-3 px-2 bg-white border border-gray-200 rounded-xl text-charcoal font-bold text-sm hover:border-mocha hover:text-mocha transition shadow-sm">
                                             {{ slot }}
                                         </button>
                                     </div>
                                 </div>

                                 <!-- Group: Soir (17:00+) -->
                                 <div v-if="eveningSlots.length > 0">
                                     <h4 class="text-sm font-bold text-gray-400 mb-3 ml-1">Soir</h4>
                                     <div class="grid grid-cols-4 gap-3">
                                         <button v-for="slot in eveningSlots" :key="slot" @click="selectTime(slot)" class="py-3 px-2 bg-white border border-gray-200 rounded-xl text-charcoal font-bold text-sm hover:border-mocha hover:text-mocha transition shadow-sm">
                                             {{ slot }}
                                         </button>
                                     </div>
                                 </div>
                             </div>
                        </div>

                    </div>
                    <!-- Step 3: Form & Confirmation -->
                     <div v-if="bookingStep === 3" class="px-6 py-8 animate-slide-up">
                         
                         <!-- Summary Card -->
                         <div class="bg-gray-50 rounded-2xl p-6 mb-8 border border-gray-100">
                             <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Récapitulatif</h4>
                             <div class="flex justify-between items-start mb-2">
                                 <div>
                                     <p class="font-serif text-xl text-charcoal">{{ getServiceName(form.service_id) }}</p>
                                     <p class="text-sm text-gray-500 mt-1 capitalize">{{ formatDate(form.date) }} / {{ form.time }}</p>
                                 </div>
                                 <span class="text-xl font-bold text-mocha">{{ getServicePrice(form.service_id) }}</span>
                             </div>
                             <div class="border-t border-gray-200 mt-4 pt-4 flex justify-between items-center">
                                 <span class="text-sm font-medium text-gray-600">Total</span>
                                 <span class="text-xl font-bold text-charcoal">{{ getServicePrice(form.service_id) }}</span>
                             </div>
                         </div>

                        <!-- Form Fields -->
                        <form @submit.prevent="submitBooking" class="space-y-4">
                            <div>
                                <input type="text" v-model="form.name" required placeholder="Votre Nom et Prénom" class="w-full bg-white border border-gray-200 text-charcoal p-4 rounded-xl focus:border-charcoal focus:ring-0 outline-none transition placeholder-gray-400">
                            </div>

                            <!-- Phone with flag hint (Visual only) -->
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="mr-2">🇨🇭</span> <span class="text-gray-400 border-r border-gray-200 pr-2 h-5 flex items-center">+41</span>
                                </div>
                                <input type="tel" v-model="form.phone" required placeholder="79 123 45 67" class="w-full bg-white border border-gray-200 text-charcoal p-4 pl-24 rounded-xl focus:border-charcoal focus:ring-0 outline-none transition placeholder-gray-400">
                            </div>

                            <div>
                                <input type="email" v-model="form.email" placeholder="Email (pour la confirmation)" class="w-full bg-white border border-gray-200 text-charcoal p-4 rounded-xl focus:border-charcoal focus:ring-0 outline-none transition placeholder-gray-400">
                            </div>
                            
                            <div>
                                <textarea placeholder="Observations (optionnel)" class="w-full bg-white border border-gray-200 text-charcoal p-4 rounded-xl focus:border-charcoal focus:ring-0 outline-none transition placeholder-gray-400 h-24 resize-none"></textarea>
                            </div>

                            <!-- Terms -->
                            <div class="space-y-3 pt-2">
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <input type="checkbox" required class="mt-1 w-5 h-5 rounded border-gray-300 text-charcoal focus:ring-charcoal transition cursor-pointer">
                                    <span class="text-sm text-gray-500 leading-tight group-hover:text-charcoal transition">J'ai lu et j'accepte les <span class="underline">Conditions Générales</span> et la Politique de Confidentialité.</span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-6">
                                <button type="submit" :disabled="form.processing" class="w-full bg-charcoal text-white p-5 rounded-xl font-bold text-lg hover:bg-black transition shadow-xl disabled:opacity-50">
                                    <span v-if="form.processing">Confirmation...</span>
                                    <span v-else>Confirmer</span>
                                </button>
                            </div>
                        </form>
                    </div>
                      <div v-if="bookingStep === 4" class="p-12 text-center space-y-6">
                        <div class="mb-4">
                            <svg class="w-16 h-16 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h2 class="text-3xl font-bold text-mocha mb-2">Rendez-vous Confirmé !</h2>
                        <p class="text-gray-500">Merci, nous avons bien reçu votre demande.</p>
                        
                        <!-- Add to Calendar Options -->
                        <div class="flex flex-col gap-3 max-w-xs mx-auto pt-4">
                            <a :href="googleCalendarLink" target="_blank" class="flex items-center justify-center gap-2 bg-white border border-gray-200 text-charcoal py-3 px-4 rounded-xl hover:bg-gray-50 hover:border-mocha transition font-medium shadow-sm">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zm-7 5h5v5h-5v-5z"/></svg>
                                Ajouter à Google Agenda
                            </a>
                            <button @click="downloadIcs" class="flex items-center justify-center gap-2 bg-white border border-gray-200 text-charcoal py-3 px-4 rounded-xl hover:bg-gray-50 hover:border-mocha transition font-medium shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Télécharger iCal / Outlook
                            </button>
                        </div>

                         <button @click="bookingStep = 1" class="text-gray-400 underline text-sm mt-4 hover:text-charcoal transition">Retour à l'accueil</button>
                    </div>

                </div>

                <div class="grid md:grid-cols-2 gap-12 items-start animate-fade-in delay-300">
                    <!-- Info -->
                    <div class="bg-white p-10 rounded-3xl border border-concrete/30 hover:border-mocha/30 transition duration-500 h-full flex flex-col justify-center shadow-lg">
                        <div class="space-y-10">
                             <div class="group">
                                <h3 class="text-mocha text-sm font-bold uppercase tracking-widest mb-2 group-hover:text-charcoal transition">Adresse</h3>
                                <p class="text-charcoal text-2xl font-serif">La Prairie 1</p>
                                <p class="text-charcoal text-2xl font-serif">1721 Cournillens, Suíça</p>
                            </div>

                            <div class="group">
                                <h3 class="text-mocha text-sm font-bold uppercase tracking-widest mb-2 group-hover:text-charcoal transition">Téléphone</h3>
                                <p class="text-gray-500 mb-2 text-sm italic">Pour rendez-vous et renseignements :</p>
                                <a href="tel:0795019212" class="text-3xl lg:text-4xl text-charcoal font-bold hover:text-mocha transition duration-300 block">079 501 92 12</a>
                            </div>

                            <div>
                                <h3 class="text-mocha text-sm font-bold uppercase tracking-widest mb-4">Horaires</h3>
                                <p class="text-charcoal text-xl font-serif italic">Sur rendez-vous</p>
                            </div>
                        </div>
                    </div>

                    <!-- Map -->
                    <!-- Map (Clickable to open Maps App) -->
                    <a 
                        href="https://www.google.com/maps/search/?api=1&query=46.859278,7.107389" 
                        target="_blank"
                        rel="noopener noreferrer"
                        class="block h-[450px] w-full bg-gray-200 rounded-3xl overflow-hidden border border-concrete/30 shadow-xl group relative cursor-pointer"
                    >
                         <!-- Google Maps Embed -->
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2737.5686000000005!2d7.107389!3d46.859278!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDbCsDUxJzMzLjQiTiA3wrAwNicyNi42IkU!5e0!3m2!1sen!2sch!4v1612345678901!5m2!1sen!2sch" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy"
                            class="grayscale group-hover:grayscale-0 transition duration-1000 pointer-events-none w-full h-full">
                        </iframe>
                        
                        <!-- Overlay Hint -->
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition duration-300 flex items-center justify-center">
                            <span class="bg-white/90 backdrop-blur px-6 py-2 rounded-full text-mocha font-bold text-sm shadow-lg opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition duration-300">
                                Ouvrir dans Maps
                            </span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
