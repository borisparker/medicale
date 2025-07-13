<?php
require_once 'auth.php';
require_role('docteur');
require_once 'db.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user']['id'];
$period = isset($_GET['period']) ? intval($_GET['period']) : 30;
if ($period < 1) $period = 30;

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

    // Fonctions utilitaires
    function getCount($pdo, $sql, $params) {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
    function getSum($pdo, $sql, $params) {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (float)$stmt->fetchColumn();
    }

    // Périodes
    $today = date('Y-m-d');
    $yearStart = date('Y-01-01');
    $weekStart = date('Y-m-d', strtotime('-6 days'));
    $monthStart = date('Y-m-d', strtotime('-29 days'));
    // Détermination de la période personnalisée
    $periodStart = date('Y-m-d', strtotime('-'.($period-1).' days'));

    // Consultations
    $consult_week = getCount($pdo, "SELECT COUNT(*) FROM consultations WHERE doctor_id = ? AND date_consultation >= ?", [$doctor_id, $weekStart]);
    $consult_month = getCount($pdo, "SELECT COUNT(*) FROM consultations WHERE doctor_id = ? AND date_consultation >= ?", [$doctor_id, $monthStart]);
    $consult_year = getCount($pdo, "SELECT COUNT(*) FROM consultations WHERE doctor_id = ? AND date_consultation >= ?", [$doctor_id, $yearStart]);
    // Consultations sur la période choisie
    $consult_period = getCount($pdo, "SELECT COUNT(*) FROM consultations WHERE doctor_id = ? AND date_consultation >= ?", [$doctor_id, $periodStart]);

    // Nouveaux patients (premier rdv dans la période)
    $new_patients_week = getCount($pdo, "SELECT COUNT(DISTINCT patient_id) FROM appointments WHERE doctor_id = ? AND DATE(date_heure) >= ?", [$doctor_id, $weekStart]);
    $new_patients_month = getCount($pdo, "SELECT COUNT(DISTINCT patient_id) FROM appointments WHERE doctor_id = ? AND DATE(date_heure) >= ?", [$doctor_id, $monthStart]);
    $new_patients_year = getCount($pdo, "SELECT COUNT(DISTINCT patient_id) FROM appointments WHERE doctor_id = ? AND DATE(date_heure) >= ?", [$doctor_id, $yearStart]);
    // Nouveaux patients sur la période choisie
    $new_patients_period = getCount($pdo, "SELECT COUNT(DISTINCT patient_id) FROM appointments WHERE doctor_id = ? AND DATE(date_heure) >= ?", [$doctor_id, $periodStart]);

    // Taux d'annulation (rdv annulés / total rdv)
    function cancelRate($pdo, $doctor_id, $start) {
        $total = getCount($pdo, "SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND DATE(date_heure) >= ?", [$doctor_id, $start]);
        $annules = getCount($pdo, "SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND statut = 'annulé' AND DATE(date_heure) >= ?", [$doctor_id, $start]);
        return $total > 0 ? round($annules * 100 / $total, 1) : 0;
    }
    $cancel_week = cancelRate($pdo, $doctor_id, $weekStart);
    $cancel_month = cancelRate($pdo, $doctor_id, $monthStart);
    $cancel_year = cancelRate($pdo, $doctor_id, $yearStart);
    // Taux d'annulation sur la période choisie
    $cancel_period = cancelRate($pdo, $doctor_id, $periodStart);

    // Evolution (variation % semaine/mois/année précédente)
    function evolution($current, $previous) {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 1);
    }
    // Semaine précédente
    $lastWeekStart = date('Y-m-d', strtotime('-13 days'));
    $lastWeekEnd = date('Y-m-d', strtotime('-7 days'));
    $consult_lastweek = getCount($pdo, "SELECT COUNT(*) FROM consultations WHERE doctor_id = ? AND date_consultation >= ? AND date_consultation < ?", [$doctor_id, $lastWeekStart, $weekStart]);
    $evo_consult = evolution($consult_week, $consult_lastweek);
    $new_patients_lastweek = getCount($pdo, "SELECT COUNT(DISTINCT patient_id) FROM appointments WHERE doctor_id = ? AND DATE(date_heure) >= ? AND DATE(date_heure) < ?", [$doctor_id, $lastWeekStart, $weekStart]);
    $evo_new_patients = evolution($new_patients_week, $new_patients_lastweek);
    $cancel_lastweek = cancelRate($pdo, $doctor_id, $lastWeekStart);
    $evo_cancel = evolution($cancel_week, $cancel_lastweek);

    $stats = [
        'consultations' => [
            'week' => $consult_week,
            'month' => $consult_month,
            'year' => $consult_year,
            'period' => $consult_period,
            'evolution' => $evo_consult
        ],
        'new_patients' => [
            'week' => $new_patients_week,
            'month' => $new_patients_month,
            'year' => $new_patients_year,
            'period' => $new_patients_period,
            'evolution' => $evo_new_patients
        ],
        'cancel_rate' => [
            'week' => $cancel_week,
            'month' => $cancel_month,
            'year' => $cancel_year,
            'period' => $cancel_period,
            'evolution' => $evo_cancel
        ]
    ];

    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
} 