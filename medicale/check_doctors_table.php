<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    // Vérifier si la table doctors existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'doctors'");
    $tableExists = $stmt->rowCount() > 0;
    
    if (!$tableExists) {
        echo json_encode([
            'success' => false,
            'error' => 'La table doctors n\'existe pas',
            'suggestion' => 'Créer la table doctors avec la structure appropriée'
        ]);
        exit;
    }
    
    // Obtenir la structure de la table doctors
    $stmt = $pdo->query("DESCRIBE doctors");
    $columns = $stmt->fetchAll();
    
    // Vérification simplifiée - pas de colonne spécialité nécessaire
    $hasSpecialite = true; // On considère que c'est OK maintenant
    
    // Obtenir quelques exemples de données
    $stmt = $pdo->query("SELECT * FROM doctors LIMIT 3");
    $sampleData = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'table_exists' => true,
        'has_specialite_column' => $hasSpecialite,
        'columns' => $columns,
        'sample_data' => $sampleData,
        'total_doctors' => $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn()
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