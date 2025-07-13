<?php
session_start();
require_once 'db.php';

echo "<h2>Test - Récupération de tous les rendez-vous</h2>";

// Simuler la session patient
$_SESSION['user'] = [
    'id' => 15,
    'email' => 'jean.patient@test.com',
    'role' => 'patient'
];

$user_id = $_SESSION['user']['id'];

// Récupérer le patient
$stmt = $pdo->prepare('SELECT id FROM patients WHERE user_id = ?');
$stmt->execute([$user_id]);
$patient = $stmt->fetch();
$patient_id = $patient['id'];

echo "<h3>Patient ID: $patient_id</h3>";

// Test 1: Tous les rendez-vous (sans JOIN)
echo "<h3>1. Tous les rendez-vous (sans JOIN)</h3>";
$stmt = $pdo->prepare('SELECT * FROM appointments WHERE patient_id = ? ORDER BY date_heure DESC');
$stmt->execute([$patient_id]);
$all_rdvs = $stmt->fetchAll();
echo "📅 Total: " . count($all_rdvs) . " rendez-vous<br>";
foreach($all_rdvs as $rdv) {
    echo "- ID: " . $rdv['id'] . " - Date: " . $rdv['date_heure'] . " - Doctor ID: " . $rdv['doctor_id'] . "<br>";
}

// Test 2: Avec LEFT JOIN
echo "<h3>2. Avec LEFT JOIN (nouvelle requête)</h3>";
$stmt = $pdo->prepare('
    SELECT 
        a.*, 
        d.id as doctor_id, 
        u.nom as doc_nom, 
        u.prenom as doc_prenom 
    FROM appointments a 
    LEFT JOIN doctors d ON a.doctor_id = d.id 
    LEFT JOIN users u ON d.user_id = u.id 
    WHERE a.patient_id = ? 
    ORDER BY a.date_heure DESC
');
$stmt->execute([$patient_id]);
$left_join_rdvs = $stmt->fetchAll();
echo "📅 Total avec LEFT JOIN: " . count($left_join_rdvs) . " rendez-vous<br>";
foreach($left_join_rdvs as $rdv) {
    $nom_medecin = 'Dr. ' . ($rdv['doc_prenom'] ?? '') . ' ' . ($rdv['doc_nom'] ?? '');
    $nom_medecin = trim($nom_medecin);
    if (empty($nom_medecin) || $nom_medecin === 'Dr. ') {
        $nom_medecin = 'Médecin inconnu';
    }
    echo "- ID: " . $rdv['id'] . " - Date: " . $rdv['date_heure'] . " - Médecin: " . $nom_medecin . "<br>";
}

// Test 3: Avec INNER JOIN (ancienne requête)
echo "<h3>3. Avec INNER JOIN (ancienne requête)</h3>";
$stmt = $pdo->prepare('
    SELECT 
        a.*, 
        d.id as doctor_id, 
        u.nom as doc_nom, 
        u.prenom as doc_prenom 
    FROM appointments a 
    JOIN doctors d ON a.doctor_id = d.id 
    JOIN users u ON d.user_id = u.id 
    WHERE a.patient_id = ? 
    ORDER BY a.date_heure DESC
');
$stmt->execute([$patient_id]);
$inner_join_rdvs = $stmt->fetchAll();
echo "📅 Total avec INNER JOIN: " . count($inner_join_rdvs) . " rendez-vous<br>";
foreach($inner_join_rdvs as $rdv) {
    $nom_medecin = 'Dr. ' . ($rdv['doc_prenom'] ?? '') . ' ' . ($rdv['doc_nom'] ?? '');
    echo "- ID: " . $rdv['id'] . " - Date: " . $rdv['date_heure'] . " - Médecin: " . $nom_medecin . "<br>";
}

echo "<h3>✅ Résumé</h3>";
echo "Sans JOIN: " . count($all_rdvs) . " rendez-vous<br>";
echo "Avec LEFT JOIN: " . count($left_join_rdvs) . " rendez-vous<br>";
echo "Avec INNER JOIN: " . count($inner_join_rdvs) . " rendez-vous<br>";

if(count($left_join_rdvs) === count($all_rdvs)) {
    echo "✅ LEFT JOIN récupère tous les rendez-vous !";
} else {
    echo "❌ Problème avec LEFT JOIN";
}
?> 