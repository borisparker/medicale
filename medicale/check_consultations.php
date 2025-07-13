<?php
require_once 'db.php';

echo "<h2>Vérification des consultations dans la base de données</h2>";

try {
    // 1. Vérifier la structure de la table consultations
    echo "<h3>1. Structure de la table consultations</h3>";
    $stmt = $pdo->query("DESCRIBE consultations");
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

    // 2. Compter les consultations
    echo "<h3>2. Nombre total de consultations</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM consultations");
    $total = $stmt->fetch()['total'];
    echo "Total consultations: " . $total . "<br><br>";

    // 3. Voir toutes les consultations
    if ($total > 0) {
        echo "<h3>3. Toutes les consultations</h3>";
        $stmt = $pdo->query("SELECT * FROM consultations ORDER BY date_consultation DESC LIMIT 10");
        $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Medical Record ID</th><th>Doctor ID</th><th>Type</th><th>Motif</th><th>Date</th></tr>";
        foreach ($consultations as $consultation) {
            echo "<tr>";
            echo "<td>" . $consultation['id'] . "</td>";
            echo "<td>" . $consultation['medical_record_id'] . "</td>";
            echo "<td>" . $consultation['doctor_id'] . "</td>";
            echo "<td>" . $consultation['type'] . "</td>";
            echo "<td>" . $consultation['motif'] . "</td>";
            echo "<td>" . $consultation['date_consultation'] . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    }

    // 4. Vérifier les docteurs
    echo "<h3>4. Docteurs dans la base</h3>";
    $stmt = $pdo->query("SELECT d.id, d.user_id, u.nom, u.prenom FROM doctors d LEFT JOIN users u ON d.user_id = u.id");
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Doctor ID</th><th>User ID</th><th>Nom</th><th>Prénom</th></tr>";
    foreach ($doctors as $doctor) {
        echo "<tr>";
        echo "<td>" . $doctor['id'] . "</td>";
        echo "<td>" . $doctor['user_id'] . "</td>";
        echo "<td>" . $doctor['nom'] . "</td>";
        echo "<td>" . $doctor['prenom'] . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";

    // 5. Vérifier les dossiers médicaux
    echo "<h3>5. Dossiers médicaux</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM medical_records");
    $total_mr = $stmt->fetch()['total'];
    echo "Total dossiers médicaux: " . $total_mr . "<br><br>";

    // 6. Vérifier les patients
    echo "<h3>6. Patients</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM patients");
    $total_patients = $stmt->fetch()['total'];
    echo "Total patients: " . $total_patients . "<br><br>";

    // 7. Test de la requête complète
    echo "<h3>7. Test de la requête complète</h3>";
    $sql = "SELECT 
                c.id,
                c.type,
                c.motif,
                c.observations,
                c.date_consultation,
                CONCAT(u.nom, ' ', u.prenom) as patient_name,
                u.telephone as patient_phone,
                p.id as patient_id,
                mr.id as medical_record_id
            FROM consultations c
            INNER JOIN medical_records mr ON c.medical_record_id = mr.id
            INNER JOIN patients p ON mr.patient_id = p.id
            INNER JOIN users u ON p.user_id = u.id
            ORDER BY c.date_consultation DESC
            LIMIT 5";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Résultats de la requête complète: " . count($results) . " consultations<br>";
    if (count($results) > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Patient</th><th>Type</th><th>Motif</th><th>Date</th></tr>";
        foreach ($results as $result) {
            echo "<tr>";
            echo "<td>" . $result['id'] . "</td>";
            echo "<td>" . $result['patient_name'] . "</td>";
            echo "<td>" . $result['type'] . "</td>";
            echo "<td>" . $result['motif'] . "</td>";
            echo "<td>" . $result['date_consultation'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage();
}
?> 