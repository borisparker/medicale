<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['patient_id']) || !isset($_SESSION['medecin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non authentifié.']);
    exit;
}

$patient_id = $_SESSION['patient_id'];
$medecin_id = $_SESSION['medecin_id'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=medicale', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('SELECT * FROM messages WHERE 
        (expediteur_id = :patient_id AND destinataire_id = :medecin_id AND type_expediteur = "patient")
        OR (expediteur_id = :medecin_id AND destinataire_id = :patient_id AND type_expediteur = "medecin")
        ORDER BY date_envoi ASC');
    $stmt->execute([
        ':patient_id' => $patient_id,
        ':medecin_id' => $medecin_id
    ]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'messages' => $messages]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
} 