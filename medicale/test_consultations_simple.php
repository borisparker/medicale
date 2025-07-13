<?php
session_start();
header('Content-Type: application/json');

// Test simple pour vérifier la session
echo json_encode([
    'session_exists' => isset($_SESSION),
    'user_exists' => isset($_SESSION['user']),
    'user_data' => $_SESSION['user'] ?? 'non défini',
    'role' => $_SESSION['user']['role'] ?? 'non défini',
    'user_id' => $_SESSION['user']['id'] ?? 'non défini'
]);
?> 