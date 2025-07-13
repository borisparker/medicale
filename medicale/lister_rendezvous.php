<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once 'db.php';

// Filtres éventuels (date, médecin, statut, month pour le calendrier)
$date = $_GET['date'] ?? $_POST['date'] ?? '';
$medecin = $_GET['medecin'] ?? $_POST['medecin'] ?? '';
$statut = $_GET['statut'] ?? $_POST['statut'] ?? '';
$month = $_GET['month'] ?? $_POST['month'] ?? ''; // Nouveau paramètre pour le calendrier

$where = [];
$params = [];

if($date) {
    $where[] = 'DATE(a.date_heure) = ?';
    $params[] = $date;
}

if($month) {
    // Filtrage par mois (format: YYYY-MM)
    $where[] = 'DATE_FORMAT(a.date_heure, "%Y-%m") = ?';
    $params[] = $month;
}

if($medecin) {
    $where[] = 'd.user_id = ?';
    $params[] = $medecin;
}

if($statut && strtolower($statut) !== 'tous les statuts') {
    $where[] = 'a.statut = ?';
    $params[] = $statut;
}

$sql = "SELECT a.id, a.date_heure, a.motif, a.statut,
        p.id as patient_id, up.nom as patient_nom, up.prenom as patient_prenom, up.sexe as patient_sexe, up.date_naissance as patient_date_naissance,
        d.id as doctor_id, ud.nom as doctor_nom, ud.prenom as doctor_prenom, ud.specialite as doctor_specialite
        FROM appointments a
        INNER JOIN patients p ON a.patient_id = p.id
        INNER JOIN users up ON p.user_id = up.id
        INNER JOIN doctors d ON a.doctor_id = d.id
        INNER JOIN users ud ON d.user_id = ud.id";

if(count($where) > 0) {
    $sql .= " WHERE " . implode(' AND ', $where);
}

// Si c'est pour le calendrier (filtrage par mois), on ne limite pas et on trie par date
if($month) {
    $sql .= " ORDER BY a.date_heure ASC";
} else {
    $sql .= " ORDER BY a.date_heure DESC";
}

// Ajout pour le dashboard : statistiques
$stats = $_GET['stats'] ?? $_POST['stats'] ?? '';
if($stats) {
    try {
        // Statistiques pour le dashboard
        $statsData = [];
        
        // Rendez-vous aujourd'hui
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM appointments WHERE DATE(date_heure) = CURDATE()");
        $stmt->execute();
        $statsData['rdv_aujourdhui'] = $stmt->fetch()['count'];
        
        // Nouveaux patients cette semaine
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM patients p 
                              INNER JOIN users u ON p.user_id = u.id 
                              WHERE p.date_creation >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stmt->execute();
        $statsData['nouveaux_patients'] = $stmt->fetch()['count'];
        
        // Consultations en attente
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM appointments WHERE statut = 'en attente'");
        $stmt->execute();
        $statsData['consultations_attente'] = $stmt->fetch()['count'];
        
        // Total patients
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM patients");
        $stmt->execute();
        $statsData['total_patients'] = $stmt->fetch()['count'];
        
        // Total docteurs
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM doctors");
        $stmt->execute();
        $statsData['total_docteurs'] = $stmt->fetch()['count'];
        
        echo json_encode(['success' => true, 'stats' => $statsData]);
        exit;
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors du calcul des statistiques: ' . $e->getMessage()]);
        exit;
    }
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rendezvous = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'rendezvous' => $rendezvous]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération des rendez-vous: ' . $e->getMessage()]);
} 