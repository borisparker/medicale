<?php
require_once 'auth.php';
require_role('docteur');
require_once 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['id']) || !is_numeric($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Consultation invalide.']);
    exit;
}
$id = intval($data['id']);
$user_id = $_SESSION['user']['id'];

// Vérifier que la consultation appartient au docteur connecté
$stmt = $pdo->prepare('SELECT id FROM consultations WHERE id = ? AND doctor_id = (SELECT id FROM doctors WHERE user_id = ?)');
$stmt->execute([$id, $user_id]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Consultation introuvable ou accès refusé.']);
    exit;
}
// Mettre à jour
$stmt = $pdo->prepare('UPDATE consultations SET type = ?, motif = ?, observations = ? WHERE id = ?');
$stmt->execute([
    $data['type'] ?? '',
    $data['motif'] ?? '',
    $data['observations'] ?? '',
    $id
]);
echo json_encode(['success' => true, 'message' => 'Consultation modifiée avec succès.']); 