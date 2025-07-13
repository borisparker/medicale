<?php
require_once 'auth.php';
require_role('docteur');
require_once 'db.php';
header('Content-Type: application/json');

try {
    // Vérifier que l'utilisateur est connecté
    if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
        exit;
    }

    $user_id = $_SESSION['user']['id'];
    
    // Récupérer les informations du docteur (sans specialite pour l'instant)
    $stmt = $pdo->prepare('SELECT u.nom, u.prenom, u.email, u.photo 
                          FROM users u 
                          WHERE u.id = ?');
    $stmt->execute([$user_id]);
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$info) {
        echo json_encode(['success' => false, 'message' => 'Profil introuvable. Vérifiez que vous êtes bien enregistré comme docteur.']);
        exit;
    }
    
    // Construire l'URL de la photo
    $info['photo_url'] = !empty($info['photo']) ? '/medicale/uploads/' . basename($info['photo']) : '/medicale/assets/images/default-user.png';
    
    // S'assurer que tous les champs ont une valeur par défaut
    $info['nom'] = $info['nom'] ?? '';
    $info['prenom'] = $info['prenom'] ?? '';
    $info['email'] = $info['email'] ?? '';
    
    echo json_encode(['success' => true] + $info);
    
} catch (PDOException $e) {
    error_log('Erreur PDO dans get_doctor_profile.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données lors du chargement du profil.']);
} catch (Exception $e) {
    error_log('Erreur générale dans get_doctor_profile.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur lors du chargement du profil: ' . $e->getMessage()]);
}
?> 