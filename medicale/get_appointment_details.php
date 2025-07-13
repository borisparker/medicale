<?php
require_once 'auth.php';
require_once 'db.php';

// Vérifier que l'utilisateur est connecté et est un patient
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'patient') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

// Vérifier que l'ID du rendez-vous est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de rendez-vous invalide']);
    exit;
}

$rdv_id = intval($_GET['id']);
$user_id = $_SESSION['user']['id'];

try {
    // Récupérer l'ID du patient à partir de l'user_id
    $stmt = $pdo->prepare('SELECT id FROM patients WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $patient = $stmt->fetch();
    
    if (!$patient) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Patient introuvable']);
        exit;
    }
    
    $patient_id = $patient['id'];
    
    // Récupérer les détails du rendez-vous avec les informations du médecin
    $stmt = $pdo->prepare("
        SELECT 
            a.id,
            a.date_heure,
            a.motif,
            a.commentaire,
            a.statut,
            a.date_creation,
            CONCAT(u.nom, ' ', u.prenom) as medecin,
            u.specialite as doctor_specialite,
            u.telephone as doctor_telephone
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        JOIN users u ON d.user_id = u.id
        WHERE a.id = ? AND a.patient_id = ?
    ");
    
    $stmt->execute([$rdv_id, $patient_id]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$appointment) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Rendez-vous non trouvé']);
        exit;
    }
    
    // Formater les dates
    $date_heure = new DateTime($appointment['date_heure']);
    $appointment['date'] = $date_heure->format('d/m/Y');
    $appointment['heure'] = $date_heure->format('H:i');
    
    if ($appointment['date_creation']) {
        $date_creation = new DateTime($appointment['date_creation']);
        $appointment['date_creation'] = $date_creation->format('d/m/Y à H:i');
    }
    
    // Traduire le statut
    $statuts = [
        'en attente' => 'En attente',
        'confirmé' => 'À venir',
        'terminé' => 'Terminé',
        'annulé' => 'Annulé'
    ];
    
    $appointment['statut'] = $statuts[$appointment['statut']] ?? $appointment['statut'];
    
    echo json_encode([
        'success' => true,
        'appointment' => $appointment
    ]);
    
} catch (PDOException $e) {
    error_log("Erreur base de données: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération des détails: ' . $e->getMessage()]);
}
?> 