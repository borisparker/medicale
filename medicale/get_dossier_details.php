<?php
require_once 'auth.php';
require_role('admin');
require_once 'db.php';

header('Content-Type: application/json');

try {
    $medical_record_id = $_GET['medical_record_id'] ?? null;
    
    if (!$medical_record_id) {
        echo json_encode(['success' => false, 'message' => 'ID du dossier médical requis']);
        exit;
    }
    
    // Récupérer les informations du dossier médical et du patient
    $sql = "SELECT 
                mr.id as medical_record_id,
                mr.titre,
                mr.description,
                mr.date_creation,
                p.id as patient_id,
                p.groupe_sanguin,
                p.allergies,
                p.statut as patient_statut,
                u.nom as patient_nom,
                u.prenom as patient_prenom,
                u.date_naissance,
                u.sexe,
                u.telephone,
                u.photo,
                CONCAT(doc.nom, ' ', doc.prenom) as doctor_name,
                doc.specialite as doctor_specialite
            FROM medical_records mr
            INNER JOIN patients p ON mr.patient_id = p.id
            INNER JOIN users u ON p.user_id = u.id
            LEFT JOIN doctors d ON mr.created_by = d.id
            LEFT JOIN users doc ON d.user_id = doc.id
            WHERE mr.id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$medical_record_id]);
    $dossier = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$dossier) {
        echo json_encode(['success' => false, 'message' => 'Dossier médical non trouvé']);
        exit;
    }
    
    // Récupérer les consultations
    $sql = "SELECT 
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
            ORDER BY c.date_consultation DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$medical_record_id]);
    $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les ordonnances
    $sql = "SELECT 
                p.id,
                p.date_creation,
                p.details,
                p.date_prescription,
                CONCAT(u.nom, ' ', u.prenom) as doctor_name,
                c.date_consultation,
                c.type as consultation_type,
                c.motif as consultation_motif
            FROM prescriptions p
            INNER JOIN consultations c ON p.consultation_id = c.id
            INNER JOIN doctors d ON p.doctor_id = d.id
            INNER JOIN users u ON d.user_id = u.id
            WHERE c.medical_record_id = ?
            ORDER BY p.date_creation DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$medical_record_id]);
    $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les médicaments pour chaque ordonnance
    $formattedPrescriptions = array_map(function($prescription) use ($pdo) {
        $sql = "SELECT 
                    m.nom,
                    m.dci,
                    m.forme,
                    m.dosage,
                    pm.quantite,
                    pm.instructions
                FROM prescription_medications pm
                INNER JOIN medications m ON pm.medication_id = m.id
                WHERE pm.prescription_id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$prescription['id']]);
        $medications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'id' => $prescription['id'],
            'date_creation' => $prescription['date_creation'],
            'date_prescription' => $prescription['date_prescription'],
            'details' => $prescription['details'],
            'doctor_name' => $prescription['doctor_name'],
            'consultation_date' => $prescription['date_consultation'],
            'consultation_type' => $prescription['consultation_type'],
            'consultation_motif' => $prescription['consultation_motif'],
            'medications' => $medications
        ];
    }, $prescriptions);
    
    // Formater les consultations
    $formattedConsultations = array_map(function($consultation) {
        return [
            'id' => $consultation['id'],
            'type' => $consultation['type'],
            'motif' => $consultation['motif'],
            'observations' => $consultation['observations'],
            'date_consultation' => $consultation['date_consultation'],
            'doctor_name' => $consultation['doctor_name'],
            'doctor_specialite' => $consultation['doctor_specialite']
        ];
    }, $consultations);
    
    echo json_encode([
        'success' => true,
        'dossier' => [
            'medical_record_id' => $dossier['medical_record_id'],
            'patient_id' => $dossier['patient_id'],
            'patient_info' => [
                'nom' => $dossier['patient_nom'],
                'prenom' => $dossier['patient_prenom'],
                'date_naissance' => $dossier['date_naissance'],
                'sexe' => $dossier['sexe'],
                'telephone' => $dossier['telephone'],
                'photo' => $dossier['photo'],
                'groupe_sanguin' => $dossier['groupe_sanguin'],
                'allergies' => $dossier['allergies'],
                'statut' => $dossier['patient_statut']
            ],
            'dossier_info' => [
                'titre' => $dossier['titre'],
                'description' => $dossier['description'],
                'date_creation' => $dossier['date_creation'],
                'doctor_name' => $dossier['doctor_name'],
                'doctor_specialite' => $dossier['doctor_specialite']
            ]
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