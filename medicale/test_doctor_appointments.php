<?php
session_start();
require_once 'db.php';

echo "<h2>Test - Gestion des rendez-vous par les docteurs</h2>";

// Simuler la session docteur
$_SESSION['user'] = [
    'id' => 16, // ID du docteur créé dans les données de test
    'email' => 'dr.sarah@test.com',
    'role' => 'docteur'
];

$user_id = $_SESSION['user']['id'];

// Récupérer le docteur
$stmt = $pdo->prepare('SELECT id FROM doctors WHERE user_id = ?');
$stmt->execute([$user_id]);
$doctor = $stmt->fetch();
$doctor_id = $doctor['id'];

echo "<h3>Docteur ID: $doctor_id</h3>";

// Test 1: Vérifier les rendez-vous du docteur
echo "<h3>1. Rendez-vous du docteur</h3>";
$stmt = $pdo->prepare('
    SELECT 
        a.*, 
        p.id as patient_id, 
        u.nom as patient_nom, 
        u.prenom as patient_prenom,
        u.telephone as patient_telephone
    FROM appointments a 
    LEFT JOIN patients p ON a.patient_id = p.id 
    LEFT JOIN users u ON p.user_id = u.id 
    WHERE a.doctor_id = ? 
    ORDER BY a.date_heure DESC
');
$stmt->execute([$doctor_id]);
$rdvs = $stmt->fetchAll();

echo "📅 Nombre de rendez-vous: " . count($rdvs) . "<br>";
foreach($rdvs as $rdv) {
    $dt = new DateTime($rdv['date_heure']);
    $date = $dt->format('d/m/Y');
    $heure = $dt->format('H:i');
    $nom_patient = ($rdv['patient_prenom'] ?? '') . ' ' . ($rdv['patient_nom'] ?? '');
    $nom_patient = trim($nom_patient);
    if (empty($nom_patient)) {
        $nom_patient = 'Patient inconnu';
    }
    
    echo "- ID: " . $rdv['id'] . " - Date: " . $date . " " . $heure . " - Patient: " . $nom_patient . " - Statut: " . $rdv['statut'] . "<br>";
}

// Test 2: Simuler la mise à jour d'un statut
if(count($rdvs) > 0) {
    echo "<h3>2. Test de mise à jour du statut</h3>";
    $test_rdv = $rdvs[0];
    $old_status = $test_rdv['statut'];
    $new_status = ($old_status === 'en attente') ? 'confirmé' : 'en attente';
    
    echo "Test de changement du statut du rendez-vous " . $test_rdv['id'] . " de '$old_status' vers '$new_status'<br>";
    
    try {
        $stmt = $pdo->prepare('UPDATE appointments SET statut = ? WHERE id = ? AND doctor_id = ?');
        $stmt->execute([$new_status, $test_rdv['id'], $doctor_id]);
        
        if($stmt->rowCount() > 0) {
            echo "✅ Statut mis à jour avec succès<br>";
            
            // Remettre l'ancien statut pour le test
            $stmt = $pdo->prepare('UPDATE appointments SET statut = ? WHERE id = ?');
            $stmt->execute([$old_status, $test_rdv['id']]);
            echo "🔄 Statut remis à '$old_status'<br>";
        } else {
            echo "❌ Erreur lors de la mise à jour<br>";
        }
    } catch(Exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "<br>";
    }
}

echo "<h3>✅ Tests terminés</h3>";
echo "<p>Si tu vois des rendez-vous ici, le système fonctionne correctement.</p>";
?> 