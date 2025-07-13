<?php
require_once 'db.php';
header('Content-Type: application/json');

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID manquant']);
    exit;
}

$id = intval($_POST['id']);
try {
    // On récupère l'user_id lié au docteur
    $stmt = $pdo->prepare('SELECT user_id FROM doctors WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Médecin introuvable']);
        exit;
    }
    $user_id = $row['user_id'];
    // On supprime l'utilisateur (ce qui supprime aussi le docteur via ON DELETE CASCADE)
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    if ($stmt->execute([$user_id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
    }
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
} 