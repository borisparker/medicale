<?php
session_start();
require_once 'db.php';

echo "<h2>Test de la base de données</h2>";

// Test 1: Vérifier la connexion
echo "<h3>1. Test de connexion</h3>";
try {
    $pdo->query("SELECT 1");
    echo "✅ Connexion à la base de données réussie<br>";
} catch(Exception $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "<br>";
    exit;
}

// Test 2: Vérifier les tables
echo "<h3>2. Vérification des tables</h3>";
$tables = ['users', 'patients', 'doctors', 'appointments'];
foreach($tables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $result = $stmt->fetch();
        echo "✅ Table $table: " . $result['count'] . " enregistrements<br>";
    } catch(Exception $e) {
        echo "❌ Erreur table $table: " . $e->getMessage() . "<br>";
    }
}

// Test 3: Vérifier la session
echo "<h3>3. Vérification de la session</h3>";
if(isset($_SESSION['user'])) {
    echo "✅ Session utilisateur trouvée<br>";
    echo "ID utilisateur: " . ($_SESSION['user']['id'] ?? 'Non défini') . "<br>";
    echo "Email: " . ($_SESSION['user']['email'] ?? 'Non défini') . "<br>";
    echo "Rôle: " . ($_SESSION['user']['role'] ?? 'Non défini') . "<br>";
} else {
    echo "❌ Aucune session utilisateur trouvée<br>";
}

// Test 4: Vérifier les données utilisateur
if(isset($_SESSION['user']['id'])) {
    echo "<h3>4. Vérification des données utilisateur</h3>";
    $user_id = $_SESSION['user']['id'];
    
    // Vérifier l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if($user) {
        echo "✅ Utilisateur trouvé: " . $user['prenom'] . " " . $user['nom'] . "<br>";
        
        // Vérifier le patient
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $patient = $stmt->fetch();
        if($patient) {
            echo "✅ Patient trouvé (ID: " . $patient['id'] . ")<br>";
            
            // Vérifier les rendez-vous
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM appointments WHERE patient_id = ?");
            $stmt->execute([$patient['id']]);
            $result = $stmt->fetch();
            echo "📅 Rendez-vous du patient: " . $result['count'] . "<br>";
            
            // Afficher les rendez-vous
            $stmt = $pdo->prepare("SELECT a.*, d.id as doctor_id, u.nom as doc_nom, u.prenom as doc_prenom 
                                  FROM appointments a 
                                  JOIN doctors d ON a.doctor_id = d.id 
                                  JOIN users u ON d.user_id = u.id 
                                  WHERE a.patient_id = ? 
                                  ORDER BY a.date_heure DESC");
            $stmt->execute([$patient['id']]);
            $rdvs = $stmt->fetchAll();
            echo "<h4>Détail des rendez-vous:</h4>";
            foreach($rdvs as $rdv) {
                echo "- " . $rdv['date_heure'] . " avec Dr. " . $rdv['doc_prenom'] . " " . $rdv['doc_nom'] . " (Statut: " . $rdv['statut'] . ")<br>";
            }
        } else {
            echo "❌ Patient non trouvé pour user_id: $user_id<br>";
        }
    } else {
        echo "❌ Utilisateur non trouvé pour ID: $user_id<br>";
    }
}

// Test 5: Vérifier les docteurs
echo "<h3>5. Vérification des docteurs</h3>";
$stmt = $pdo->query("SELECT d.id, u.prenom, u.nom FROM doctors d JOIN users u ON d.user_id = u.id");
$doctors = $stmt->fetchAll();
echo "📋 Docteurs disponibles (" . count($doctors) . "):<br>";
foreach($doctors as $doctor) {
    echo "- Dr. " . $doctor['prenom'] . " " . $doctor['nom'] . " (ID: " . $doctor['id'] . ")<br>";
}
?> 