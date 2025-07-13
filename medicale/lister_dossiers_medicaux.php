<?php
require_once 'auth.php';
require_role('admin');
require_once 'db.php';

header('Content-Type: application/json');

try {
    // Récupérer tous les dossiers médicaux avec les informations des patients
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
                doc.specialite as doctor_specialite,
                (SELECT COUNT(*) FROM consultations c WHERE c.medical_record_id = mr.id) as consultations_count,
                (SELECT COUNT(*) FROM prescriptions pr 
                 INNER JOIN consultations c ON pr.consultation_id = c.id 
                 WHERE c.medical_record_id = mr.id) as prescriptions_count,
                (SELECT MAX(c.date_consultation) FROM consultations c WHERE c.medical_record_id = mr.id) as derniere_consultation
            FROM medical_records mr
            INNER JOIN patients p ON mr.patient_id = p.id
            INNER JOIN users u ON p.user_id = u.id
            LEFT JOIN doctors d ON mr.created_by = d.id
            LEFT JOIN users doc ON d.user_id = doc.id
            ORDER BY mr.date_creation DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $dossiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données pour l'affichage
    $formattedDossiers = array_map(function($dossier) {
        return [
            'medical_record_id' => $dossier['medical_record_id'],
            'patient_id' => $dossier['patient_id'],
            'patient_name' => $dossier['patient_nom'] . ' ' . $dossier['patient_prenom'],
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
            ],
            'statistiques' => [
                'consultations_count' => $dossier['consultations_count'],
                'prescriptions_count' => $dossier['prescriptions_count'],
                'derniere_consultation' => $dossier['derniere_consultation']
            ]
        ];
    }, $dossiers);
    
    echo json_encode([
        'success' => true,
        'dossiers' => $formattedDossiers,
        'total' => count($formattedDossiers)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des dossiers médicaux: ' . $e->getMessage()
    ]);
}
?> 