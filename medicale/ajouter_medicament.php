<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

$nom = $_POST['nom'] ?? '';
$dci = $_POST['dci'] ?? null;
$forme = $_POST['forme'] ?? null;
$dosage = $_POST['dosage'] ?? null;
$stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;
$categorie = $_POST['categorie'] ?? null;

if(!$nom) {
    echo json_encode(['success' => false, 'message' => 'Le nom du médicament est obligatoire.']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO medications (nom, dci, forme, dosage, stock, categorie) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$nom, $dci, $forme, $dosage, $stock, $categorie]);
    echo json_encode(['success' => true, 'message' => 'Médicament ajouté avec succès.']);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du médicament : ' . $e->getMessage()]);
} 