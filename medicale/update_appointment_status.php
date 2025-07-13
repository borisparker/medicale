<?php
session_start();
require_once 'auth.php';
require_once 'db.php';
header('Content-Type: application/json');

// Vérifier que l'utilisateur est connecté et est un médecin
if (!isset($_SESSION['user']) || !is_array($_SESSION['user']) || $_SESSION['user']['role'] !== 'docteur') {
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

// Vérification de la session
$user = $_SESSION['user'] ?? null;
if (!$user || !is_array($user) || !isset($user['id'])) {
    echo json_encode(['success' => false, 'message' => 'Session invalide.']);
    exit;
}

$user_id = $user['id'];

// Récupérer l'id du docteur
$stmt = $pdo->prepare('SELECT id FROM doctors WHERE user_id = ?');
$stmt->execute([$user_id]);
$doctor = $stmt->fetch();
if (!$doctor) {
    echo json_encode(['success' => false, 'message' => 'Docteur introuvable.']);
    exit;
}
$doctor_id = $doctor['id'];

// Récupérer les données
$data = json_decode(file_get_contents('php://input'), true);
$appointment_id = $data['appointment_id'] ?? null;
$new_status = $data['status'] ?? null;

// Vérification des données
if (!$appointment_id || !$new_status) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes.']);
    exit;
}

// Vérifier que le statut est valide
$valid_statuses = ['confirmé', 'annulé', 'terminé', 'en attente'];
if (!in_array($new_status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Statut invalide.']);
    exit;
}

try {
    // Vérifier que le rendez-vous appartient bien à ce docteur
    $stmt = $pdo->prepare('SELECT id FROM appointments WHERE id = ? AND doctor_id = ?');
    $stmt->execute([$appointment_id, $doctor_id]);
    $appointment = $stmt->fetch();
    
    if (!$appointment) {
        echo json_encode(['success' => false, 'message' => 'Rendez-vous introuvable ou non autorisé.']);
        exit;
    }
    
    // Mettre à jour le statut
    $stmt = $pdo->prepare('UPDATE appointments SET statut = ? WHERE id = ?');
    $stmt->execute([$new_status, $appointment_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Statut du rendez-vous mis à jour avec succès.',
        'new_status' => $new_status
    ]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
} 