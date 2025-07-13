<?php
require_once 'auth.php';
require_role('admin');
require_once 'db.php';

header('Content-Type: application/json');

// Paramètres de pagination et filtres
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$dateFilter = $_GET['date'] ?? '';
$doctorFilter = $_GET['doctor'] ?? '';
$statusFilter = $_GET['status'] ?? '';

// Construire la requête avec filtres
$where = [];
$params = [];

if ($dateFilter) {
    $where[] = 'DATE(a.date_heure) = ?';
    $params[] = $dateFilter;
}

if ($doctorFilter) {
    $where[] = 'd.id = ?';
    $params[] = $doctorFilter;
}

if ($statusFilter) {
    $where[] = 'a.statut = ?';
    $params[] = $statusFilter;
}

$whereClause = '';
if (!empty($where)) {
    $whereClause = 'WHERE ' . implode(' AND ', $where);
}

try {
    // Requête pour compter le total
    $countSql = "SELECT COUNT(*) as total FROM appointments a 
                 INNER JOIN patients p ON a.patient_id = p.id 
                 INNER JOIN doctors d ON a.doctor_id = d.id 
                 $whereClause";
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalCount = $countStmt->fetch()['total'];
    
    // Requête principale avec pagination
    $sql = "SELECT 
                a.id,
                a.date_heure,
                a.motif,
                a.statut,
                CONCAT(up.nom, ' ', up.prenom) as patient_name,
                up.telephone as patient_phone,
                CONCAT(ud.nom, ' ', ud.prenom) as doctor_name,
                ud.specialite as doctor_specialite
            FROM appointments a
            INNER JOIN patients p ON a.patient_id = p.id
            INNER JOIN users up ON p.user_id = up.id
            INNER JOIN doctors d ON a.doctor_id = d.id
            INNER JOIN users ud ON d.user_id = ud.id
            $whereClause
            ORDER BY a.date_heure DESC
            LIMIT ? OFFSET ?";
    
    $params[] = $per_page;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données
    $formattedAppointments = array_map(function($apt) {
        $date = new DateTime($apt['date_heure']);
        return [
            'id' => $apt['id'],
            'date_heure' => $date->format('d/m/Y H:i'),
            'motif' => $apt['motif'] ?? 'Non spécifié',
            'statut' => $apt['statut'],
            'patient_name' => $apt['patient_name'],
            'patient_phone' => $apt['patient_phone'],
            'doctor_name' => 'Dr. ' . $apt['doctor_name'],
            'doctor_specialite' => $apt['doctor_specialite']
        ];
    }, $appointments);
    
    // Informations de pagination
    $totalPages = ceil($totalCount / $per_page);
    
    $pagination = [
        'current_page' => $page,
        'per_page' => $per_page,
        'total_count' => $totalCount,
        'total_pages' => $totalPages
    ];
    
    echo json_encode([
        'success' => true,
        'appointments' => $formattedAppointments,
        'pagination' => $pagination
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des rendez-vous: ' . $e->getMessage()
    ]);
}
?> 