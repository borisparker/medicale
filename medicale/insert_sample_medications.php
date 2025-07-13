<?php
require_once 'db.php';

try {
    // Vérifier si la table medications existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'medications'");
    if ($stmt->rowCount() == 0) {
        echo "❌ La table 'medications' n'existe pas. Création de la table...<br>";
        
        // Créer la table medications
        $sql = "CREATE TABLE medications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(255) NOT NULL,
            dosage VARCHAR(100),
            forme VARCHAR(100),
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);
        echo "✅ Table 'medications' créée<br>";
    }
    
    // Vérifier si la table prescription_medications existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'prescription_medications'");
    if ($stmt->rowCount() == 0) {
        echo "❌ La table 'prescription_medications' n'existe pas. Création de la table...<br>";
        
        // Créer la table prescription_medications
        $sql = "CREATE TABLE prescription_medications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            prescription_id INT NOT NULL,
            medication_id INT NOT NULL,
            quantite VARCHAR(100) NOT NULL,
            instructions TEXT,
            FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE,
            FOREIGN KEY (medication_id) REFERENCES medications(id) ON DELETE CASCADE
        )";
        $pdo->exec($sql);
        echo "✅ Table 'prescription_medications' créée<br>";
    }
    
    // Vérifier si des médicaments existent déjà
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM medications");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        echo "📝 Insertion des médicaments de test...<br>";
        
        // Liste de médicaments de test
        $medications = [
            ['nom' => 'Paracétamol', 'dosage' => '500mg', 'forme' => 'Comprimé', 'description' => 'Antidouleur et antipyrétique'],
            ['nom' => 'Ibuprofène', 'dosage' => '400mg', 'forme' => 'Comprimé', 'description' => 'Anti-inflammatoire non stéroïdien'],
            ['nom' => 'Aspirine', 'dosage' => '100mg', 'forme' => 'Comprimé', 'description' => 'Anticoagulant et antidouleur'],
            ['nom' => 'Amoxicilline', 'dosage' => '1g', 'forme' => 'Comprimé', 'description' => 'Antibiotique à large spectre'],
            ['nom' => 'Oméprazole', 'dosage' => '20mg', 'forme' => 'Gélule', 'description' => 'Protecteur gastrique'],
            ['nom' => 'Vitamine D', 'dosage' => '1000 UI', 'forme' => 'Gouttes', 'description' => 'Complément vitaminique'],
            ['nom' => 'Fer', 'dosage' => '50mg', 'forme' => 'Comprimé', 'description' => 'Complément en fer'],
            ['nom' => 'Calcium', 'dosage' => '500mg', 'forme' => 'Comprimé', 'description' => 'Complément en calcium'],
            ['nom' => 'Magnésium', 'dosage' => '300mg', 'forme' => 'Comprimé', 'description' => 'Complément en magnésium'],
            ['nom' => 'Zinc', 'dosage' => '15mg', 'forme' => 'Comprimé', 'description' => 'Complément en zinc']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO medications (nom, dosage, forme, description) VALUES (?, ?, ?, ?)");
        
        foreach ($medications as $medication) {
            $stmt->execute([
                $medication['nom'],
                $medication['dosage'],
                $medication['forme'],
                $medication['description']
            ]);
        }
        
        echo "✅ " . count($medications) . " médicaments insérés avec succès<br>";
    } else {
        echo "ℹ️ " . $count . " médicaments existent déjà dans la base<br>";
    }
    
    // Afficher les médicaments
    echo "<h3>Médicaments disponibles :</h3>";
    $stmt = $pdo->query("SELECT * FROM medications ORDER BY nom");
    $medications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin-top: 10px;'>";
    echo "<tr><th>ID</th><th>Nom</th><th>Dosage</th><th>Forme</th><th>Description</th></tr>";
    foreach ($medications as $medication) {
        echo "<tr>";
        echo "<td>" . $medication['id'] . "</td>";
        echo "<td>" . $medication['nom'] . "</td>";
        echo "<td>" . $medication['dosage'] . "</td>";
        echo "<td>" . $medication['forme'] . "</td>";
        echo "<td>" . $medication['description'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br>✅ Script terminé avec succès !";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?> 