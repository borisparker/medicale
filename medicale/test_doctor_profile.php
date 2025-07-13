<?php
// Fichier de test pour diagnostiquer les problèmes de profil docteur
session_start();
header('Content-Type: application/json');

echo json_encode([
    'session_data' => $_SESSION,
    'user_connected' => isset($_SESSION['user']),
    'user_id' => $_SESSION['user']['id'] ?? 'non défini',
    'user_role' => $_SESSION['user']['role'] ?? 'non défini',
    'timestamp' => date('Y-m-d H:i:s')
]);
?> 