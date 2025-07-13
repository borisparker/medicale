<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$email = $_POST['email'] ?? '';
$motdepasse = $_POST['motdepasse'] ?? '';
$date_naissance = $_POST['date_naissance'] ?? null;
$sexe = $_POST['sexe'] ?? null;
$telephone = $_POST['telephone'] ?? null;
$specialite = $_POST['specialite'] ?? null;
$disponibilite = $_POST['disponibilite'] ?? null;
// Photo non gérée pour l'instant

if(!$nom || !$prenom || !$email || !$motdepasse) {
    echo json_encode(['success' => false, 'message' => 'Champs obligatoires manquants.']);
    exit;
}

$hash = password_hash($motdepasse, PASSWORD_DEFAULT);

try {
    // 1. Insertion dans users
    $stmt = $pdo->prepare('INSERT INTO users (nom, prenom, email, mot_de_passe, role, date_naissance, sexe, telephone, specialite) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$nom, $prenom, $email, $hash, 'docteur', $date_naissance, $sexe, $telephone, $specialite]);
    $user_id = $pdo->lastInsertId();

    // 2. Insertion dans doctors
    $stmt2 = $pdo->prepare('INSERT INTO doctors (user_id, disponibilite) VALUES (?, ?)');
    $stmt2->execute([$user_id, $disponibilite]);

    echo json_encode(['success' => true, 'message' => 'Médecin ajouté avec succès.']);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du médecin : ' . $e->getMessage()]);
} 