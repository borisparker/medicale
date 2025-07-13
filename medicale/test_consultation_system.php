<?php
require_once 'db.php';

echo "<h1>Test du système de consultations et ordonnances</h1>\n";

try {
    // Test 1: Vérifier les tables
    echo "<h2>1. Vérification des tables</h2>\n";
    
    $tables = ['medical_records', 'consultations', 'prescriptions', 'medications', 'prescription_medications'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table' existe\n";
        } else {
            echo "❌ Table '$table' n'existe pas\n";
        }
    }
    
    // Test 2: Vérifier les médicaments
    echo "<h2>2. Vérification des médicaments</h2>\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM medications");
    $count = $stmt->fetch()['count'];
    echo "Nombre de médicaments dans la base : $count\n";
    
    if ($count == 0) {
        echo "⚠️ Aucun médicament trouvé. Exécutez insert_sample_medications.php pour ajouter des médicaments de test.\n";
    }
    
    // Test 3: Vérifier les patients et médecins
    echo "<h2>3. Vérification des utilisateurs</h2>\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM patients");
    $patientCount = $stmt->fetch()['count'];
    echo "Nombre de patients : $patientCount\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM doctors");
    $doctorCount = $stmt->fetch()['count'];
    echo "Nombre de médecins : $doctorCount\n";
    
    // Test 4: Vérifier les rendez-vous
    echo "<h2>4. Vérification des rendez-vous</h2>\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM appointments");
    $appointmentCount = $stmt->fetch()['count'];
    echo "Nombre de rendez-vous : $appointmentCount\n";
    
    // Test 5: Vérifier les dossiers médicaux
    echo "<h2>5. Vérification des dossiers médicaux</h2>\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM medical_records");
    $medicalRecordCount = $stmt->fetch()['count'];
    echo "Nombre de dossiers médicaux : $medicalRecordCount\n";
    
    // Test 6: Vérifier les consultations
    echo "<h2>6. Vérification des consultations</h2>\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM consultations");
    $consultationCount = $stmt->fetch()['count'];
    echo "Nombre de consultations : $consultationCount\n";
    
    // Test 7: Vérifier les ordonnances
    echo "<h2>7. Vérification des ordonnances</h2>\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM prescriptions");
    $prescriptionCount = $stmt->fetch()['count'];
    echo "Nombre d'ordonnances : $prescriptionCount\n";
    
    // Test 8: Afficher quelques exemples
    echo "<h2>8. Exemples de données</h2>\n";
    
    if ($patientCount > 0) {
        echo "<h3>Patients :</h3>\n";
        $stmt = $pdo->query("SELECT p.id, u.nom, u.prenom FROM patients p INNER JOIN users u ON p.user_id = u.id LIMIT 3");
        while ($row = $stmt->fetch()) {
            echo "- {$row['nom']} {$row['prenom']} (ID: {$row['id']})\n";
        }
    }
    
    if ($doctorCount > 0) {
        echo "<h3>Médecins :</h3>\n";
        $stmt = $pdo->query("SELECT d.id, u.nom, u.prenom, u.specialite FROM doctors d INNER JOIN users u ON d.user_id = u.id LIMIT 3");
        while ($row = $stmt->fetch()) {
            echo "- Dr. {$row['nom']} {$row['prenom']} ({$row['specialite']}) (ID: {$row['id']})\n";
        }
    }
    
    if ($count > 0) {
        echo "<h3>Médicaments disponibles :</h3>\n";
        $stmt = $pdo->query("SELECT nom, dosage, forme FROM medications LIMIT 5");
        while ($row = $stmt->fetch()) {
            echo "- {$row['nom']} {$row['dosage']} ({$row['forme']})\n";
        }
    }
    
    echo "<h2>9. Instructions pour tester le système</h2>\n";
    echo "<ol>\n";
    echo "<li>Connectez-vous en tant que médecin</li>\n";
    echo "<li>Allez dans la section 'Consultations'</li>\n";
    echo "<li>Cliquez sur 'Nouvelle consultation'</li>\n";
    echo "<li>Sélectionnez un rendez-vous confirmé</li>\n";
    echo "<li>Remplissez les détails de la consultation</li>\n";
    echo "<li>Créez la consultation</li>\n";
    echo "<li>Cliquez sur 'Ordonnance' pour créer une ordonnance</li>\n";
    echo "<li>Ajoutez des médicaments à l'ordonnance</li>\n";
    echo "<li>Connectez-vous en tant que patient pour voir le dossier médical</li>\n";
    echo "</ol>\n";
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
?> 