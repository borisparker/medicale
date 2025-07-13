<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['patient_id']) || !isset($_SESSION['medecin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non authentifié.']);
    exit;
}

$patient_id = $_SESSION['patient_id'];
$medecin_id = $_SESSION['medecin_id'];

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['contenu']) || trim($data['contenu']) === '') {
    echo json_encode(['success' => false, 'message' => 'Le contenu du message est vide.']);
    exit;
}
$contenu = trim($data['contenu']);

try {
    $pdo = new PDO('mysql:host=localhost;dbname=medicale', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('INSERT INTO messages (expediteur_id, destinataire_id, type_expediteur, contenu, date_envoi, lu) VALUES (?, ?, "patient", ?, NOW(), 0)');
    $stmt->execute([$patient_id, $medecin_id, $contenu]);
    echo json_encode(['success' => true, 'message' => 'Message envoyé.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
} 