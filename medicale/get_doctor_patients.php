<?php
require_once 'auth.php';
require_role('docteur');
require_once 'db.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user']['id'];

try {
    // Récupérer l'id du docteur
    $stmt = $pdo->prepare('SELECT id FROM doctors WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $doctor = $stmt->fetch();
    if (!$doctor) {
        echo json_encode(['success' => false, 'patients' => [], 'message' => 'Docteur introuvable']);
        exit;
    }
    $doctor_id = $doctor['id'];

    // Récupérer les patients ayant au moins un rendez-vous confirmé avec ce docteur
    $sql = "SELECT DISTINCT u.id as user_id, u.nom, u.prenom, u.date_naissance, u.sexe, u.telephone, u.photo, p.id as patient_id, p.groupe_sanguin, p.allergies, p.statut
            FROM appointments a
            INNER JOIN patients p ON a.patient_id = p.id
            INNER JOIN users u ON p.user_id = u.id
            WHERE a.doctor_id = ? AND a.statut = 'confirmé'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$doctor_id]);
    $patients = $stmt->fetchAll();
    echo json_encode(['success' => true, 'patients' => $patients]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération des patients : ' . $e->getMessage()]);
} 