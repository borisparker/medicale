<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once 'db.php';

try {
    $sql = "SELECT u.id as user_id, u.nom, u.prenom, u.email, u.date_naissance, u.sexe, u.telephone, u.photo, p.id as patient_id, p.groupe_sanguin, p.allergies, p.statut
            FROM users u
            INNER JOIN patients p ON u.id = p.user_id
            ORDER BY u.nom, u.prenom";
    $stmt = $pdo->query($sql);
    $patients = $stmt->fetchAll();
    echo json_encode(['success' => true, 'patients' => $patients]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération des patients : ' . $e->getMessage()]);
} 