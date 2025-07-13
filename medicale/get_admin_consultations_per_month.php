<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'auth.php';
require_role('admin');
require_once 'db.php';
header('Content-Type: application/json');

try {
    $data = [];
    for ($i = 11; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $count = $pdo->prepare("SELECT COUNT(*) FROM consultations WHERE DATE_FORMAT(date_consultation, '%Y-%m') = ?");
        $count->execute([$month]);
        $data[] = [
            'month' => strftime('%b %Y', strtotime($month.'-01')),
            'count' => (int)$count->fetchColumn()
        ];
    }
    echo json_encode(['success' => true, 'data' => $data]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 