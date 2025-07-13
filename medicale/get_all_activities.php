<?php
require_once 'auth.php';
require_role('admin');
require_once 'db.php';

header('Content-Type: application/json');

try {
    $activities = [];
    
    // 1. Récupérer tous les nouveaux patients
    $stmt = $pdo->prepare("
        SELECT 'patient' as type, u.nom, u.prenom, p.date_creation, 'Nouveau patient enregistré' as action
        FROM patients p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.date_creation DESC 
        LIMIT 50
    ");
    $stmt->execute();
    $newPatients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($newPatients as $patient) {
        $activities[] = [
            'type' => 'patient',
            'action' => $patient['action'],
            'description' => $patient['prenom'] . ' ' . $patient['nom'],
            'time' => $patient['date_creation'],
            'icon' => 'fas fa-user-plus',
            'color' => 'green'
        ];
    }
    
    // 2. Récupérer tous les nouveaux médecins
    $stmt = $pdo->prepare("
        SELECT 'doctor' as type, u.nom, u.prenom, d.date_creation, 'Nouveau médecin ajouté' as action
        FROM doctors d
        JOIN users u ON d.user_id = u.id
        ORDER BY d.date_creation DESC 
        LIMIT 50
    ");
    $stmt->execute();
    $newDoctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($newDoctors as $doctor) {
        $activities[] = [
            'type' => 'doctor',
            'action' => $doctor['action'],
            'description' => 'Dr. ' . $doctor['prenom'] . ' ' . $doctor['nom'],
            'time' => $doctor['date_creation'],
            'icon' => 'fas fa-user-md',
            'color' => 'blue'
        ];
    }
    
    // 3. Récupérer tous les nouveaux rendez-vous
    $stmt = $pdo->prepare("
        SELECT 'appointment' as type, a.date_heure, a.statut, 
               up.nom as patient_nom, up.prenom as patient_prenom, 
               ud.nom as doctor_nom, ud.prenom as doctor_prenom,
               a.date_creation
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN users up ON p.user_id = up.id
        JOIN doctors d ON a.doctor_id = d.id
        JOIN users ud ON d.user_id = ud.id
        ORDER BY a.date_creation DESC 
        LIMIT 50
    ");
    $stmt->execute();
    $newAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($newAppointments as $appointment) {
        $action = 'Nouveau rendez-vous créé';
        if ($appointment['statut'] === 'confirmé') {
            $action = 'Rendez-vous confirmé';
        } elseif ($appointment['statut'] === 'annulé') {
            $action = 'Rendez-vous annulé';
        } elseif ($appointment['statut'] === 'terminé') {
            $action = 'Rendez-vous terminé';
        }
        
        $activities[] = [
            'type' => 'appointment',
            'action' => $action,
            'description' => $appointment['patient_prenom'] . ' ' . $appointment['patient_nom'] . ' - Dr. ' . $appointment['doctor_prenom'] . ' ' . $appointment['doctor_nom'],
            'time' => $appointment['date_creation'],
            'icon' => 'fas fa-calendar-check',
            'color' => 'yellow'
        ];
    }
    
    // 4. Récupérer toutes les nouvelles consultations
    $stmt = $pdo->prepare("
        SELECT 'consultation' as type, c.date_consultation, c.type as consultation_type,
               up.nom as patient_nom, up.prenom as patient_prenom, 
               ud.nom as doctor_nom, ud.prenom as doctor_prenom
        FROM consultations c
        JOIN medical_records mr ON c.medical_record_id = mr.id
        JOIN patients p ON mr.patient_id = p.id
        JOIN users up ON p.user_id = up.id
        JOIN doctors d ON c.doctor_id = d.id
        JOIN users ud ON d.user_id = ud.id
        ORDER BY c.date_consultation DESC 
        LIMIT 50
    ");
    $stmt->execute();
    $newConsultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($newConsultations as $consultation) {
        $activities[] = [
            'type' => 'consultation',
            'action' => 'Nouvelle consultation effectuée',
            'description' => $consultation['patient_prenom'] . ' ' . $consultation['patient_nom'] . ' - Dr. ' . $consultation['doctor_prenom'] . ' ' . $consultation['doctor_nom'],
            'time' => $consultation['date_consultation'],
            'icon' => 'fas fa-stethoscope',
            'color' => 'purple'
        ];
    }
    
    // 5. Récupérer toutes les nouvelles ordonnances
    $stmt = $pdo->prepare("
        SELECT 'prescription' as type, pr.date_prescription, pr.date_creation,
               up.nom as patient_nom, up.prenom as patient_prenom, 
               ud.nom as doctor_nom, ud.prenom as doctor_prenom
        FROM prescriptions pr
        JOIN patients p ON pr.patient_id = p.id
        JOIN users up ON p.user_id = up.id
        JOIN doctors d ON pr.doctor_id = d.id
        JOIN users ud ON d.user_id = ud.id
        ORDER BY pr.date_creation DESC 
        LIMIT 50
    ");
    $stmt->execute();
    $newPrescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($newPrescriptions as $prescription) {
        $activities[] = [
            'type' => 'prescription',
            'action' => 'Ordonnance générée',
            'description' => $prescription['patient_prenom'] . ' ' . $prescription['patient_nom'] . ' - Dr. ' . $prescription['doctor_prenom'] . ' ' . $prescription['doctor_nom'],
            'time' => $prescription['date_creation'],
            'icon' => 'fas fa-pills',
            'color' => 'purple'
        ];
    }
    
    // 6. Récupérer tous les nouveaux dossiers médicaux
    $stmt = $pdo->prepare("
        SELECT 'medical_record' as type, mr.titre, mr.date_creation,
               up.nom as patient_nom, up.prenom as patient_prenom
        FROM medical_records mr
        JOIN patients p ON mr.patient_id = p.id
        JOIN users up ON p.user_id = up.id
        ORDER BY mr.date_creation DESC 
        LIMIT 50
    ");
    $stmt->execute();
    $newMedicalRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($newMedicalRecords as $record) {
        $activities[] = [
            'type' => 'medical_record',
            'action' => 'Nouveau dossier médical créé',
            'description' => $record['patient_prenom'] . ' ' . $record['patient_nom'] . ' - ' . $record['titre'],
            'time' => $record['date_creation'],
            'icon' => 'fas fa-file-medical',
            'color' => 'blue'
        ];
    }
    
    // Trier toutes les activités par date (plus récentes en premier)
    usort($activities, function($a, $b) {
        return strtotime($b['time']) - strtotime($a['time']);
    });
    
    // Formater les dates pour l'affichage
    foreach ($activities as &$activity) {
        $activity['time_ago'] = getTimeAgo($activity['time']);
    }
    
    echo json_encode([
        'success' => true,
        'activities' => $activities
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des activités: ' . $e->getMessage()
    ]);
}

function getTimeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'À l\'instant';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return 'Il y a ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '');
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return 'Il y a ' . $hours . ' heure' . ($hours > 1 ? 's' : '');
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return 'Il y a ' . $days . ' jour' . ($days > 1 ? 's' : '');
    } else {
        $weeks = floor($diff / 604800);
        return 'Il y a ' . $weeks . ' semaine' . ($weeks > 1 ? 's' : '');
    }
}
?> 