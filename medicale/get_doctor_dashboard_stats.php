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
        echo json_encode(['success' => false, 'message' => 'Docteur introuvable']);
        exit;
    }
    $doctor_id = $doctor['id'];

    // Patients suivis (distinct patients avec au moins un rendez-vous confirmé)
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT a.patient_id) as count FROM appointments a WHERE a.doctor_id = ? AND a.statut = 'confirmé'");
    $stmt->execute([$doctor_id]);
    $patients_count = (int)$stmt->fetchColumn();

    // Rendez-vous du jour
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND DATE(date_heure) = CURDATE()");
    $stmt->execute([$doctor_id]);
    $rdv_today = (int)$stmt->fetchColumn();

    // Consultations du mois
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM consultations WHERE doctor_id = ? AND MONTH(date_consultation) = MONTH(CURDATE()) AND YEAR(date_consultation) = YEAR(CURDATE())");
    $stmt->execute([$doctor_id]);
    $consultations_month = (int)$stmt->fetchColumn();

    // Rendez-vous urgents du jour (motif ou statut urgent)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND DATE(date_heure) = CURDATE() AND (motif LIKE '%urgent%' OR statut = 'Urgent')");
    $stmt->execute([$doctor_id]);
    $urgent_today = (int)$stmt->fetchColumn();

    // Messages non lus (fictif)
    $messages_unread = 2;

    echo json_encode([
        'success' => true,
        'patients_count' => $patients_count,
        'rdv_today' => $rdv_today,
        'consultations_month' => $consultations_month,
        'urgent_today' => $urgent_today,
        'messages_unread' => $messages_unread
    ]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
} 