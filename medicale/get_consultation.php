<?php
require_once 'auth.php';
require_role('docteur');
require_once 'db.php';
header('Content-Type: application/json');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Consultation invalide.']);
    exit;
}
$id = intval($_GET['id']);
$user_id = $_SESSION['user']['id'];

// Vérifier que la consultation appartient au docteur connecté
$stmt = $pdo->prepare('SELECT c.*, mr.patient_id FROM consultations c JOIN medical_records mr ON c.medical_record_id = mr.id WHERE c.id = ? AND c.doctor_id = (SELECT id FROM doctors WHERE user_id = ?)');
$stmt->execute([$id, $user_id]);
$consultation = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$consultation) {
    echo json_encode(['success' => false, 'message' => 'Consultation introuvable.']);
    exit;
}
echo json_encode(['success' => true, 'consultation' => $consultation]); 