<?php
require_once 'db.php';

echo "<h2>Correction de la table prescriptions</h2>";

try {
    // 1. Vérifier la structure actuelle
    echo "<h3>1. Structure actuelle de la table prescriptions :</h3>";
    $stmt = $pdo->query("DESCRIBE prescriptions");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";
    
    // 2. Vérifier et ajouter les colonnes manquantes
    $existingColumns = array_column($columns, 'Field');
    
    // Colonnes nécessaires
    $requiredColumns = [
        'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
        'consultation_id' => 'INT NOT NULL',
        'doctor_id' => 'INT NOT NULL',
        'patient_id' => 'INT NOT NULL',
        'details' => 'TEXT',
        'date_prescription' => 'DATE NOT NULL',
        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
    ];
    
    echo "<h3>2. Ajout des colonnes manquantes :</h3>";
    
    foreach ($requiredColumns as $columnName => $columnDefinition) {
        if (!in_array($columnName, $existingColumns)) {
            echo "❌ Colonne '$columnName' manquante. Ajout...<br>";
            
            if ($columnName === 'id') {
                // Ne pas ajouter id s'il existe déjà
                continue;
            }
            
            if ($columnName === 'created_at') {
                $sql = "ALTER TABLE prescriptions ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
            } elseif ($columnName === 'date_prescription') {
                $sql = "ALTER TABLE prescriptions ADD COLUMN date_prescription DATE NOT NULL DEFAULT (CURDATE())";
            } else {
                $sql = "ALTER TABLE prescriptions ADD COLUMN $columnName " . explode(' ', $columnDefinition)[0] . " NOT NULL";
            }
            
            $pdo->exec($sql);
            echo "✅ Colonne '$columnName' ajoutée<br>";
        } else {
            echo "✅ Colonne '$columnName' existe déjà<br>";
        }
    }
    
    // 3. Vérifier et ajouter les clés étrangères
    echo "<h3>3. Vérification des clés étrangères :</h3>";
    
    // Vérifier les contraintes existantes
    $stmt = $pdo->query("SHOW CREATE TABLE prescriptions");
    $createTable = $stmt->fetch()[1];
    
    if (strpos($createTable, 'FOREIGN KEY') === false) {
        echo "❌ Aucune clé étrangère trouvée. Ajout des contraintes...<br>";
        
        // Ajouter les clés étrangères
        $foreignKeys = [
            'consultation_id' => 'consultations(id)',
            'doctor_id' => 'doctors(id)',
            'patient_id' => 'patients(id)'
        ];
        
        foreach ($foreignKeys as $column => $reference) {
            try {
                $sql = "ALTER TABLE prescriptions ADD CONSTRAINT fk_prescriptions_$column FOREIGN KEY ($column) REFERENCES $reference ON DELETE CASCADE";
                $pdo->exec($sql);
                echo "✅ Clé étrangère pour '$column' ajoutée<br>";
            } catch (Exception $e) {
                echo "⚠️ Erreur lors de l'ajout de la clé étrangère pour '$column': " . $e->getMessage() . "<br>";
            }
        }
    } else {
        echo "✅ Clés étrangères existent déjà<br>";
    }
    
    // 4. Mettre à jour les données existantes si nécessaire
    echo "<h3>4. Mise à jour des données :</h3>";
    
    // Vérifier s'il y a des prescriptions sans patient_id
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM prescriptions WHERE patient_id IS NULL OR patient_id = 0");
    $nullPatientCount = $stmt->fetch()['count'];
    
    if ($nullPatientCount > 0) {
        echo "⚠️ $nullPatientCount prescriptions sans patient_id trouvées. Mise à jour...<br>";
        
        // Mettre à jour patient_id en utilisant la consultation
        $sql = "UPDATE prescriptions p 
                INNER JOIN consultations c ON p.consultation_id = c.id 
                INNER JOIN medical_records mr ON c.medical_record_id = mr.id 
                SET p.patient_id = mr.patient_id 
                WHERE p.patient_id IS NULL OR p.patient_id = 0";
        $pdo->exec($sql);
        echo "✅ Patient_id mis à jour pour les prescriptions<br>";
    } else {
        echo "✅ Toutes les prescriptions ont un patient_id valide<br>";
    }
    
    // 5. Vérifier le nombre final d'ordonnances
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM prescriptions");
    $count = $stmt->fetch()['count'];
    echo "<h3>5. Nombre total d'ordonnances : $count</h3>";
    
    if ($count > 0) {
        echo "<h3>Exemples d'ordonnances après correction :</h3>";
        $stmt = $pdo->query("SELECT * FROM prescriptions LIMIT 3");
        $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Consultation ID</th><th>Doctor ID</th><th>Patient ID</th><th>Date</th></tr>";
        foreach ($prescriptions as $prescription) {
            echo "<tr>";
            echo "<td>" . $prescription['id'] . "</td>";
            echo "<td>" . $prescription['consultation_id'] . "</td>";
            echo "<td>" . $prescription['doctor_id'] . "</td>";
            echo "<td>" . $prescription['patient_id'] . "</td>";
            echo "<td>" . $prescription['date_prescription'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<br>✅ Correction terminée avec succès !";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?> 