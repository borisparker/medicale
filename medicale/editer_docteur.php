<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_POST['doctor_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID manquant']);
    exit;
}

$doctor_id = intval($_POST['doctor_id']);
$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$email = $_POST['email'] ?? '';
$specialite = $_POST['specialite'] ?? '';
$telephone = $_POST['telephone'] ?? '';
$disponibilite = $_POST['disponibilite'] ?? '';
$sexe = $_POST['sexe'] ?? '';
$date_naissance = $_POST['date_naissance'] ?? null;
$motdepasse = $_POST['motdepasse'] ?? '';

try {
    // Récupérer l'user_id lié au docteur
    $stmt = $pdo->prepare('SELECT user_id FROM doctors WHERE id = ?');
    $stmt->execute([$doctor_id]);
    $row = $stmt->fetch();
    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Médecin introuvable']);
        exit;
    }
    $user_id = $row['user_id'];

    // Préparer la requête de mise à jour
    $params = [$nom, $prenom, $email, $specialite, $telephone, $sexe, $date_naissance, $user_id];
    $sql = "UPDATE users SET nom=?, prenom=?, email=?, specialite=?, telephone=?, sexe=?, date_naissance=? WHERE id=?";
    $pdo->prepare($sql)->execute($params);

    // Mot de passe (optionnel)
    if (!empty($motdepasse)) {
        $hash = password_hash($motdepasse, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE users SET mot_de_passe=? WHERE id=?')->execute([$hash, $user_id]);
    }

    // Photo (optionnelle)
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = 'photo_docteur_' . $user_id . '_' . time() . '.' . $ext;
        $dest = 'uploads/' . $filename;
        if (!is_dir('uploads')) mkdir('uploads');
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
            $pdo->prepare('UPDATE users SET photo=? WHERE id=?')->execute([$dest, $user_id]);
        }
    }

    // Disponibilité (table doctors)
    $pdo->prepare('UPDATE doctors SET disponibilite=? WHERE id=?')->execute([$disponibilite, $doctor_id]);

    echo json_encode(['success' => true]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
} 