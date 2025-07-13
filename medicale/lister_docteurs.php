<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once 'db.php';

try {
    $sql = "SELECT u.id as user_id, u.nom, u.prenom, u.email, u.sexe, u.telephone, u.specialite, u.date_naissance, d.id as doctor_id, d.disponibilite, d.nb_patients
            FROM users u
            INNER JOIN doctors d ON u.id = d.user_id
            WHERE u.role = 'docteur'
            ORDER BY u.nom, u.prenom";
    $stmt = $pdo->query($sql);
    $docteurs = $stmt->fetchAll();
    echo json_encode(['success' => true, 'docteurs' => $docteurs]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération des médecins : ' . $e->getMessage()]);
} 