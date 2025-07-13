<?php
// Test simple pour vérifier l'API des rendez-vous admin
echo "<h2>Test de l'API des rendez-vous admin</h2>";

// Simuler une session admin
session_start();
$_SESSION['user'] = [
    'id' => 1,
    'email' => 'admin@test.com',
    'nom' => 'Admin',
    'prenom' => 'Test',
    'role' => 'admin'
];

// Test 1: Récupération des médecins
echo "<h3>1. Test récupération des médecins</h3>";
$doctors_url = 'http://localhost/medicale/get_admin_doctors.php';
$doctors_response = file_get_contents($doctors_url);
$doctors_data = json_decode($doctors_response, true);

if ($doctors_data && $doctors_data['success']) {
    echo "✅ Médecins récupérés avec succès<br>";
    echo "Nombre de médecins: " . count($doctors_data['doctors']) . "<br>";
    foreach ($doctors_data['doctors'] as $doctor) {
        echo "- " . $doctor['name'] . " (ID: " . $doctor['id'] . ")<br>";
    }
} else {
    echo "❌ Erreur lors de la récupération des médecins<br>";
    if ($doctors_data) {
        echo "Message: " . $doctors_data['message'] . "<br>";
    }
}

echo "<br>";

// Test 2: Récupération des rendez-vous
echo "<h3>2. Test récupération des rendez-vous</h3>";
$appointments_url = 'http://localhost/medicale/get_admin_appointments.php';
$appointments_response = file_get_contents($appointments_url);
$appointments_data = json_decode($appointments_response, true);

if ($appointments_data && $appointments_data['success']) {
    echo "✅ Rendez-vous récupérés avec succès<br>";
    echo "Nombre de rendez-vous: " . count($appointments_data['appointments']) . "<br>";
    echo "Total: " . $appointments_data['pagination']['total_count'] . "<br>";
    echo "Page actuelle: " . $appointments_data['pagination']['current_page'] . "<br>";
    echo "Pages totales: " . $appointments_data['pagination']['total_pages'] . "<br>";
    
    if (count($appointments_data['appointments']) > 0) {
        echo "<h4>Premier rendez-vous:</h4>";
        $first = $appointments_data['appointments'][0];
        echo "- Patient: " . $first['patient_name'] . "<br>";
        echo "- Médecin: " . $first['doctor_name'] . "<br>";
        echo "- Date: " . $first['date_heure'] . "<br>";
        echo "- Statut: " . $first['statut'] . "<br>";
    }
} else {
    echo "❌ Erreur lors de la récupération des rendez-vous<br>";
    if ($appointments_data) {
        echo "Message: " . $appointments_data['message'] . "<br>";
    }
}

echo "<br>";

// Test 3: Test avec filtres
echo "<h3>3. Test avec filtres</h3>";
$filtered_url = 'http://localhost/medicale/get_admin_appointments.php?status=en%20attente';
$filtered_response = file_get_contents($filtered_url);
$filtered_data = json_decode($filtered_response, true);

if ($filtered_data && $filtered_data['success']) {
    echo "✅ Filtres fonctionnent correctement<br>";
    echo "Rendez-vous 'en attente': " . count($filtered_data['appointments']) . "<br>";
} else {
    echo "❌ Erreur avec les filtres<br>";
}

echo "<br><a href='admin.php'>Retour à l'admin</a>";
?> 