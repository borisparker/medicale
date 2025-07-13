<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT SUM(stock) AS total_stock FROM medications");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_stock = $row['total_stock'] ?? 0;
    echo json_encode(['success' => true, 'total_stock' => (int)$total_stock]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors du calcul du stock']);
} 