<?php
require_once 'auth.php';
require_role('docteur');
require_once 'db.php';
header('Content-Type: application/json');

try {
    $user_id = $_SESSION['user']['id'];
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $password = $_POST['password'] ?? '';
    $photo_url = null;

    // Validation des données
    if (empty($nom) || empty($prenom) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs obligatoires doivent être remplis.']);
        exit;
    }

    // Validation email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Format d\'email invalide.']);
        exit;
    }

    // Vérifier si l'email existe déjà (sauf pour l'utilisateur actuel)
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
    $stmt->execute([$email, $user_id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Cette adresse email est déjà utilisée.']);
        exit;
    }

    // Gestion upload photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['photo'];
        
        // Vérifier la taille (2MB max)
        if ($file['size'] > 2 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Le fichier est trop volumineux. Taille maximum: 2MB']);
            exit;
        }
        
        // Vérifier le type de fichier
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.']);
            exit;
        }
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'doctor_' . $user_id . '_' . time() . '.' . $ext;
        $dest = __DIR__ . '/uploads/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $stmt = $pdo->prepare('UPDATE users SET photo = ? WHERE id = ?');
            $stmt->execute([$filename, $user_id]);
            $photo_url = '/medicale/uploads/' . $filename;
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'upload de la photo.']);
            exit;
        }
    }

    // Mettre à jour les informations de base
    $stmt = $pdo->prepare('UPDATE users SET nom = ?, prenom = ?, email = ? WHERE id = ?');
    $stmt->execute([$nom, $prenom, $email, $user_id]);

    // Mettre à jour le mot de passe si fourni
    if (!empty($password)) {
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 6 caractères.']);
            exit;
        }
        
        // Vérifier l'ancien mot de passe
        $stmt = $pdo->prepare('SELECT mot_de_passe FROM users WHERE id = ?');
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($current_password, $user['mot_de_passe'])) {
            echo json_encode(['success' => false, 'message' => 'Le mot de passe actuel est incorrect.']);
            exit;
        }
        
        // Vérifier que le nouveau mot de passe est différent de l'ancien
        if (password_verify($password, $user['mot_de_passe'])) {
            echo json_encode(['success' => false, 'message' => 'Le nouveau mot de passe doit être différent de l\'actuel.']);
            exit;
        }
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE users SET mot_de_passe = ? WHERE id = ?');
        $stmt->execute([$hashed_password, $user_id]);
    }

    $res = ['success' => true, 'message' => 'Profil mis à jour avec succès.'];
    if ($photo_url) {
        $res['photo_url'] = $photo_url;
    }
    
    echo json_encode($res);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
}
?> 