<?php
require_once 'auth.php';
require_once 'db.php';

// Test de la page dossiers médicaux
echo "<h1>Test des dossiers médicaux</h1>";

try {
    // 1. Vérifier les tables
    echo "<h2>1. Vérification des tables</h2>";
    
    $tables = ['medical_records', 'consultations', 'prescriptions', 'patients', 'doctors', 'users'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table' existe<br>";
        } else {
            echo "❌ Table '$table' n'existe pas<br>";
        }
    }
    
    // 2. Vérifier les dossiers médicaux
    echo "<h2>2. Dossiers médicaux existants</h2>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM medical_records");
    $count = $stmt->fetch()['count'];
    echo "Nombre de dossiers médicaux : $count<br>";
    
    if ($count > 0) {
        $stmt = $pdo->query("SELECT * FROM medical_records LIMIT 5");
        $dossiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Patient ID</th><th>Titre</th><th>Date création</th></tr>";
        foreach ($dossiers as $dossier) {
            echo "<tr>";
            echo "<td>" . $dossier['id'] . "</td>";
            echo "<td>" . $dossier['patient_id'] . "</td>";
            echo "<td>" . $dossier['titre'] . "</td>";
            echo "<td>" . $dossier['date_creation'] . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    }
    
    // 3. Vérifier les consultations
    echo "<h2>3. Consultations existantes</h2>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM consultations");
    $count = $stmt->fetch()['count'];
    echo "Nombre de consultations : $count<br>";
    
    if ($count > 0) {
        $stmt = $pdo->query("SELECT * FROM consultations LIMIT 5");
        $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Medical Record ID</th><th>Doctor ID</th><th>Type</th><th>Date</th></tr>";
        foreach ($consultations as $consultation) {
            echo "<tr>";
            echo "<td>" . $consultation['id'] . "</td>";
            echo "<td>" . $consultation['medical_record_id'] . "</td>";
            echo "<td>" . $consultation['doctor_id'] . "</td>";
            echo "<td>" . $consultation['type'] . "</td>";
            echo "<td>" . $consultation['date_consultation'] . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    }
    
    // 4. Test de la requête complète
    echo "<h2>4. Test de la requête complète</h2>";
    $sql = "SELECT 
                mr.id as medical_record_id,
                mr.titre,
                mr.description,
                mr.date_creation,
                p.id as patient_id,
                p.groupe_sanguin,
                p.allergies,
                p.statut as patient_statut,
                u.nom as patient_nom,
                u.prenom as patient_prenom,
                u.date_naissance,
                u.sexe,
                u.telephone,
                u.photo,
                CONCAT(doc.nom, ' ', doc.prenom) as doctor_name,
                doc.specialite as doctor_specialite,
                (SELECT COUNT(*) FROM consultations c WHERE c.medical_record_id = mr.id) as consultations_count,
                (SELECT COUNT(*) FROM prescriptions pr 
                 INNER JOIN consultations c ON pr.consultation_id = c.id 
                 WHERE c.medical_record_id = mr.id) as prescriptions_count,
                (SELECT MAX(c.date_consultation) FROM consultations c WHERE c.medical_record_id = mr.id) as derniere_consultation
            FROM medical_records mr
            INNER JOIN patients p ON mr.patient_id = p.id
            INNER JOIN users u ON p.user_id = u.id
            LEFT JOIN doctors d ON mr.created_by = d.id
            LEFT JOIN users doc ON d.user_id = doc.id
            ORDER BY mr.date_creation DESC
            LIMIT 3";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Résultats de la requête complète : " . count($results) . " dossiers<br>";
    
    if (count($results) > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Dossier ID</th><th>Patient</th><th>Consultations</th><th>Ordonnances</th><th>Dernière visite</th></tr>";
        foreach ($results as $result) {
            echo "<tr>";
            echo "<td>" . $result['medical_record_id'] . "</td>";
            echo "<td>" . $result['patient_nom'] . " " . $result['patient_prenom'] . "</td>";
            echo "<td>" . $result['consultations_count'] . "</td>";
            echo "<td>" . $result['prescriptions_count'] . "</td>";
            echo "<td>" . ($result['derniere_consultation'] ? $result['derniere_consultation'] : 'Aucune') . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    }
    
    // 5. Test de l'API
    echo "<h2>5. Test de l'API lister_dossiers_medicaux.php</h2>";
    echo "<a href='lister_dossiers_medicaux.php' target='_blank'>Tester l'API</a><br>";
    
    // 6. Test de l'API get_dossier_details.php
    if (count($results) > 0) {
        $first_dossier_id = $results[0]['medical_record_id'];
        echo "<h2>6. Test de l'API get_dossier_details.php</h2>";
        echo "<a href='get_dossier_details.php?medical_record_id=$first_dossier_id' target='_blank'>Tester l'API pour le dossier $first_dossier_id</a><br>";
    }
    
    // 7. Test de l'API get_admin_doctors.php
    echo "<h2>7. Test de l'API get_admin_doctors.php</h2>";
    echo "<a href='get_admin_doctors.php' target='_blank'>Tester l'API</a><br>";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?> 