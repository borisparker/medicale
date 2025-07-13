<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once 'db.php';

// Vérification de la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

// Récupération des données POST
$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$email = $_POST['email'] ?? '';
$motdepasse = $_POST['motdepasse'] ?? '';
$date_naissance = $_POST['date_naissance'] ?? null;
$sexe = $_POST['sexe'] ?? null;
$telephone = $_POST['telephone'] ?? null;
$groupe_sanguin = $_POST['groupe_sanguin'] ?? null;
$allergies = $_POST['allergies'] ?? null;
$statut = $_POST['statut'] ?? 'actif';
// Pour la photo, on gère l'upload plus tard

// Validation simple
if(!$nom || !$prenom || !$email || !$motdepasse) {
    echo json_encode(['success' => false, 'message' => 'Champs obligatoires manquants.']);
    exit;
}

// Hash du mot de passe
$hash = password_hash($motdepasse, PASSWORD_DEFAULT);

// Gestion de la photo
$photo_path = null;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $filename = 'photo_patient_' . time() . '_' . rand(1000,9999) . '.' . $ext;
    $dest = '../uploads/' . $filename;
    if (!is_dir('../uploads')) mkdir('../uploads');
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
        $photo_path = 'uploads/' . $filename;
    }
}

try {
    // 1. Insertion dans users
    $stmt = $pdo->prepare('INSERT INTO users (nom, prenom, email, mot_de_passe, role, date_naissance, sexe, telephone, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$nom, $prenom, $email, $hash, 'patient', $date_naissance, $sexe, $telephone, $photo_path]);
    $user_id = $pdo->lastInsertId();

    // 2. Insertion dans patients
    $stmt2 = $pdo->prepare('INSERT INTO patients (user_id, groupe_sanguin, allergies, statut) VALUES (?, ?, ?, ?)');
    $stmt2->execute([$user_id, $groupe_sanguin, $allergies, $statut]);

    echo json_encode(['success' => true, 'message' => 'Patient ajouté avec succès.']);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du patient : ' . $e->getMessage()]);
} 