<?php
require_once 'auth.php';
require_once 'db_connect.php';

// Vérifier que l'utilisateur est connecté et est un patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

$patient_id = $_SESSION['user_id'];

try {
    $notifications = [];
    
    // 1. Rendez-vous dans les 2 prochains jours
    $stmt = $pdo->prepare("
        SELECT rv.date_heure, CONCAT(u.nom, ' ', u.prenom) as doctor_name, rv.motif
        FROM rendez_vous rv
        JOIN utilisateurs u ON rv.medecin_id = u.id
        WHERE rv.patient_id = ? 
        AND rv.date_heure BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 2 DAY)
        AND rv.statut != 'annulé'
        ORDER BY rv.date_heure ASC
        LIMIT 1
    ");
    $stmt->execute([$patient_id]);
    $upcoming_rdv = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($upcoming_rdv) {
        $days_until = ceil((strtotime($upcoming_rdv['date_heure']) - time()) / (24 * 60 * 60));
        $notifications[] = [
            'type' => 'info',
            'message' => "Votre rendez-vous avec Dr. {$upcoming_rdv['doctor_name']} est dans {$days_until} jour(s) - {$upcoming_rdv['motif']}",
            'icon' => 'fa-calendar',
            'date' => date('d/m/Y H:i', strtotime($upcoming_rdv['date_heure']))
        ];
    }
    
    // 2. Ordonnances qui expirent bientôt
    $stmt = $pdo->prepare("
        SELECT o.date_expiration, CONCAT(u.nom, ' ', u.prenom) as doctor_name, COUNT(*) as count
        FROM ordonnances o
        JOIN consultations c ON o.consultation_id = c.id
        JOIN utilisateurs u ON c.medecin_id = u.id
        WHERE c.patient_id = ? 
        AND o.date_expiration BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
        GROUP BY o.date_expiration, u.nom, u.prenom
        ORDER BY o.date_expiration ASC
    ");
    $stmt->execute([$patient_id]);
    $expiring_prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($expiring_prescriptions) {
        $total_expiring = array_sum(array_column($expiring_prescriptions, 'count'));
        $notifications[] = [
            'type' => 'warning',
            'message' => "Vous avez {$total_expiring} ordonnance(s) qui expire(nt) dans les 7 prochains jours",
            'icon' => 'fa-exclamation-triangle',
            'details' => 'Renouvelez vos prescriptions si nécessaire'
        ];
    }
    
    // 3. Messages non lus
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM messages 
        WHERE destinataire_id = ? AND lu = 0
    ");
    $stmt->execute([$patient_id]);
    $unread_messages = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($unread_messages['count'] > 0) {
        $notifications[] = [
            'type' => 'info',
            'message' => "Vous avez {$unread_messages['count']} message(s) non lu(s)",
            'icon' => 'fa-envelope',
            'action' => 'loadMessages()'
        ];
    }
    
    // 4. Rendez-vous annulés récemment
    $stmt = $pdo->prepare("
        SELECT rv.date_heure, CONCAT(u.nom, ' ', u.prenom) as doctor_name
        FROM rendez_vous rv
        JOIN utilisateurs u ON rv.medecin_id = u.id
        WHERE rv.patient_id = ? 
        AND rv.statut = 'annulé'
        AND rv.date_modification > DATE_SUB(NOW(), INTERVAL 7 DAY)
        ORDER BY rv.date_modification DESC
        LIMIT 1
    ");
    $stmt->execute([$patient_id]);
    $cancelled_rdv = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($cancelled_rdv) {
        $notifications[] = [
            'type' => 'warning',
            'message' => "Votre rendez-vous du " . date('d/m/Y', strtotime($cancelled_rdv['date_heure'])) . " avec Dr. {$cancelled_rdv['doctor_name']} a été annulé",
            'icon' => 'fa-calendar-times',
            'action' => 'loadAppointments()'
        ];
    }
    
    // 5. Nouveaux résultats d'analyses (si applicable)
    $stmt = $pdo->prepare("
        SELECT a.date_creation, a.type_analyse
        FROM analyses a
        WHERE a.patient_id = ? 
        AND a.date_creation > DATE_SUB(NOW(), INTERVAL 3 DAY)
        AND a.statut = 'disponible'
        ORDER BY a.date_creation DESC
        LIMIT 1
    ");
    $stmt->execute([$patient_id]);
    $new_analysis = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($new_analysis) {
        $notifications[] = [
            'type' => 'success',
            'message' => "Nouveaux résultats d'analyse disponibles : {$new_analysis['type_analyse']}",
            'icon' => 'fa-flask',
            'date' => date('d/m/Y', strtotime($new_analysis['date_creation']))
        ];
    }
    
    // 6. Rappel de vaccination (si applicable)
    $stmt = $pdo->prepare("
        SELECT v.nom_vaccin, v.date_rappel
        FROM vaccinations v
        WHERE v.patient_id = ? 
        AND v.date_rappel BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)
        AND v.statut = 'programmée'
        ORDER BY v.date_rappel ASC
        LIMIT 1
    ");
    $stmt->execute([$patient_id]);
    $vaccination_reminder = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($vaccination_reminder) {
        $days_until_vaccination = ceil((strtotime($vaccination_reminder['date_rappel']) - time()) / (24 * 60 * 60));
        $notifications[] = [
            'type' => 'info',
            'message' => "Rappel de vaccination : {$vaccination_reminder['nom_vaccin']} dans {$days_until_vaccination} jour(s)",
            'icon' => 'fa-syringe',
            'date' => date('d/m/Y', strtotime($vaccination_reminder['date_rappel']))
        ];
    }
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'count' => count($notifications)
    ]);
    
} catch (PDOException $e) {
    error_log("Erreur get_notifications: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des notifications',
        'notifications' => []
    ]);
}
?> 