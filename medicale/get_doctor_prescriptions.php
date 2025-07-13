<?php
require_once 'auth.php';
require_once 'db.php';

header('Content-Type: application/json');

// Vérifier que l'utilisateur est connecté et est un médecin
if (!isset($_SESSION['user']) || !is_array($_SESSION['user']) || $_SESSION['user']['role'] !== 'docteur') {
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

try {
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
    
    // Récupérer les ordonnances du médecin avec les informations du patient et les médicaments
    $sql = "SELECT 
                p.id,
                p.details,
                p.date_prescription,
                CONCAT(u.nom, ' ', u.prenom) as patient_name,
                c.type as consultation_type,
                c.motif as consultation_motif,
                GROUP_CONCAT(
                    CONCAT(m.nom, ' ', m.dosage, ' - ', pm.quantite, ' (', pm.instructions, ')')
                    SEPARATOR '; '
                ) as medications
            FROM prescriptions p
            INNER JOIN patients pt ON p.patient_id = pt.id
            INNER JOIN users u ON pt.user_id = u.id
            INNER JOIN consultations c ON p.consultation_id = c.id
            LEFT JOIN prescription_medications pm ON p.id = pm.prescription_id
            LEFT JOIN medications m ON pm.medication_id = m.id
            WHERE p.doctor_id = ?
            GROUP BY p.id
            ORDER BY p.date_prescription DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$real_doctor_id]);
    $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données
    $formattedPrescriptions = array_map(function($prescription) {
        $date = new DateTime($prescription['date_prescription']);
        return [
            'id' => $prescription['id'],
            'details' => $prescription['details'],
            'date_prescription' => $date->format('d/m/Y'),
            'patient_name' => $prescription['patient_name'],
            'consultation_type' => $prescription['consultation_type'],
            'consultation_motif' => $prescription['consultation_motif'],
            'medications' => $prescription['medications'] ?: 'Aucun médicament'
        ];
    }, $prescriptions);
    
    echo json_encode([
        'success' => true,
        'prescriptions' => $formattedPrescriptions,
        'count' => count($formattedPrescriptions)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des ordonnances: ' . $e->getMessage()
    ]);
}
?> 