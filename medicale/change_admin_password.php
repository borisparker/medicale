<?php
require_once 'auth.php';
require_role('admin');
require_once 'db.php';

header('Content-Type: application/json');

try {
    // Vérifier que les données sont présentes
    if (!isset($_POST['current_password']) || !isset($_POST['new_password'])) {
        throw new Exception('Tous les champs sont requis.');
    }
    
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    
    // Validation du nouveau mot de passe
    if (strlen($new_password) < 8) {
        throw new Exception('Le nouveau mot de passe doit contenir au moins 8 caractères.');
    }
    
    // Récupérer l'ID de l'admin connecté
    $user_id = $_SESSION['user']['id'];
    
    // Vérifier le mot de passe actuel
    $stmt = $pdo->prepare('SELECT mot_de_passe FROM users WHERE id = ? AND role = "admin"');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        throw new Exception('Utilisateur non trouvé.');
    }
    
    // Vérifier que le mot de passe actuel est correct
    if (!password_verify($current_password, $user['mot_de_passe'])) {
        throw new Exception('Le mot de passe actuel est incorrect.');
    }
    
    // Hasher le nouveau mot de passe
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Mettre à jour le mot de passe
    $stmt = $pdo->prepare('UPDATE users SET mot_de_passe = ? WHERE id = ? AND role = "admin"');
    $result = $stmt->execute([$hashed_password, $user_id]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Mot de passe modifié avec succès.'
        ]);
    } else {
        throw new Exception('Erreur lors de la mise à jour du mot de passe.');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 