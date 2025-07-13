<?php
session_start();
require_once 'db.php';

echo "<h2>Test du système de rendez-vous</h2>";

// Test 1: Vérifier la session
echo "<h3>1. Vérification de la session</h3>";
if(isset($_SESSION['user'])) {
    echo "✅ Session utilisateur trouvée<br>";
    echo "ID: " . $_SESSION['user']['id'] . "<br>";
    echo "Email: " . $_SESSION['user']['email'] . "<br>";
    echo "Rôle: " . $_SESSION['user']['role'] . "<br>";
} else {
    echo "❌ Aucune session utilisateur<br>";
    echo "<p>Connectez-vous d'abord en tant que patient.</p>";
    exit;
}

// Test 2: Vérifier que l'utilisateur est un patient
if($_SESSION['user']['role'] !== 'patient') {
    echo "❌ L'utilisateur n'est pas un patient (rôle: " . $_SESSION['user']['role'] . ")<br>";
    exit;
}

$user_id = $_SESSION['user']['id'];

// Test 3: Vérifier que le patient existe
echo "<h3>2. Vérification du patient</h3>";
$stmt = $pdo->prepare("SELECT * FROM patients WHERE user_id = ?");
$stmt->execute([$user_id]);
$patient = $stmt->fetch();

if($patient) {
    echo "✅ Patient trouvé (ID: " . $patient['id'] . ")<br>";
    $patient_id = $patient['id'];
} else {
    echo "❌ Patient non trouvé pour user_id: $user_id<br>";
    echo "<p>Le patient doit être créé dans la base de données.</p>";
    exit;
}

// Test 4: Vérifier les docteurs disponibles
echo "<h3>3. Vérification des docteurs</h3>";
$stmt = $pdo->query("SELECT d.id, u.prenom, u.nom FROM doctors d JOIN users u ON d.user_id = u.id");
$doctors = $stmt->fetchAll();

if(count($doctors) > 0) {
    echo "✅ " . count($doctors) . " docteur(s) trouvé(s):<br>";
    foreach($doctors as $doctor) {
        echo "- Dr. " . $doctor['prenom'] . " " . $doctor['nom'] . " (ID: " . $doctor['id'] . ")<br>";
    }
    $test_doctor_id = $doctors[0]['id'];
} else {
    echo "❌ Aucun docteur trouvé<br>";
    echo "<p>Il faut créer des docteurs dans la base de données.</p>";
    exit;
}

// Test 5: Vérifier les rendez-vous existants
echo "<h3>4. Rendez-vous existants</h3>";
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM appointments WHERE patient_id = ?");
$stmt->execute([$patient_id]);
$result = $stmt->fetch();
echo "📅 Rendez-vous existants: " . $result['count'] . "<br>";

// Test 6: Simuler l'ajout d'un rendez-vous
echo "<h3>5. Test d'ajout de rendez-vous</h3>";
$test_date = date('Y-m-d H:i:s', strtotime('+1 day'));
$test_motif = "Test de consultation";
$test_commentaire = "Rendez-vous de test";

try {
    $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, date_heure, motif, commentaire, statut) VALUES (?, ?, ?, ?, ?, 'en attente')");
    $stmt->execute([$patient_id, $test_doctor_id, $test_date, $test_motif, $test_commentaire]);
    $appointment_id = $pdo->lastInsertId();
    echo "✅ Rendez-vous de test ajouté (ID: $appointment_id)<br>";
    
    // Vérifier que le rendez-vous a été ajouté
    $stmt = $pdo->prepare("SELECT a.*, u.prenom as doc_prenom, u.nom as doc_nom FROM appointments a JOIN doctors d ON a.doctor_id = d.id JOIN users u ON d.user_id = u.id WHERE a.id = ?");
    $stmt->execute([$appointment_id]);
    $rdv = $stmt->fetch();
    
    if($rdv) {
        echo "✅ Rendez-vous récupéré:<br>";
        echo "- Date: " . $rdv['date_heure'] . "<br>";
        echo "- Médecin: Dr. " . $rdv['doc_prenom'] . " " . $rdv['doc_nom'] . "<br>";
        echo "- Motif: " . $rdv['motif'] . "<br>";
        echo "- Statut: " . $rdv['statut'] . "<br>";
    }
    
    // Nettoyer le test
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->execute([$appointment_id]);
    echo "🧹 Rendez-vous de test supprimé<br>";
    
} catch(Exception $e) {
    echo "❌ Erreur lors de l'ajout du rendez-vous: " . $e->getMessage() . "<br>";
}

// Test 7: Vérifier la structure de la table appointments
echo "<h3>6. Structure de la table appointments</h3>";
try {
    $stmt = $pdo->query("DESCRIBE appointments");
    $columns = $stmt->fetchAll();
    echo "✅ Structure de la table appointments:<br>";
    foreach($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")<br>";
    }
} catch(Exception $e) {
    echo "❌ Erreur lors de la vérification de la structure: " . $e->getMessage() . "<br>";
}

echo "<h3>✅ Tests terminés</h3>";
echo "<p>Si tous les tests sont passés, le système devrait fonctionner correctement.</p>";
?> 