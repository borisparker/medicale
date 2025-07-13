<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once 'db.php';

// Récupération des filtres
$recherche = $_GET['recherche'] ?? $_POST['recherche'] ?? '';
$categorie = $_GET['categorie'] ?? $_POST['categorie'] ?? '';

$where = [];
$params = [];
if($recherche) {
    $where[] = '(nom LIKE ? OR dci LIKE ?)';
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
}
if($categorie && strtolower($categorie) !== 'toutes catégories') {
    $where[] = 'categorie = ?';
    $params[] = $categorie;
}

$sql = "SELECT * FROM medications";
if(count($where) > 0) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY nom";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $medicaments = $stmt->fetchAll();
    echo json_encode(['success' => true, 'medicaments' => $medicaments]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération des médicaments : ' . $e->getMessage()]);
} 