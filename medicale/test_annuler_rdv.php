<?php
session_start();
require_once 'db.php';

echo "<h2>Test d'annulation de rendez-vous</h2>";

// Simuler une session patient
$_SESSION['user'] = [
    'id' => 15, // ID du patient créé dans les données de test
    'email' => 'jean.patient@test.com',
    'role' => 'patient'
];

$user_id = $_SESSION['user']['id'];
echo "<h3>Session patient: ID = $user_id</h3>";

try {
    // 1. Récupérer l'ID du patient
    $stmt = $pdo->prepare('SELECT id FROM patients WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $patient = $stmt->fetch();
    
    if (!$patient) {
        echo "❌ Patient non trouvé pour user_id: $user_id<br>";
        exit;
    }
    
    $real_patient_id = $patient['id'];
    echo "✅ Patient trouvé - ID réel: $real_patient_id<br>";
    
    // 2. Vérifier les rendez-vous du patient
    $stmt = $pdo->prepare('SELECT id, date_heure, statut FROM appointments WHERE patient_id = ? ORDER BY date_heure DESC');
    $stmt->execute([$real_patient_id]);
    $appointments = $stmt->fetchAll();
    
    echo "<h3>Rendez-vous du patient:</h3>";
    if (count($appointments) > 0) {
        foreach($appointments as $apt) {
            echo "- ID: " . $apt['id'] . " - Date: " . $apt['date_heure'] . " - Statut: " . $apt['statut'] . "<br>";
        }
        
        // 3. Tester l'annulation du premier rendez-vous
        $test_rdv_id = $appointments[0]['id'];
        echo "<h3>Test d'annulation du rendez-vous ID: $test_rdv_id</h3>";
        
        // Vérifier le statut actuel
        $stmt = $pdo->prepare('SELECT statut FROM appointments WHERE id = ? AND patient_id = ?');
        $stmt->execute([$test_rdv_id, $real_patient_id]);
        $current_status = $stmt->fetch();
        
        if ($current_status) {
            echo "✅ Rendez-vous trouvé - Statut actuel: " . $current_status['statut'] . "<br>";
            
            if ($current_status['statut'] === 'annulé') {
                echo "⚠️ Le rendez-vous est déjà annulé<br>";
            } elseif ($current_status['statut'] === 'terminé') {
                echo "⚠️ Le rendez-vous est terminé, impossible d'annuler<br>";
            } else {
                // Tenter l'annulation
                $stmt = $pdo->prepare('UPDATE appointments SET statut = ? WHERE id = ? AND patient_id = ?');
                $result = $stmt->execute(['annulé', $test_rdv_id, $real_patient_id]);
                
                if ($result) {
                    echo "✅ Annulation réussie !<br>";
                    
                    // Vérifier le nouveau statut
                    $stmt = $pdo->prepare('SELECT statut FROM appointments WHERE id = ?');
                    $stmt->execute([$test_rdv_id]);
                    $new_status = $stmt->fetch();
                    echo "✅ Nouveau statut: " . $new_status['statut'] . "<br>";
                } else {
                    echo "❌ Erreur lors de l'annulation<br>";
                    echo "Erreur SQL: " . json_encode($stmt->errorInfo()) . "<br>";
                }
            }
        } else {
            echo "❌ Rendez-vous non trouvé ou n'appartient pas au patient<br>";
        }
        
    } else {
        echo "❌ Aucun rendez-vous trouvé pour ce patient<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
}
?> 