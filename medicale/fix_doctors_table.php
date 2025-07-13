<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    echo json_encode([
        'success' => true,
        'message' => 'Aucune correction nécessaire - spécialité retirée du système',
        'action' => 'no_correction_needed'
    ]);
    
    // Vérifier et ajouter d'autres colonnes manquantes si nécessaire
    $requiredColumns = [
        'disponibilite' => "VARCHAR(255) DEFAULT 'Disponible'",
        'nb_patients' => "INT DEFAULT 0",
        'date_creation' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
    ];
    
    $addedColumns = [];
    foreach ($requiredColumns as $column => $definition) {
        $stmt = $pdo->query("SHOW COLUMNS FROM doctors LIKE '$column'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE doctors ADD COLUMN $column $definition");
            $addedColumns[] = $column;
        }
    }
    
    if (!empty($addedColumns)) {
        echo json_encode([
            'success' => true,
            'message' => 'Colonnes ajoutées: ' . implode(', ', $addedColumns),
            'added_columns' => $addedColumns
        ]);
    }
    
    // Afficher la structure finale
    $stmt = $pdo->query("DESCRIBE doctors");
    $finalStructure = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'message' => 'Structure de la table doctors corrigée',
        'final_structure' => $finalStructure
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erreur PDO: ' . $e->getMessage(),
        'error_code' => $e->getCode()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erreur générale: ' . $e->getMessage()
    ]);
}
?> 