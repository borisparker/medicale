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
$nom = $_POST['nom'] ?? '';
$dci = $_POST['dci'] ?? '';
$forme = $_POST['forme'] ?? '';
$dosage = $_POST['dosage'] ?? '';
$stock = $_POST['stock'] ?? 0;
$categorie = $_POST['categorie'] ?? '';

if (!$id || !$nom) {
    echo json_encode(['success' => false, 'message' => 'ID et nom du médicament sont requis']);
    exit;
}

try {
    // Vérifier si le médicament existe
    $stmt = $pdo->prepare("SELECT id FROM medications WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Médicament non trouvé']);
        exit;
    }
    
    // Mettre à jour le médicament
    $stmt = $pdo->prepare("
        UPDATE medications 
        SET nom = ?, dci = ?, forme = ?, dosage = ?, stock = ?, categorie = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        $nom, $dci, $forme, $dosage, $stock, $categorie, $id
    ]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Médicament mis à jour avec succès'
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()]);
}
?> 