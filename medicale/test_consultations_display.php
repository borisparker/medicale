<?php
require_once 'auth.php';
require_once 'db.php';

// Simuler une session de docteur pour le test
session_start();
$_SESSION['user'] = [
    'id' => 1, // ID d'un docteur existant
    'role' => 'docteur'
];

echo "<h2>Test de get_consultations.php</h2>";

// Test 1: Vérifier la session
echo "<h3>1. Vérification de la session</h3>";
if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'docteur') {
    echo "✅ Session valide - Docteur ID: " . $_SESSION['user']['id'] . "<br>";
} else {
    echo "❌ Session invalide<br>";
    exit;
}

// Test 2: Vérifier la connexion à la base de données
echo "<h3>2. Vérification de la base de données</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM consultations");
    $result = $stmt->fetch();
    echo "✅ Connexion DB OK - Total consultations: " . $result['total'] . "<br>";
} catch (Exception $e) {
    echo "❌ Erreur DB: " . $e->getMessage() . "<br>";
    exit;
}

// Test 3: Vérifier les docteurs
echo "<h3>3. Vérification des docteurs</h3>";
try {
    $stmt = $pdo->query("SELECT id, user_id FROM doctors LIMIT 5");
    $doctors = $stmt->fetchAll();
    echo "✅ Docteurs trouvés: " . count($doctors) . "<br>";
    foreach ($doctors as $doctor) {
        echo "   - Docteur ID: " . $doctor['id'] . ", User ID: " . $doctor['user_id'] . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
}

// Test 4: Vérifier les consultations du docteur
echo "<h3>4. Vérification des consultations du docteur</h3>";
try {
    $doctor_id = 1; // Utiliser le premier docteur pour le test
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
            WHERE c.doctor_id = ?
            ORDER BY c.date_consultation DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$doctor_id]);
    $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "✅ Requête exécutée - Consultations trouvées: " . count($consultations) . "<br>";
    
    if (count($consultations) > 0) {
        echo "<h4>Consultations trouvées:</h4>";
        foreach ($consultations as $consultation) {
            echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
            echo "<strong>ID:</strong> " . $consultation['id'] . "<br>";
            echo "<strong>Patient:</strong> " . $consultation['patient_name'] . "<br>";
            echo "<strong>Date:</strong> " . $consultation['date_consultation'] . "<br>";
            echo "<strong>Type:</strong> " . $consultation['type'] . "<br>";
            echo "<strong>Motif:</strong> " . $consultation['motif'] . "<br>";
            echo "</div>";
        }
    } else {
        echo "⚠️ Aucune consultation trouvée pour ce docteur<br>";
        
        // Vérifier s'il y a des consultations dans la base
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM consultations");
        $total = $stmt->fetch()['total'];
        echo "Total consultations dans la base: " . $total . "<br>";
        
        if ($total > 0) {
            echo "<h4>Consultations existantes (tous docteurs):</h4>";
            $stmt = $pdo->query("SELECT c.id, c.doctor_id, c.type, c.motif FROM consultations c LIMIT 5");
            $all_consultations = $stmt->fetchAll();
            foreach ($all_consultations as $consultation) {
                echo "ID: " . $consultation['id'] . ", Docteur: " . $consultation['doctor_id'] . ", Type: " . $consultation['type'] . "<br>";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur lors de la récupération des consultations: " . $e->getMessage() . "<br>";
}

// Test 5: Simuler la réponse JSON
echo "<h3>5. Simulation de la réponse JSON</h3>";
try {
    $doctor_id = 1;
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
            WHERE c.doctor_id = ?
            ORDER BY c.date_consultation DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$doctor_id]);
    $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $formattedConsultations = array_map(function($consultation) {
        $date = new DateTime($consultation['date_consultation']);
        return [
            'id' => $consultation['id'],
            'type' => $consultation['type'],
            'motif' => $consultation['motif'],
            'observations' => $consultation['observations'],
            'date_consultation' => $date->format('d/m/Y'),
            'patient_name' => $consultation['patient_name'],
            'patient_phone' => $consultation['patient_phone'],
            'patient_id' => $consultation['patient_id'],
            'medical_record_id' => $consultation['medical_record_id']
        ];
    }, $consultations);
    
    $response = [
        'success' => true,
        'consultations' => $formattedConsultations,
        'count' => count($formattedConsultations)
    ];
    
    echo "✅ Réponse JSON simulée:<br>";
    echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
}
?> 