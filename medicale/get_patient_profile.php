<?php
session_start();
header('Content-Type: application/json');

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non authentifié.']);
    exit;
}

// Connexion à la base de données (adapte ce chemin et ces variables à ta config)
$host = 'localhost';
$db   = 'medicale'; // nom de ta base
$user = 'root';     // utilisateur MySQL
$pass = '';         // mot de passe MySQL
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur connexion BDD.']);
    exit;
}

// Récupère les infos du patient
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT prenom, nom, email, telephone, photo FROM patients WHERE id = ?");
$stmt->execute([$user_id]);
$patient = $stmt->fetch();

if ($patient) {
    echo json_encode([
        'success' => true,
        'prenom' => $patient['prenom'],
        'nom' => $patient['nom'],
        'email' => $patient['email'],
        'telephone' => $patient['telephone'],
        'photo' => $patient['photo'] // Mets ici le chemin complet si besoin
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Patient introuvable.']);
}