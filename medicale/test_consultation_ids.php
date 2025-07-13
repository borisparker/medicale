<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// Simuler une session docteur pour le test
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'docteur') {
    echo json_encode(['error' => 'Connectez-vous en tant que docteur pour ce test']);
    exit;
}

$user_id = $_SESSION['user']['id'];

try {
    // 1. Récupérer l'ID du docteur depuis la table doctors
    $stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $doctor = $stmt->fetch();
    
    if (!$doctor) {
        echo json_encode(['error' => 'Docteur introuvable dans la table doctors']);
        exit;
    }
    
    $doctor_id = $doctor['id'];
    
    // 2. Récupérer tous les rendez-vous confirmés de ce docteur
    $stmt = $pdo->prepare("
        SELECT 
            a.id as appointment_id,
            a.patient_id,
            a.doctor_id,
            a.date_heure,
            a.motif,
            a.statut,
            CONCAT(u.nom, ' ', u.prenom) as patient_name
        FROM appointments a
        INNER JOIN patients p ON a.patient_id = p.id
        INNER JOIN users u ON p.user_id = u.id
        WHERE a.doctor_id = ? AND a.statut = 'confirmé'
        ORDER BY a.date_heure DESC
    ");
    $stmt->execute([$doctor_id]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 3. Vérifier les correspondances
    $results = [
        'user_id' => $user_id,
        'doctor_id' => $doctor_id,
        'appointments_count' => count($appointments),
        'appointments' => []
    ];
    
    foreach ($appointments as $apt) {
        $results['appointments'][] = [
            'appointment_id' => $apt['appointment_id'],
            'patient_id' => $apt['patient_id'],
            'patient_name' => $apt['patient_name'],
            'date_heure' => $apt['date_heure'],
            'motif' => $apt['motif'],
            'statut' => $apt['statut'],
            'doctor_id_match' => ($apt['doctor_id'] == $doctor_id)
        ];
    }
    
    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Erreur: ' . $e->getMessage()]);
}
?> 