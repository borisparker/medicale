<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'session_data' => $_SESSION,
    'user_exists' => isset($_SESSION['user']),
    'user_is_array' => is_array($_SESSION['user'] ?? null),
    'user_role' => $_SESSION['user']['role'] ?? 'non défini',
    'user_id' => $_SESSION['user']['id'] ?? 'non défini'
]);
?> 