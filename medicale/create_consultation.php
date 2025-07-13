<?php
require_once 'auth.php';
require_once 'db.php';

header('Content-Type: application/json');

// Vérifier que l'utilisateur est connecté et est un médecin
if (!isset($_SESSION['user']) || !is_array($_SESSION['user']) || $_SESSION['user']['role'] !== 'docteur') {
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
        exit;
    }
    
    // Validation des données requises
    $required_fields = ['appointment_id', 'patient_id', 'type', 'motif', 'observations'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            echo json_encode(['success' => false, 'message' => "Le champ '$field' est requis"]);
            exit;
        }
    }
    
    $appointment_id = $input['appointment_id'];
    $patient_id = $input['patient_id'];
    $user_id = $_SESSION['user']['id']; // ID de l'utilisateur connecté
    $type = $input['type'];
    $motif = $input['motif'];
    $observations = $input['observations'];
    $date_consultation = date('Y-m-d');
    
    // Récupérer l'ID du docteur depuis la table doctors
    $stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $doctor = $stmt->fetch();
    
    if (!$doctor) {
        echo json_encode(['success' => false, 'message' => 'Docteur introuvable']);
        exit;
    }
    
    $doctor_id = $doctor['id']; // ID réel du docteur dans la table doctors
    
    // Vérifier que le rendez-vous existe et appartient au médecin
    $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ? AND doctor_id = ? AND statut = 'confirmé'");
    $stmt->execute([$appointment_id, $doctor_id]);
    $appointment = $stmt->fetch();
    
    if (!$appointment) {
        echo json_encode([
            'success' => false, 
            'message' => 'Rendez-vous non trouvé ou non autorisé',
            'debug' => [
                'appointment_id' => $appointment_id,
                'doctor_id' => $doctor_id,
                'user_id' => $user_id
            ]
        ]);
        exit;
    }
    
    // Vérifier si un dossier médical existe pour ce patient, sinon en créer un
    $stmt = $pdo->prepare("SELECT id FROM medical_records WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $medical_record = $stmt->fetch();
    
    if (!$medical_record) {
        // Créer un nouveau dossier médical
        $stmt = $pdo->prepare("INSERT INTO medical_records (patient_id, titre, description, created_by) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $patient_id,
            'Dossier médical - ' . date('d/m/Y'),
            'Dossier médical créé automatiquement',
            $doctor_id
        ]);
        $medical_record_id = $pdo->lastInsertId();
    } else {
        $medical_record_id = $medical_record['id'];
    }
    
    // Créer la consultation
    $stmt = $pdo->prepare("INSERT INTO consultations (medical_record_id, doctor_id, type, motif, observations, date_consultation) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $medical_record_id,
        $doctor_id,
        $type,
        $motif,
        $observations,
        $date_consultation
    ]);
    
    $consultation_id = $pdo->lastInsertId();
    
    // Mettre à jour le statut du rendez-vous
    $stmt = $pdo->prepare("UPDATE appointments SET statut = 'terminé' WHERE id = ?");
    $stmt->execute([$appointment_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Consultation créée avec succès',
        'consultation_id' => $consultation_id,
        'medical_record_id' => $medical_record_id
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la création de la consultation: ' . $e->getMessage()
    ]);
}
?> 