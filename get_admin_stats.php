<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'auth.php';
require_role('admin');
require_once 'db.php';

header('Content-Type: application/json');

try {
    // Nombre de consultations
    $stmt = $pdo->query("SELECT COUNT(*) FROM consultations WHERE date_consultation >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $consultations_7 = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM consultations WHERE date_consultation >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $consultations_30 = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM consultations WHERE YEAR(date_consultation) = YEAR(NOW())");
    $consultations_year = $stmt->fetchColumn();

    // Nouveaux patients
    $stmt = $pdo->query("SELECT COUNT(*) FROM patients WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $patients_7 = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM patients WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $patients_30 = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM patients WHERE YEAR(date_creation) = YEAR(NOW())");
    $patients_year = $stmt->fetchColumn();

    // Taux d'annulation
    $stmt = $pdo->query("SELECT COUNT(*) FROM rendezvous WHERE statut = 'annulé' AND date_heure >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $annulations_7 = $stmt->fetchColumn();
    $stmt = $pdo->query("SELECT COUNT(*) FROM rendezvous WHERE date_heure >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $total_rdv_7 = $stmt->fetchColumn();
    $taux_annulation_7 = $total_rdv_7 > 0 ? round($annulations_7 / $total_rdv_7 * 100, 1) : 0;

    $stmt = $pdo->query("SELECT COUNT(*) FROM rendezvous WHERE statut = 'annulé' AND date_heure >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $annulations_30 = $stmt->fetchColumn();
    $stmt = $pdo->query("SELECT COUNT(*) FROM rendezvous WHERE date_heure >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $total_rdv_30 = $stmt->fetchColumn();
    $taux_annulation_30 = $total_rdv_30 > 0 ? round($annulations_30 / $total_rdv_30 * 100, 1) : 0;

    $stmt = $pdo->query("SELECT COUNT(*) FROM rendezvous WHERE statut = 'annulé' AND YEAR(date_heure) = YEAR(NOW())");
    $annulations_year = $stmt->fetchColumn();
    $stmt = $pdo->query("SELECT COUNT(*) FROM rendezvous WHERE YEAR(date_heure) = YEAR(NOW())");
    $total_rdv_year = $stmt->fetchColumn();
    $taux_annulation_year = $total_rdv_year > 0 ? round($annulations_year / $total_rdv_year * 100, 1) : 0;

    // Revenu moyen (exemple, à adapter selon votre structure)
    $stmt = $pdo->query("SELECT AVG(montant) FROM paiements WHERE date_paiement >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $revenu_7 = round($stmt->fetchColumn() ?: 0, 2);

    $stmt = $pdo->query("SELECT AVG(montant) FROM paiements WHERE date_paiement >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $revenu_30 = round($stmt->fetchColumn() ?: 0, 2);

    $stmt = $pdo->query("SELECT AVG(montant) FROM paiements WHERE YEAR(date_paiement) = YEAR(NOW())");
    $revenu_year = round($stmt->fetchColumn() ?: 0, 2);

    echo json_encode([
        'success' => true,
        'stats' => [
            'consultations_7' => $consultations_7,
            'consultations_30' => $consultations_30,
            'consultations_year' => $consultations_year,
            'patients_7' => $patients_7,
            'patients_30' => $patients_30,
            'patients_year' => $patients_year,
            'taux_annulation_7' => $taux_annulation_7,
            'taux_annulation_30' => $taux_annulation_30,
            'taux_annulation_year' => $taux_annulation_year,
            'revenu_7' => $revenu_7,
            'revenu_30' => $revenu_30,
            'revenu_year' => $revenu_year
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 