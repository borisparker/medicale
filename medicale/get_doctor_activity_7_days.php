<?php
require_once 'auth.php';
require_role('docteur');
require_once 'db.php';

header('Content-Type: application/json');

try {
    $user_id = $_SESSION['user']['id'];
    
    // Récupérer l'ID du docteur
    $stmt = $pdo->prepare('SELECT id FROM doctors WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$doctor) {
        throw new Exception('Docteur non trouvé');
    }
    
    $doctor_id = $doctor['id'];
    
    // Générer les 7 derniers jours
    $days = [];
    $activities = [];
    
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $day_name = date('D', strtotime($date)); // Lun, Mar, etc.
        
        $days[] = $day_name;
        
        // Compter les consultations pour ce jour
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM consultations 
            WHERE doctor_id = ? 
            AND DATE(date_consultation) = ?
        ");
        $stmt->execute([$doctor_id, $date]);
        $consultations = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Compter les rendez-vous pour ce jour
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM appointments 
            WHERE doctor_id = ? 
            AND DATE(date_heure) = ?
        ");
        $stmt->execute([$doctor_id, $date]);
        $appointments = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Compter les ordonnances pour ce jour
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM prescriptions 
            WHERE doctor_id = ? 
            AND DATE(date_prescription) = ?
        ");
        $stmt->execute([$doctor_id, $date]);
        $prescriptions = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Total d'activité pour ce jour (consultations + rendez-vous + ordonnances)
        $total_activity = $consultations['count'] + $appointments['count'] + $prescriptions['count'];
        $activities[] = $total_activity;
    }
    
    // Calculer le pourcentage de hauteur pour chaque barre
    $max_activity = max($activities);
    $heights = [];
    
    foreach ($activities as $activity) {
        if ($max_activity > 0) {
            $heights[] = round(($activity / $max_activity) * 100);
        } else {
            $heights[] = 0;
        }
    }
    
    echo json_encode([
        'success' => true,
        'days' => $days,
        'activities' => $activities,
        'heights' => $heights,
        'max_activity' => $max_activity
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des données d\'activité: ' . $e->getMessage()
    ]);
}
?> 