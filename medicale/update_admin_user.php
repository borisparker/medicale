<?php
require_once 'auth.php';
require_role('admin');
require_once 'db.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user']['id'];

// Récupération des champs
$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$email = trim($_POST['email'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');

// Validation simple
if (!$nom || !$prenom || !$email) {
    echo json_encode(['success' => false, 'message' => 'Champs obligatoires manquants.']);
    exit;
}

// Gestion de la photo
$photo_path = null;
if (!empty($_FILES['photo']['name'])) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Format de photo non autorisé.']);
        exit;
    }
    if ($_FILES['photo']['size'] > 2*1024*1024) {
        echo json_encode(['success' => false, 'message' => 'Photo trop volumineuse (max 2Mo).']);
        exit;
    }
    $upload_dir = __DIR__ . '/uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    $filename = 'admin_' . $user_id . '_' . time() . '.' . $ext;
    $dest = $upload_dir . $filename;
    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'upload de la photo.']);
        exit;
    }
    $photo_path = 'uploads/' . $filename;
}

// Mise à jour en base
try {
    $sql = "UPDATE users SET nom = ?, prenom = ?, email = ?, telephone = ?";
    $params = [$nom, $prenom, $email, $telephone, $user_id];
    if ($photo_path) {
        $sql .= ", photo = ?";
        array_splice($params, 4, 0, $photo_path); // insère la photo avant l'id
    }
    $sql .= " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['success' => true]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur SQL : ' . $e->getMessage()]);
} 