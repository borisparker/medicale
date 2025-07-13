<?php
// Test simple pour vérifier get_appointments.php
echo "<h2>Test de get_appointments.php</h2>";

// Simuler une session patient
session_start();
$_SESSION['user'] = [
    'id' => 15, // ID du patient créé dans les données de test
    'email' => 'jean.patient@test.com',
    'role' => 'patient'
];

echo "<h3>Session simulée:</h3>";
echo "ID: " . $_SESSION['user']['id'] . "<br>";
echo "Email: " . $_SESSION['user']['email'] . "<br>";
echo "Rôle: " . $_SESSION['user']['role'] . "<br>";

echo "<h3>Réponse de get_appointments.php:</h3>";
echo "<pre>";

// Capturer la sortie de get_appointments.php
ob_start();
include 'get_appointments.php';
$output = ob_get_clean();

echo htmlspecialchars($output);
echo "</pre>";

echo "<h3>JSON décodé:</h3>";
$data = json_decode($output, true);
echo "<pre>";
print_r($data);
echo "</pre>";
?> 