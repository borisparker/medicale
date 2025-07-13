<?php
require_once 'auth.php';
require_role('docteur');
require_once 'db.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare('SELECT id FROM doctors WHERE user_id = ?');
$stmt->execute([$user_id]);
$doctor = $stmt->fetch();
if (!$doctor) {
    echo json_encode(['success' => false, 'message' => 'Docteur introuvable']);
    exit;
}
$doctor_id = $doctor['id'];

$start = date('Y-m-01', strtotime('-11 months'));
$sql = "SELECT motif, COUNT(*) as count FROM consultations WHERE doctor_id = ? AND date_consultation >= ? GROUP BY motif ORDER BY count DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$doctor_id, $start]);
$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = [
        'motif' => $row['motif'],
        'count' => (int)$row['count']
    ];
}
echo json_encode(['success' => true, 'data' => $data]); 