<?php
// Fichier de diagnostic pour le profil docteur
session_start();
header('Content-Type: application/json');

$debug = [];

try {
    // 1. Vérifier la session
    $debug['session'] = [
        'exists' => isset($_SESSION),
        'user_exists' => isset($_SESSION['user']),
        'user_data' => $_SESSION['user'] ?? 'non défini'
    ];

    // 2. Vérifier la connexion à la base de données
    require_once 'db.php';
    $debug['database'] = [
        'connection' => 'OK',
        'pdo_object' => isset($pdo) ? 'PDO créé' : 'PDO non créé'
    ];

    // 3. Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
        $debug['error'] = 'Utilisateur non connecté';
        echo json_encode($debug);
        exit;
    }

    $user_id = $_SESSION['user']['id'];
    $debug['user_id'] = $user_id;

    // 4. Vérifier si l'utilisateur existe dans la table users
    $stmt = $pdo->prepare('SELECT id, nom, prenom, email, role FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        $debug['error'] = 'Utilisateur introuvable dans la table users';
        echo json_encode($debug);
        exit;
    }

    $debug['user_info'] = $user;

    // 5. Vérifier si l'utilisateur est un docteur
    if ($user['role'] !== 'docteur') {
        $debug['error'] = 'Utilisateur n\'est pas un docteur (rôle: ' . $user['role'] . ')';
        echo json_encode($debug);
        exit;
    }

    // 6. Vérifier si l'utilisateur existe dans la table doctors
    $stmt = $pdo->prepare('SELECT id FROM doctors WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $doctor = $stmt->fetch();

    if (!$doctor) {
        $debug['error'] = 'Utilisateur introuvable dans la table doctors';
        echo json_encode($debug);
        exit;
    }

    $debug['doctor_info'] = $doctor;

    // 7. Récupérer les informations complètes
    $stmt = $pdo->prepare('SELECT u.nom, u.prenom, u.email, u.photo 
                          FROM users u 
                          WHERE u.id = ?');
    $stmt->execute([$user_id]);
    $info = $stmt->fetch();

    if (!$info) {
        $debug['error'] = 'Impossible de récupérer les informations complètes';
        echo json_encode($debug);
        exit;
    }

    $debug['complete_info'] = $info;
    $debug['success'] = true;
    $debug['message'] = 'Toutes les vérifications sont passées avec succès';

} catch (PDOException $e) {
    $debug['error'] = 'Erreur PDO: ' . $e->getMessage();
    $debug['error_code'] = $e->getCode();
} catch (Exception $e) {
    $debug['error'] = 'Erreur générale: ' . $e->getMessage();
}

echo json_encode($debug);
?> 