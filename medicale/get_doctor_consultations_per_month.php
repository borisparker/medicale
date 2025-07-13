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

$data = [];
for ($i = 11; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $count = $pdo->prepare("SELECT COUNT(*) FROM consultations WHERE doctor_id = ? AND DATE_FORMAT(date_consultation, '%Y-%m') = ?");
    $count->execute([$doctor_id, $month]);
    $data[] = [
        'month' => strftime('%b %Y', strtotime($month.'-01')),
        'count' => (int)$count->fetchColumn()
    ];
}
echo json_encode(['success' => true, 'data' => $data]); 