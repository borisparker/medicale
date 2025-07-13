<?php
require_once 'auth.php';
require_role('admin');
require_once 'db.php';

header('Content-Type: application/json');

// Vérifier les données reçues
$appointment_id = $_POST['appointment_id'] ?? '';
$status = $_POST['status'] ?? '';

if (!$appointment_id || !$status) {
    echo json_encode([
        'success' => false,
        'message' => 'ID du rendez-vous et statut requis'
    ]);
    exit;
}

// Valider le statut
$validStatuses = ['confirmé', 'en attente', 'annulé', 'terminé'];
if (!in_array($status, $validStatuses)) {
    echo json_encode([
        'success' => false,
        'message' => 'Statut invalide. Statuts autorisés: ' . implode(', ', $validStatuses)
    ]);
    exit;
}

try {
    // Vérifier que le rendez-vous existe
    $checkStmt = $pdo->prepare("SELECT id FROM appointments WHERE id = ?");
    $checkStmt->execute([$appointment_id]);
    
    if (!$checkStmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Rendez-vous introuvable'
        ]);
        exit;
    }
    
    // Mettre à jour le statut
    $updateStmt = $pdo->prepare("UPDATE appointments SET statut = ? WHERE id = ?");
    $result = $updateStmt->execute([$status, $appointment_id]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Statut mis à jour avec succès'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour du statut'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
    ]);
}
?> 