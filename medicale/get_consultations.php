<?php
require_once 'auth.php';
require_once 'db.php';

header('Content-Type: application/json');

// Debug: Log pour voir si le fichier est appelé
error_log("get_consultations.php appelé");

// Vérifier que l'utilisateur est connecté et est un médecin
if (!isset($_SESSION['user']) || !is_array($_SESSION['user']) || $_SESSION['user']['role'] !== 'docteur') {
    error_log("get_consultations.php - Accès non autorisé: " . json_encode($_SESSION));
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé', 'debug' => $_SESSION]);
    exit;
}

try {
    $doctor_id = $_SESSION['user']['id']; // Utiliser l'ID du médecin connecté
    error_log("get_consultations.php - Docteur ID: " . $doctor_id);
    
    // Récupérer l'ID réel du docteur depuis la table doctors
    $stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
    $stmt->execute([$doctor_id]);
    $doctor = $stmt->fetch();
    
    if (!$doctor) {
        error_log("get_consultations.php - Docteur non trouvé dans la table doctors pour user_id: " . $doctor_id);
        echo json_encode(['success' => false, 'message' => 'Docteur non trouvé', 'debug' => ['user_id' => $doctor_id]]);
        exit;
    }
    
    $real_doctor_id = $doctor['id'];
    error_log("get_consultations.php - ID réel du docteur: " . $real_doctor_id);
    
    // Récupérer les consultations du médecin avec les informations du patient
    $sql = "SELECT 
                c.id,
                c.type,
                c.motif,
                c.observations,
                c.date_consultation,
                CONCAT(u.nom, ' ', u.prenom) as patient_name,
                u.telephone as patient_phone,
                p.id as patient_id,
                mr.id as medical_record_id
            FROM consultations c
            INNER JOIN medical_records mr ON c.medical_record_id = mr.id
            INNER JOIN patients p ON mr.patient_id = p.id
            INNER JOIN users u ON p.user_id = u.id
            WHERE c.doctor_id = ?
            ORDER BY c.date_consultation DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$real_doctor_id]);
    $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("get_consultations.php - Consultations trouvées: " . count($consultations));
    
    // Formater les données
    $formattedConsultations = array_map(function($consultation) {
        $date = new DateTime($consultation['date_consultation']);
        return [
            'id' => $consultation['id'],
            'type' => $consultation['type'],
            'motif' => $consultation['motif'],
            'observations' => $consultation['observations'],
            'date_consultation' => $date->format('d/m/Y'),
            'patient_name' => $consultation['patient_name'],
            'patient_phone' => $consultation['patient_phone'],
            'patient_id' => $consultation['patient_id'],
            'medical_record_id' => $consultation['medical_record_id']
        ];
    }, $consultations);
    
    $response = [
        'success' => true,
        'consultations' => $formattedConsultations,
        'count' => count($formattedConsultations),
        'debug' => [
            'user_id' => $doctor_id,
            'doctor_id' => $real_doctor_id,
            'consultations_count' => count($consultations)
        ]
    ];
    
    error_log("get_consultations.php - Réponse: " . json_encode($response));
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("get_consultations.php - Erreur: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des consultations: ' . $e->getMessage()
    ]);
}
?> 