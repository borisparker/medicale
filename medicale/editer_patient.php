<?php
require_once 'db.php';
header('Content-Type: application/json');

if (!isset($_POST['patient_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID manquant']);
    exit;
}

$patient_id = intval($_POST['patient_id']);
$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$email = $_POST['email'] ?? '';
$date_naissance = $_POST['date_naissance'] ?? null;
$sexe = $_POST['sexe'] ?? '';
$telephone = $_POST['telephone'] ?? '';
$groupe_sanguin = $_POST['groupe_sanguin'] ?? '';
$allergies = $_POST['allergies'] ?? '';
$statut = $_POST['statut'] ?? 'actif';
$motdepasse = $_POST['motdepasse'] ?? '';

try {
    // Récupérer l'user_id lié au patient
    $stmt = $pdo->prepare('SELECT user_id FROM patients WHERE id = ?');
    $stmt->execute([$patient_id]);
    $row = $stmt->fetch();
    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Patient introuvable']);
        exit;
    }
    $user_id = $row['user_id'];

    // Préparer la requête de mise à jour
    $params = [$nom, $prenom, $email, $date_naissance, $sexe, $telephone, $user_id];
    $sql = "UPDATE users SET nom=?, prenom=?, email=?, date_naissance=?, sexe=?, telephone=? WHERE id=?";
    $pdo->prepare($sql)->execute($params);

    // Mot de passe (optionnel)
    if (!empty($motdepasse)) {
        $hash = password_hash($motdepasse, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE users SET mot_de_passe=? WHERE id=?')->execute([$hash, $user_id]);
    }

    // Photo (optionnelle)
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = 'photo_patient_' . $user_id . '_' . time() . '.' . $ext;
        $dest = '../uploads/' . $filename;
        if (!is_dir('../uploads')) mkdir('../uploads');
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
            $pdo->prepare('UPDATE users SET photo=? WHERE id=?')->execute(['uploads/' . $filename, $user_id]);
        }
    }

    // Mettre à jour la table patients
    $pdo->prepare('UPDATE patients SET groupe_sanguin=?, allergies=?, statut=? WHERE id=?')->execute([
        $groupe_sanguin, $allergies, $statut, $patient_id
    ]);

    echo json_encode(['success' => true]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
} 