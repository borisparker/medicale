<?php
session_start();
require_once 'db.php';
header('Content-Type: application/json');

// Sécuriser la récupération de l'id utilisateur
if (!isset($_SESSION['user']) || !is_array($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Session invalide ou utilisateur non connecté.']);
    exit;
}
$user_id = $_SESSION['user']['id'];

// Récupérer l'id du patient
$stmt = $pdo->prepare('SELECT id FROM patients WHERE user_id = ?');
$stmt->execute([$user_id]);
$patient = $stmt->fetch();
if (!$patient) {
    echo json_encode(['success' => false, 'message' => 'Patient introuvable.']);
    exit;
}
$patient_id = $patient['id'];

// Récupérer les données du formulaire
$data = json_decode(file_get_contents('php://input'), true);
$doctor_id = $data['doctor_id'] ?? null;
$date_heure = $data['date_heure'] ?? null;
$motif = $data['motif'] ?? '';
$commentaire = $data['commentaire'] ?? '';

// Vérification des champs obligatoires
if (!$doctor_id || !$date_heure || !$motif) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs obligatoires doivent être remplis.']);
    exit;
}

// Vérification du format de la date/heure
if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/', $date_heure) && !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}/', $date_heure)) {
    echo json_encode(['success' => false, 'message' => 'Format de date/heure invalide.']);
    exit;
}

// Conversion du format HTML5 ("2023-06-15T14:00") en format MySQL ("2023-06-15 14:00:00")
$date_heure = str_replace('T', ' ', $date_heure);
if (strlen($date_heure) == 16) {
    $date_heure .= ':00';
}

// Vérifier que la date n'est pas dans le passé
$now = new DateTime();
$appointment_date = new DateTime($date_heure);
if ($appointment_date < $now) {
    echo json_encode(['success' => false, 'message' => 'La date du rendez-vous ne peut pas être dans le passé.']);
    exit;
}

// Vérifier que le docteur existe (correction ici)
// Le doctor_id envoyé correspond à l'ID de l'utilisateur, pas à l'ID dans la table doctors
$stmt = $pdo->prepare('SELECT d.id FROM doctors d WHERE d.user_id = ?');
$stmt->execute([$doctor_id]);
$doctor = $stmt->fetch();
if (!$doctor) {
    echo json_encode(['success' => false, 'message' => 'Médecin introuvable.']);
    exit;
}
$real_doctor_id = $doctor['id']; // L'ID réel dans la table doctors

try {
    // Insérer le rendez-vous avec l'ID correct du docteur
    $stmt = $pdo->prepare('INSERT INTO appointments (patient_id, doctor_id, date_heure, motif, commentaire, statut) VALUES (?, ?, ?, ?, ?, "en attente")');
    $stmt->execute([$patient_id, $real_doctor_id, $date_heure, $motif, $commentaire]);
    
    $appointment_id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Rendez-vous enregistré avec succès.',
        'appointment_id' => $appointment_id,
        'statut' => 'en attente',
        'date_heure' => $date_heure
    ]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement : ' . $e->getMessage()]);
} 