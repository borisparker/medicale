<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    $sql = "SELECT u.id as id, CONCAT(u.prenom, ' ', u.nom) as nom FROM users u INNER JOIN doctors d ON u.id = d.user_id WHERE u.role = 'docteur' ORDER BY u.nom, u.prenom";
    $stmt = $pdo->query($sql);
    $doctors = $stmt->fetchAll();
    echo json_encode($doctors);
} catch(Exception $e) {
    echo json_encode([]);
} 