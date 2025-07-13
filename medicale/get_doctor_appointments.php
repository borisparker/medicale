<?php
require_once 'auth.php';
require_once 'db.php';
header('Content-Type: application/json');

// Vérifier que l'utilisateur est connecté et est un médecin
if (!isset($_SESSION['user']) || !is_array($_SESSION['user']) || $_SESSION['user']['role'] !== 'docteur') {
    echo json_encode([
        'success' => false, 
        'appointments' => [], 
        'message' => 'Accès non autorisé',
        'debug' => 'Session: ' . json_encode($_SESSION)
    ]);
    exit;
}

// Vérification de la session
$user = $_SESSION['user'] ?? null;
if (!$user || !is_array($user) || !isset($user['id'])) {
    echo json_encode([
        'success' => false, 
        'appointments' => [], 
        'message' => 'Session invalide.',
        'debug' => 'Session: ' . json_encode($_SESSION)
    ]);
    exit;
}

$user_id = $user['id'];

// Récupérer l'id du docteur
try {
    $stmt = $pdo->prepare('SELECT id FROM doctors WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $doctor = $stmt->fetch();
    
    if (!$doctor) {
        echo json_encode([
            'success' => false, 
            'appointments' => [], 
            'message' => 'Docteur introuvable pour user_id ' . $user_id,
            'debug' => 'user_id: ' . $user_id
        ]);
        exit;
    }
    
    $doctor_id = $doctor['id'];
    
    // Vérifier si on filtre par statut
    $statut_filter = $_GET['statut'] ?? null;
    $where_clause = 'WHERE a.doctor_id = ?';
    $params = [$doctor_id];
    
    if ($statut_filter) {
        $where_clause .= ' AND a.statut = ?';
        $params[] = $statut_filter;
    }
    
    // Récupérer les rendez-vous du docteur avec LEFT JOIN pour inclure tous les rendez-vous
    $stmt = $pdo->prepare("
        SELECT 
            a.*, 
            p.id as patient_id, 
            u.nom as patient_nom, 
            u.prenom as patient_prenom,
            u.telephone as patient_telephone
        FROM appointments a 
        LEFT JOIN patients p ON a.patient_id = p.id 
        LEFT JOIN users u ON p.user_id = u.id 
        $where_clause
        ORDER BY a.date_heure DESC
    ");
    $stmt->execute($params);
    $rdvs = $stmt->fetchAll();
    
    $appointments = [];
    foreach ($rdvs as $rdv) {
        // Formatage date/heure
        $dt = new DateTime($rdv['date_heure']);
        $date = $dt->format('d/m/Y');
        $heure = $dt->format('H:i');
        
        // Gestion du statut
        $statut = $rdv['statut'] ?? 'en attente';
        $statut_class = 'bg-yellow-100 text-yellow-800';
        
        if ($statut === 'confirmé') {
            $statut_class = 'bg-green-100 text-green-800';
        } elseif ($statut === 'annulé') {
            $statut_class = 'bg-red-100 text-red-800';
        } elseif ($statut === 'terminé') {
            $statut_class = 'bg-gray-100 text-gray-800';
        }
        
        // Nom du patient
        $nom_patient = ($rdv['patient_prenom'] ?? '') . ' ' . ($rdv['patient_nom'] ?? '');
        $nom_patient = trim($nom_patient);
        if (empty($nom_patient)) {
            $nom_patient = 'Patient inconnu';
        }
        
        $appointments[] = [
            'id' => $rdv['id'],
            'date' => $date,
            'heure' => $heure,
            'patient' => $nom_patient,
            'patient_telephone' => $rdv['patient_telephone'] ?? '',
            'statut' => ucfirst($statut),
            'statut_class' => $statut_class,
            'motif' => $rdv['motif'] ?? '',
            'commentaire' => $rdv['commentaire'] ?? '',
            'date_heure_original' => $rdv['date_heure'],
            'patient_id' => $rdv['patient_id'],
            'patient_name' => $nom_patient
        ];
    }
    
    echo json_encode([
        'success' => true, 
        'appointments' => $appointments, 
        'doctor_id' => $doctor_id,
        'count' => count($appointments),
        'filtered_by' => $statut_filter
    ]);
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false, 
        'appointments' => [], 
        'message' => 'Erreur lors de la récupération des rendez-vous: ' . $e->getMessage(),
        'debug' => 'Exception: ' . $e->getMessage()
    ]);
} 