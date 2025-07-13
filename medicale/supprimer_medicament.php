<?php
require_once 'auth.php';
require_role('admin');
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID du médicament manquant']);
    exit;
}

try {
    // Vérifier si le médicament existe
    $stmt = $pdo->prepare("SELECT id, nom FROM medications WHERE id = ?");
    $stmt->execute([$id]);
    $medicament = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$medicament) {
        echo json_encode(['success' => false, 'message' => 'Médicament non trouvé']);
        exit;
    }
    
    // Vérifier si le médicament est utilisé dans des prescriptions
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM prescription_medications WHERE medication_id = ?");
    $stmt->execute([$id]);
    $usage = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usage['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'Ce médicament ne peut pas être supprimé car il est utilisé dans des prescriptions']);
        exit;
    }
    
    // Supprimer le médicament
    $stmt = $pdo->prepare("DELETE FROM medications WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Médicament "' . $medicament['nom'] . '" supprimé avec succès'
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression : ' . $e->getMessage()]);
}
?> 