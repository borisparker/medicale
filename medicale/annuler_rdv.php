<?php
require_once 'auth.php';
require_role('patient');
header('Content-Type: application/json');

try {
    require_once 'db.php';
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['id']) || !is_numeric($input['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID de rendez-vous invalide.']);
        exit;
    }
    $rdv_id = intval($input['id']);
    $patient_id = $_SESSION['user']['id'];
    $stmt = $pdo->prepare('SELECT id FROM patients WHERE user_id = ?');
    $stmt->execute([$patient_id]);
    $patient = $stmt->fetch();
    if (!$patient) {
        echo json_encode(['success' => false, 'message' => 'Patient introuvable.']);
        exit;
    }
    $real_patient_id = $patient['id'];
    $stmt = $pdo->prepare('SELECT id, statut FROM appointments WHERE id = ? AND patient_id = ?');
    $stmt->execute([$rdv_id, $real_patient_id]);
    $rdv = $stmt->fetch();
    if (!$rdv) {
        echo json_encode(['success' => false, 'message' => 'Rendez-vous introuvable ou non autorisé.']);
        exit;
    }
    if ($rdv['statut'] === 'annulé') {
        echo json_encode(['success' => false, 'message' => 'Ce rendez-vous est déjà annulé.']);
        exit;
    }
    if ($rdv['statut'] === 'terminé') {
        echo json_encode(['success' => false, 'message' => 'Impossible d\'annuler un rendez-vous terminé.']);
        exit;
    }
    $stmt = $pdo->prepare('UPDATE appointments SET statut = ? WHERE id = ? AND patient_id = ?');
    if ($stmt->execute(['annulé', $rdv_id, $real_patient_id])) {
        echo json_encode(['success' => true, 'message' => 'Rendez-vous annulé avec succès.']);
    } else {
        echo json_encode(['success' => false, 'message' => "Erreur lors de l'annulation."]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur lors de l\'annulation: ' . $e->getMessage()]);
} 