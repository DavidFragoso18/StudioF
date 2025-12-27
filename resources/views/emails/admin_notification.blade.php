<!DOCTYPE html>
<html>

<head>
    <title>Nouvelle Réservation</title>
</head>

<body style="font-family: Arial, sans-serif; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;">
        <h2 style="color: #8C7C6D;">Nouvelle Réservation reçue !</h2>

        <p>Bonjour,</p>
        <p>Une nouvelle réservation vient d'être effectuée sur le site StudioF.</p>

        <div style="background-color: #FDFBF7; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Client:</strong> {{ $appointment['customer_name'] }}</p>
            <p><strong>Téléphone:</strong> {{ $appointment['customer_phone'] }}</p>
            <p><strong>Email:</strong> {{ $appointment['customer_email'] ?? 'Non fourni' }}</p>
            <hr style="border: 0; border-top: 1px solid #ddd; margin: 10px 0;">
            <p><strong>Service:</strong> {{ $appointment->service->name }}</p>
            <p><strong>Date:</strong>
                {{ \Carbon\Carbon::parse($appointment['start_time'])->translatedFormat('l d F Y') }}</p>
            <p><strong>Heure:</strong> {{ \Carbon\Carbon::parse($appointment['start_time'])->format('H:i') }}</p>
        </div>

        <p><a href="{{ url('/dashboard') }}" style="color: #8C7C6D; font-weight: bold;">Voir dans le panneau
                d'administration</a></p>
    </div>
</body>

</html>