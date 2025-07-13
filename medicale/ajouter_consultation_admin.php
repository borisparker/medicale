<?php
require_once 'auth.php';
require_role('admin');
require_once 'db.php';

header('Content-Type: application/json');

try {
    $medical_record_id = $_POST['medical_record_id'] ?? null;
    $doctor_id = $_POST['doctor_id'] ?? null;
    $type = $_POST['type'] ?? '';
    $motif = $_POST['motif'] ?? '';
    $observations = $_POST['observations'] ?? '';
    $date_consultation = $_POST['date_consultation'] ?? date('Y-m-d');
    
    if (!$medical_record_id || !$doctor_id || !$type || !$motif) {
        echo json_encode([
            'success' => false,
            'message' => 'Tous les champs obligatoires doivent être remplis'
        ]);
        exit;
    }
    
    // Vérifier que le dossier médical existe
    $stmt = $pdo->prepare("SELECT id FROM medical_records WHERE id = ?");
    $stmt->execute([$medical_record_id]);
    if (!$stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Dossier médical non trouvé'
        ]);
        exit;
    }
    
    // Vérifier que le médecin existe
    $stmt = $pdo->prepare("SELECT id FROM doctors WHERE id = ?");
    $stmt->execute([$doctor_id]);
    if (!$stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Médecin non trouvé'
        ]);
        exit;
    }
    
    // Insérer la consultation
    $stmt = $pdo->prepare("INSERT INTO consultations (medical_record_id, doctor_id, type, motif, observations, date_consultation) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $medical_record_id,
        $doctor_id,
        $type,
        $motif,
        $observations,
        $date_consultation
    ]);
    
    $consultation_id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Consultation ajoutée avec succès',
        'consultation_id' => $consultation_id
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'ajout de la consultation: ' . $e->getMessage()
    ]);
}
?> 