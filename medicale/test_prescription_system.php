<?php
require_once 'auth.php';
require_once 'db.php';

echo "<h2>Test du système d'ordonnances</h2>";

try {
    // 1. Vérifier la session
    echo "<h3>1. Vérification de la session</h3>";
    if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'docteur') {
        echo "✅ Session valide - Docteur ID: " . $_SESSION['user']['id'] . "<br>";
    } else {
        echo "❌ Session invalide - Simulation d'une session docteur<br>";
        $_SESSION['user'] = ['id' => 18, 'role' => 'docteur']; // Utiliser le docteur existant
    }
    
    // 2. Vérifier les tables
    echo "<h3>2. Vérification des tables</h3>";
    
    $tables = ['prescriptions', 'medications', 'prescription_medications'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table' existe<br>";
        } else {
            echo "❌ Table '$table' n'existe pas<br>";
        }
    }
    
    // 3. Vérifier les médicaments
    echo "<h3>3. Médicaments disponibles</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM medications");
    $medicationCount = $stmt->fetch()['count'];
    echo "Nombre de médicaments: $medicationCount<br>";
    
    if ($medicationCount > 0) {
        $stmt = $pdo->query("SELECT * FROM medications LIMIT 3");
        $medications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<ul>";
        foreach ($medications as $medication) {
            echo "<li>{$medication['nom']} {$medication['dosage']} - {$medication['forme']}</li>";
        }
        echo "</ul>";
    }
    
    // 4. Vérifier les consultations
    echo "<h3>4. Consultations disponibles</h3>";
    $doctor_id = 7; // ID du docteur qui a des consultations
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM consultations WHERE doctor_id = ?");
    $stmt->execute([$doctor_id]);
    $consultationCount = $stmt->fetch()['count'];
    echo "Nombre de consultations pour le docteur $doctor_id: $consultationCount<br>";
    
    if ($consultationCount > 0) {
        $stmt = $pdo->prepare("SELECT c.id, c.type, c.motif, CONCAT(u.nom, ' ', u.prenom) as patient_name 
                              FROM consultations c 
                              INNER JOIN medical_records mr ON c.medical_record_id = mr.id 
                              INNER JOIN patients p ON mr.patient_id = p.id 
                              INNER JOIN users u ON p.user_id = u.id 
                              WHERE c.doctor_id = ? 
                              LIMIT 3");
        $stmt->execute([$doctor_id]);
        $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<ul>";
        foreach ($consultations as $consultation) {
            echo "<li>ID: {$consultation['id']} - {$consultation['patient_name']} - {$consultation['type']} - {$consultation['motif']}</li>";
        }
        echo "</ul>";
    }
    
    // 5. Vérifier les ordonnances existantes
    echo "<h3>5. Ordonnances existantes</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM prescriptions");
    $prescriptionCount = $stmt->fetch()['count'];
    echo "Nombre d'ordonnances: $prescriptionCount<br>";
    
    // 6. Test de création d'ordonnance
    echo "<h3>6. Test de création d'ordonnance</h3>";
    if ($consultationCount > 0 && $medicationCount > 0) {
        echo "✅ Conditions réunies pour créer une ordonnance<br>";
        echo "Pour tester :<br>";
        echo "1. Connecte-toi en tant que docteur<br>";
        echo "2. Va sur la page Consultations<br>";
        echo "3. Clique sur 'Ordonnance' pour une consultation<br>";
        echo "4. Sélectionne des médicaments et crée l'ordonnance<br>";
    } else {
        echo "❌ Impossible de créer une ordonnance :<br>";
        if ($consultationCount == 0) echo "- Aucune consultation disponible<br>";
        if ($medicationCount == 0) echo "- Aucun médicament disponible<br>";
    }
    
    // 7. Vérifier les fichiers PHP
    echo "<h3>7. Vérification des fichiers PHP</h3>";
    $files = ['create_prescription.php', 'get_medications.php', 'get_doctor_prescriptions.php'];
    foreach ($files as $file) {
        if (file_exists($file)) {
            echo "✅ Fichier '$file' existe<br>";
        } else {
            echo "❌ Fichier '$file' manquant<br>";
        }
    }
    
    echo "<br>✅ Test terminé !";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?> 