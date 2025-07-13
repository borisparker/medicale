<?php
require_once 'db.php';

echo "<h1>Initialisation des dossiers médicaux de test</h1>";

try {
    // 1. Vérifier s'il y a des patients
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM patients");
    $patientCount = $stmt->fetch()['count'];
    echo "Nombre de patients dans la base : $patientCount<br>";
    
    if ($patientCount == 0) {
        echo "❌ Aucun patient trouvé. Veuillez d'abord créer des patients.<br>";
        exit;
    }
    
    // 2. Vérifier s'il y a des médecins
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM doctors");
    $doctorCount = $stmt->fetch()['count'];
    echo "Nombre de médecins dans la base : $doctorCount<br>";
    
    if ($doctorCount == 0) {
        echo "❌ Aucun médecin trouvé. Veuillez d'abord créer des médecins.<br>";
        exit;
    }
    
    // 3. Vérifier s'il y a déjà des dossiers médicaux
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM medical_records");
    $dossierCount = $stmt->fetch()['count'];
    echo "Nombre de dossiers médicaux existants : $dossierCount<br>";
    
    if ($dossierCount > 0) {
        echo "⚠️ Des dossiers médicaux existent déjà. Voulez-vous continuer ?<br>";
        echo "<a href='?force=1'>Oui, continuer</a> | <a href='admin.php'>Non, retourner à l'admin</a><br>";
        
        if (!isset($_GET['force'])) {
            exit;
        }
    }
    
    // 4. Récupérer les patients
    $stmt = $pdo->query("SELECT p.id, u.nom, u.prenom FROM patients p INNER JOIN users u ON p.user_id = u.id LIMIT 5");
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 5. Récupérer les médecins
    $stmt = $pdo->query("SELECT id FROM doctors LIMIT 3");
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($doctors)) {
        echo "❌ Aucun médecin trouvé<br>";
        exit;
    }
    
    $doctor_id = $doctors[0]['id'];
    
    echo "<h2>Création des dossiers médicaux</h2>";
    
    // 6. Créer des dossiers médicaux pour chaque patient
    foreach ($patients as $patient) {
        // Vérifier si un dossier existe déjà
        $stmt = $pdo->prepare("SELECT id FROM medical_records WHERE patient_id = ?");
        $stmt->execute([$patient['id']]);
        
        if (!$stmt->fetch()) {
            // Créer le dossier médical
            $stmt = $pdo->prepare("INSERT INTO medical_records (patient_id, titre, description, created_by) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $patient['id'],
                'Dossier médical - ' . $patient['nom'] . ' ' . $patient['prenom'],
                'Dossier médical créé automatiquement',
                $doctor_id
            ]);
            
            $medical_record_id = $pdo->lastInsertId();
            echo "✅ Dossier créé pour " . $patient['nom'] . " " . $patient['prenom'] . " (ID: $medical_record_id)<br>";
            
            // Ajouter quelques consultations de test
            $consultationTypes = [
                'Consultation générale',
                'Bilan de santé',
                'Suivi traitement',
                'Consultation spécialisée'
            ];
            
            $motifs = [
                'Contrôle de routine',
                'Bilan annuel',
                'Suivi d\'un traitement',
                'Consultation pour symptômes'
            ];
            
            $observations = [
                'Examen clinique normal',
                'Bons résultats biologiques',
                'Traitement efficace',
                'Amélioration des symptômes'
            ];
            
            // Créer 2-3 consultations par patient
            for ($i = 0; $i < rand(2, 3); $i++) {
                $date = date('Y-m-d', strtotime('-' . rand(1, 30) . ' days'));
                $type = $consultationTypes[array_rand($consultationTypes)];
                $motif = $motifs[array_rand($motifs)];
                $observation = $observations[array_rand($observations)];
                
                $stmt = $pdo->prepare("INSERT INTO consultations (medical_record_id, doctor_id, type, motif, observations, date_consultation) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $medical_record_id,
                    $doctor_id,
                    $type,
                    $motif,
                    $observation,
                    $date
                ]);
                
                echo "  - Consultation ajoutée : $type ($date)<br>";
            }
        } else {
            echo "⚠️ Dossier déjà existant pour " . $patient['nom'] . " " . $patient['prenom'] . "<br>";
        }
    }
    
    echo "<h2>Résumé</h2>";
    
    // Afficher le nombre final de dossiers
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM medical_records");
    $finalCount = $stmt->fetch()['count'];
    echo "Nombre total de dossiers médicaux : $finalCount<br>";
    
    // Afficher le nombre final de consultations
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM consultations");
    $consultationCount = $stmt->fetch()['count'];
    echo "Nombre total de consultations : $consultationCount<br>";
    
    echo "<br><a href='test_dossiers_medicaux.html' class='bg-indigo-600 text-white px-4 py-2 rounded-lg'>Tester les dossiers médicaux</a>";
    echo "<br><a href='admin.php' class='bg-gray-600 text-white px-4 py-2 rounded-lg mt-2 inline-block'>Retour à l'admin</a>";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?> 