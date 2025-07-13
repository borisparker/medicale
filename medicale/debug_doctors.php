<?php
require_once 'db.php';

echo "<h2>Debug - Problème des docteurs</h2>";

// 1. Vérifier tous les utilisateurs docteurs
echo "<h3>1. Utilisateurs docteurs</h3>";
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'docteur'");
$doctors_users = $stmt->fetchAll();
echo "📋 Utilisateurs docteurs (" . count($doctors_users) . "):<br>";
foreach($doctors_users as $user) {
    echo "- ID: " . $user['id'] . " - Nom: " . $user['nom'] . " " . $user['prenom'] . " - Email: " . $user['email'] . "<br>";
}

// 2. Vérifier la table doctors
echo "<h3>2. Table doctors</h3>";
$stmt = $pdo->query("SELECT * FROM doctors");
$doctors = $stmt->fetchAll();
echo "📋 Docteurs dans la table doctors (" . count($doctors) . "):<br>";
foreach($doctors as $doctor) {
    echo "- ID: " . $doctor['id'] . " - User ID: " . $doctor['user_id'] . "<br>";
}

// 3. Vérifier la requête de get_doctors.php
echo "<h3>3. Test de get_doctors.php</h3>";
$stmt = $pdo->query("SELECT u.id as id, CONCAT(u.prenom, ' ', u.nom) as nom FROM users u INNER JOIN doctors d ON u.id = d.user_id WHERE u.role = 'docteur' ORDER BY u.nom, u.prenom");
$doctors_for_form = $stmt->fetchAll();
echo "📋 Docteurs pour le formulaire (" . count($doctors_for_form) . "):<br>";
foreach($doctors_for_form as $doctor) {
    echo "- ID: " . $doctor['id'] . " - Nom: " . $doctor['nom'] . "<br>";
}

// 4. Test de validation dans ajouter_rdv.php
echo "<h3>4. Test de validation</h3>";
foreach($doctors_for_form as $doctor) {
    $doctor_id = $doctor['id'];
    echo "Test pour docteur ID: $doctor_id<br>";
    
    // Test 1: Vérifier dans doctors (ce que fait ajouter_rdv.php)
    $stmt = $pdo->prepare("SELECT id FROM doctors WHERE id = ?");
    $stmt->execute([$doctor_id]);
    $result = $stmt->fetch();
    if($result) {
        echo "✅ Trouvé dans doctors<br>";
    } else {
        echo "❌ NON trouvé dans doctors<br>";
    }
    
    // Test 2: Vérifier dans users
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'docteur'");
    $stmt->execute([$doctor_id]);
    $result = $stmt->fetch();
    if($result) {
        echo "✅ Trouvé dans users<br>";
    } else {
        echo "❌ NON trouvé dans users<br>";
    }
    echo "<br>";
}

echo "<h3>✅ Diagnostic terminé</h3>";
?> 