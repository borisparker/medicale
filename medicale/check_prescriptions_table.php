<?php
require_once 'db.php';

echo "<h2>Vérification de la table prescriptions</h2>";

try {
    // 1. Vérifier si la table prescriptions existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'prescriptions'");
    if ($stmt->rowCount() == 0) {
        echo "❌ La table 'prescriptions' n'existe pas. Création de la table...<br>";
        
        // Créer la table prescriptions
        $sql = "CREATE TABLE prescriptions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            consultation_id INT NOT NULL,
            doctor_id INT NOT NULL,
            patient_id INT NOT NULL,
            details TEXT,
            date_prescription DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (consultation_id) REFERENCES consultations(id) ON DELETE CASCADE,
            FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
            FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
        )";
        $pdo->exec($sql);
        echo "✅ Table 'prescriptions' créée<br>";
    } else {
        echo "✅ La table 'prescriptions' existe<br>";
    }
    
    // 2. Vérifier la structure de la table
    echo "<h3>Structure actuelle de la table prescriptions :</h3>";
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
    
    // 3. Vérifier si la colonne date_prescription existe
    $hasDatePrescription = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'date_prescription') {
            $hasDatePrescription = true;
            break;
        }
    }
    
    if (!$hasDatePrescription) {
        echo "❌ La colonne 'date_prescription' n'existe pas. Ajout de la colonne...<br>";
        
        // Ajouter la colonne date_prescription
        $sql = "ALTER TABLE prescriptions ADD COLUMN date_prescription DATE NOT NULL DEFAULT (CURRENT_DATE)";
        $pdo->exec($sql);
        echo "✅ Colonne 'date_prescription' ajoutée<br>";
        
        // Mettre à jour les enregistrements existants
        $sql = "UPDATE prescriptions SET date_prescription = DATE(created_at) WHERE date_prescription IS NULL";
        $pdo->exec($sql);
        echo "✅ Enregistrements existants mis à jour<br>";
    } else {
        echo "✅ La colonne 'date_prescription' existe<br>";
    }
    
    // 4. Vérifier le nombre d'ordonnances
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM prescriptions");
    $count = $stmt->fetch()['count'];
    echo "<h3>Nombre d'ordonnances dans la base : " . $count . "</h3>";
    
    if ($count > 0) {
        echo "<h3>Exemples d'ordonnances :</h3>";
        $stmt = $pdo->query("SELECT * FROM prescriptions LIMIT 5");
        $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Consultation ID</th><th>Doctor ID</th><th>Patient ID</th><th>Date</th><th>Créé le</th></tr>";
        foreach ($prescriptions as $prescription) {
            echo "<tr>";
            echo "<td>" . $prescription['id'] . "</td>";
            echo "<td>" . $prescription['consultation_id'] . "</td>";
            echo "<td>" . $prescription['doctor_id'] . "</td>";
            echo "<td>" . $prescription['patient_id'] . "</td>";
            echo "<td>" . $prescription['date_prescription'] . "</td>";
            echo "<td>" . $prescription['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<br>✅ Vérification terminée !";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?> 