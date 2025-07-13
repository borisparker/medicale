<?php
require_once 'auth.php';
require_role('admin');
require_once 'db.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID du médicament manquant']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM medications WHERE id = ?");
    $stmt->execute([$id]);
    $medicament = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$medicament) {
        echo json_encode(['success' => false, 'message' => 'Médicament non trouvé']);
        exit;
    }
    
    echo json_encode([
        'success' => true, 
        'medicament' => $medicament
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération : ' . $e->getMessage()]);
}
?> 