<?php
session_start();
require_once 'db.php';

echo "<h2>Debug - Affichage des rendez-vous</h2>";

// 1. Vérifier la session
echo "<h3>1. Session utilisateur</h3>";
if(isset($_SESSION['user'])) {
    echo "✅ Session trouvée<br>";
    echo "ID: " . $_SESSION['user']['id'] . "<br>";
    echo "Email: " . $_SESSION['user']['email'] . "<br>";
    echo "Rôle: " . $_SESSION['user']['role'] . "<br>";
    $user_id = $_SESSION['user']['id'];
} else {
    echo "❌ Aucune session<br>";
    exit;
}

// 2. Vérifier le patient
echo "<h3>2. Vérification du patient</h3>";
$stmt = $pdo->prepare("SELECT * FROM patients WHERE user_id = ?");
$stmt->execute([$user_id]);
$patient = $stmt->fetch();

if($patient) {
    echo "✅ Patient trouvé (ID: " . $patient['id'] . ")<br>";
    $patient_id = $patient['id'];
} else {
    echo "❌ Patient non trouvé pour user_id: $user_id<br>";
    exit;
}

// 3. Vérifier tous les rendez-vous de ce patient
echo "<h3>3. Rendez-vous du patient</h3>";
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE patient_id = ?");
$stmt->execute([$patient_id]);
$all_appointments = $stmt->fetchAll();

echo "📅 Nombre total de rendez-vous: " . count($all_appointments) . "<br>";
foreach($all_appointments as $rdv) {
    echo "- Rendez-vous ID: " . $rdv['id'] . " - Date: " . $rdv['date_heure'] . " - Statut: " . $rdv['statut'] . "<br>";
}

// 4. Test de la requête complète (comme dans get_appointments.php)
echo "<h3>4. Test de la requête complète</h3>";
try {
    $stmt = $pdo->prepare('
        SELECT 
            a.*, 
            d.id as doctor_id, 
            u.nom as doc_nom, 
            u.prenom as doc_prenom 
        FROM appointments a 
        JOIN doctors d ON a.doctor_id = d.id 
        JOIN users u ON d.user_id = u.id 
        WHERE a.patient_id = ? 
        ORDER BY a.date_heure DESC
    ');
    $stmt->execute([$patient_id]);
    $rdvs = $stmt->fetchAll();
    
    echo "✅ Requête complète réussie - " . count($rdvs) . " rendez-vous trouvés<br>";
    
    foreach($rdvs as $rdv) {
        $dt = new DateTime($rdv['date_heure']);
        $date = $dt->format('d/m/Y');
        $heure = $dt->format('H:i');
        $nom_medecin = 'Dr. ' . ($rdv['doc_prenom'] ?? '') . ' ' . ($rdv['doc_nom'] ?? '');
        
        echo "- " . $date . " " . $heure . " avec " . $nom_medecin . " (Motif: " . $rdv['motif'] . ")<br>";
    }
    
} catch(Exception $e) {
    echo "❌ Erreur dans la requête complète: " . $e->getMessage() . "<br>";
}

// 5. Test de la réponse JSON
echo "<h3>5. Test de la réponse JSON</h3>";
$appointments = [];
foreach ($rdvs as $rdv) {
    $dt = new DateTime($rdv['date_heure']);
    $date = $dt->format('d/m/Y');
    $heure = $dt->format('H:i');
    
    $statut = $rdv['statut'] ?? 'en attente';
    $statut_class = 'bg-yellow-100 text-yellow-800';
    
    if ($statut === 'confirmé') {
        $statut_class = 'bg-green-100 text-green-800';
    } elseif ($statut === 'annulé') {
        $statut_class = 'bg-red-100 text-red-800';
    } elseif ($statut === 'terminé') {
        $statut_class = 'bg-gray-100 text-gray-800';
    }
    
    $nom_medecin = 'Dr. ' . ($rdv['doc_prenom'] ?? '') . ' ' . ($rdv['doc_nom'] ?? '');
    $nom_medecin = trim($nom_medecin);
    if (empty($nom_medecin) || $nom_medecin === 'Dr. ') {
        $nom_medecin = 'Médecin inconnu';
    }
    
    $appointments[] = [
        'id' => $rdv['id'],
        'date' => $date,
        'heure' => $heure,
        'medecin' => $nom_medecin,
        'statut' => ucfirst($statut),
        'statut_class' => $statut_class,
        'motif' => $rdv['motif'] ?? '',
        'commentaire' => $rdv['commentaire'] ?? ''
    ];
}

$response = [
    'success' => true, 
    'appointments' => $appointments, 
    'patient_id' => $patient_id,
    'count' => count($appointments)
];

echo "📋 Réponse JSON générée:<br>";
echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";

echo "<h3>✅ Debug terminé</h3>";
echo "<p>Si tu vois des rendez-vous ici mais pas dans l'interface, le problème est dans le JavaScript.</p>";
?> 