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
    if (empty($input['consultation_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID de consultation requis']);
        exit;
    }
    
    if (empty($input['medications']) || !is_array($input['medications'])) {
        echo json_encode(['success' => false, 'message' => 'Au moins un médicament est requis']);
        exit;
    }
    
    $consultation_id = $input['consultation_id'];
    $details = $input['details'] ?? '';
    $medications = $input['medications'];
    $doctor_id = $_SESSION['user']['id'];
    
    // Récupérer l'ID réel du docteur depuis la table doctors
    $stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
    $stmt->execute([$doctor_id]);
    $doctor = $stmt->fetch();
    
    if (!$doctor) {
        echo json_encode(['success' => false, 'message' => 'Docteur non trouvé']);
        exit;
    }
    
    $real_doctor_id = $doctor['id'];
    
    // Vérifier que la consultation existe et appartient au médecin
    $stmt = $pdo->prepare("SELECT c.id, c.medical_record_id, p.id as patient_id 
                          FROM consultations c 
                          INNER JOIN medical_records mr ON c.medical_record_id = mr.id 
                          INNER JOIN patients p ON mr.patient_id = p.id 
                          WHERE c.id = ? AND c.doctor_id = ?");
    $stmt->execute([$consultation_id, $real_doctor_id]);
    $consultation = $stmt->fetch();
    
    if (!$consultation) {
        echo json_encode(['success' => false, 'message' => 'Consultation non trouvée ou non autorisée']);
        exit;
    }
    
    // Commencer la transaction
    $pdo->beginTransaction();
    
    try {
        // Créer l'ordonnance
        $stmt = $pdo->prepare("INSERT INTO prescriptions (consultation_id, doctor_id, patient_id, details, date_prescription) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $consultation_id,
            $real_doctor_id,
            $consultation['patient_id'],
            $details,
            date('Y-m-d')
        ]);
        
        $prescription_id = $pdo->lastInsertId();
        
        // Ajouter les médicaments à l'ordonnance
        foreach ($medications as $medication) {
            if (!empty($medication['medication_id']) && !empty($medication['quantite'])) {
                $stmt = $pdo->prepare("INSERT INTO prescription_medications (prescription_id, medication_id, quantite, instructions) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $prescription_id,
                    $medication['medication_id'],
                    $medication['quantite'],
                    $medication['instructions'] ?? ''
                ]);
            }
        }
        
        // Valider la transaction
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Ordonnance créée avec succès',
            'prescription_id' => $prescription_id
        ]);
        
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la création de l\'ordonnance: ' . $e->getMessage()
    ]);
}
?> 