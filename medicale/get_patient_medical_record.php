<?php
require_once 'auth.php';
require_once 'db.php';

header('Content-Type: application/json');

try {
    $patient_id = null;
    
    // Déterminer l'ID du patient selon le rôle
    if ($_SESSION['user']['role'] === 'patient') {
        // Le patient connecté consulte son propre dossier
        $stmt = $pdo->prepare("SELECT id FROM patients WHERE user_id = ?");
        $stmt->execute([$_SESSION['user']['id']]);
        $patient = $stmt->fetch();
        $patient_id = $patient['id'];
    } elseif ($_SESSION['user']['role'] === 'docteur') {
        // Le médecin consulte le dossier d'un patient spécifique
        if (isset($_GET['patient_id'])) {
            $patient_id = $_GET['patient_id'];
        } else {
            echo json_encode(['success' => false, 'message' => 'ID du patient requis']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
        exit;
    }
    
    // Récupérer le dossier médical
    $stmt = $pdo->prepare("SELECT * FROM medical_records WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $medical_record = $stmt->fetch();
    
    if (!$medical_record) {
        echo json_encode(['success' => false, 'message' => 'Aucun dossier médical trouvé']);
        exit;
    }
    
    // Récupérer les consultations
    $stmt = $pdo->prepare("SELECT 
                            c.id,
                            c.type,
                            c.motif,
                            c.observations,
                            c.date_consultation,
                            CONCAT(u.nom, ' ', u.prenom) as doctor_name,
                            u.specialite as doctor_specialite
                          FROM consultations c
                          INNER JOIN doctors d ON c.doctor_id = d.id
                          INNER JOIN users u ON d.user_id = u.id
                          WHERE c.medical_record_id = ?
                          ORDER BY c.date_consultation DESC");
    $stmt->execute([$medical_record['id']]);
    $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les ordonnances
    $stmt = $pdo->prepare("SELECT 
                            p.id,
                            p.date_creation,
                            p.details,
                            CONCAT(u.nom, ' ', u.prenom) as doctor_name,
                            c.date_consultation,
                            c.type as consultation_type
                          FROM prescriptions p
                          INNER JOIN consultations c ON p.consultation_id = c.id
                          INNER JOIN doctors d ON p.doctor_id = d.id
                          INNER JOIN users u ON d.user_id = u.id
                          WHERE c.medical_record_id = ?
                          ORDER BY p.date_creation DESC");
    $stmt->execute([$medical_record['id']]);
    $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les médicaments pour chaque ordonnance
    foreach ($prescriptions as &$prescription) {
        $stmt = $pdo->prepare("SELECT 
                                m.nom,
                                m.dci,
                                m.forme,
                                m.dosage,
                                pm.quantite,
                                pm.instructions
                              FROM prescription_medications pm
                              INNER JOIN medications m ON pm.medication_id = m.id
                              WHERE pm.prescription_id = ?");
        $stmt->execute([$prescription['id']]);
        $prescription['medications'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Formater les données
    $formattedConsultations = array_map(function($consultation) {
        $date = new DateTime($consultation['date_consultation']);
        return [
            'id' => $consultation['id'],
            'type' => $consultation['type'],
            'motif' => $consultation['motif'],
            'observations' => $consultation['observations'],
            'date_consultation' => $date->format('d/m/Y'),
            'doctor_name' => 'Dr. ' . $consultation['doctor_name'],
            'doctor_specialite' => $consultation['doctor_specialite']
        ];
    }, $consultations);
    
    $formattedPrescriptions = array_map(function($prescription) {
        $date = new DateTime($prescription['date_creation']);
        $consultation_date = new DateTime($prescription['date_consultation']);
        return [
            'id' => $prescription['id'],
            'date_creation' => $date->format('d/m/Y'),
            'details' => $prescription['details'],
            'doctor_name' => 'Dr. ' . $prescription['doctor_name'],
            'consultation_date' => $consultation_date->format('d/m/Y'),
            'consultation_type' => $prescription['consultation_type'],
            'medications' => $prescription['medications']
        ];
    }, $prescriptions);
    
    echo json_encode([
        'success' => true,
        'medical_record' => [
            'id' => $medical_record['id'],
            'titre' => $medical_record['titre'],
            'description' => $medical_record['description'],
            'date_creation' => $medical_record['date_creation']
        ],
        'consultations' => $formattedConsultations,
        'prescriptions' => $formattedPrescriptions,
        'consultations_count' => count($formattedConsultations),
        'prescriptions_count' => count($formattedPrescriptions)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération du dossier médical: ' . $e->getMessage()
    ]);
}
?> 