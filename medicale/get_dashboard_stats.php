<?php
require_once 'auth.php';
require_once 'db.php';

header('Content-Type: application/json');

try {
    $user_id = $_SESSION['user']['id'];
    $stmt = $pdo->prepare('SELECT id FROM patients WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $patient = $stmt->fetch();
    if (!$patient) {
        echo json_encode(['success' => false, 'message' => 'Patient introuvable']);
        exit;
    }
    $patient_id = $patient['id'];

    $stmt = $pdo->prepare('SELECT id FROM medical_records WHERE patient_id = ?');
    $stmt->execute([$patient_id]);
    $medical_record = $stmt->fetch();
    if (!$medical_record) {
        echo json_encode(['success' => false, 'message' => 'Aucun dossier médical trouvé']);
        exit;
    }
    $medical_record_id = $medical_record['id'];

    $stats = [];

    // Rendez-vous à venir
    $stmt = $pdo->prepare('SELECT COUNT(*) as count, MIN(date_heure) as next_date FROM appointments WHERE patient_id = ? AND date_heure > NOW() AND statut != "annulé"');
    $stmt->execute([$patient_id]);
    $rdv_data = $stmt->fetch();
    $stats['upcoming_appointments'] = (int)$rdv_data['count'];
    $stats['next_appointment_date'] = $rdv_data['next_date'] ? date('d/m/Y', strtotime($rdv_data['next_date'])) : '-';

    // Consultations
    $stmt = $pdo->prepare('SELECT id, date_consultation FROM consultations WHERE medical_record_id = ? ORDER BY date_consultation DESC');
    $stmt->execute([$medical_record_id]);
    $consultations = $stmt->fetchAll();
    $stats['consultations_count'] = count($consultations);
    $stats['last_consultation'] = isset($consultations[0]) ? date('d/m/Y', strtotime($consultations[0]['date_consultation'])) : 'Aucune';

    // Dernier médecin
    if (isset($consultations[0])) {
        $stmt = $pdo->prepare('SELECT u.nom, u.prenom FROM consultations c INNER JOIN doctors d ON c.doctor_id = d.id INNER JOIN users u ON d.user_id = u.id WHERE c.id = ?');
        $stmt->execute([$consultations[0]['id']]);
        $doc = $stmt->fetch();
        $stats['last_doctor'] = $doc ? $doc['prenom'] . ' ' . $doc['nom'] : '-';
    } else {
        $stats['last_doctor'] = '-';
    }

    // Ordonnances (toutes actives)
    $stmt = $pdo->prepare('SELECT p.id FROM prescriptions p INNER JOIN consultations c ON p.consultation_id = c.id WHERE c.medical_record_id = ?');
    $stmt->execute([$medical_record_id]);
    $prescriptions = $stmt->fetchAll();
    $stats['active_prescriptions'] = count($prescriptions);
    $stats['prescriptions_expiring'] = '-';
    $stats['prescriptions_count'] = count($prescriptions);

    // Messages non lus
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM messages WHERE destinataire_id = ? AND lu = 0');
    $stmt->execute([$user_id]);
    $messages_data = $stmt->fetch();
    $stats['unread_messages'] = (int)$messages_data['count'];

    // Dernier message reçu
    $stmt = $pdo->prepare('SELECT m.date_envoi, u.nom, u.prenom FROM messages m INNER JOIN users u ON m.expediteur_id = u.id WHERE m.destinataire_id = ? ORDER BY m.date_envoi DESC LIMIT 1');
    $stmt->execute([$user_id]);
    $last_message = $stmt->fetch();
    $stats['last_message'] = $last_message ? 'De ' . $last_message['prenom'] . ' ' . $last_message['nom'] . ' le ' . date('d/m/Y', strtotime($last_message['date_envoi'])) : '-';

    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des statistiques : ' . $e->getMessage()
    ]);
} 