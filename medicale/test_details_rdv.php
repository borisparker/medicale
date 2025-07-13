<?php
session_start();
require_once 'db.php';

echo "<h2>Test - Fonctionnalité Détails Rendez-vous</h2>";

// Simuler la session patient
$_SESSION['user'] = [
    'id' => 5, // ID du patient créé dans les données de test
    'email' => 'jean.patient@test.com',
    'role' => 'patient'
];

$user_id = $_SESSION['user']['id'];

echo "<h3>1. Vérification de la session</h3>";
echo "User ID: $user_id<br>";
echo "Role: " . $_SESSION['user']['role'] . "<br>";

// Récupérer le patient
$stmt = $pdo->prepare('SELECT id FROM patients WHERE user_id = ?');
$stmt->execute([$user_id]);
$patient = $stmt->fetch();

if (!$patient) {
    echo "❌ Patient introuvable pour user_id: $user_id<br>";
    exit;
}

$patient_id = $patient['id'];
echo "✅ Patient trouvé (ID: $patient_id)<br>";

// Vérifier les rendez-vous du patient
echo "<h3>2. Rendez-vous du patient</h3>";
$stmt = $pdo->prepare('
    SELECT 
        a.id,
        a.date_heure,
        a.motif,
        a.commentaire,
        a.statut,
        a.date_creation,
        CONCAT(u.nom, ' ', u.prenom) as medecin,
        d.specialite as doctor_specialite,
        d.telephone as doctor_telephone
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.id
    JOIN users u ON d.user_id = u.id
    WHERE a.patient_id = ?
    ORDER BY a.date_heure DESC
    LIMIT 3
');
$stmt->execute([$patient_id]);
$appointments = $stmt->fetchAll();

echo "📅 Nombre de rendez-vous trouvés: " . count($appointments) . "<br>";

if (count($appointments) > 0) {
    echo "<h3>3. Test de la requête de détails</h3>";
    $test_rdv_id = $appointments[0]['id'];
    echo "Test avec le rendez-vous ID: $test_rdv_id<br>";
    
    // Simuler la requête de get_appointment_details.php
    $stmt = $pdo->prepare("
        SELECT 
            a.id,
            a.date_heure,
            a.motif,
            a.commentaire,
            a.statut,
            a.date_creation,
            CONCAT(u.nom, ' ', u.prenom) as medecin,
            d.specialite as doctor_specialite,
            d.telephone as doctor_telephone
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        JOIN users u ON d.user_id = u.id
        WHERE a.id = ? AND a.patient_id = ?
    ");
    
    $stmt->execute([$test_rdv_id, $patient_id]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($appointment) {
        echo "✅ Détails du rendez-vous récupérés avec succès:<br>";
        echo "- ID: " . $appointment['id'] . "<br>";
        echo "- Date/Heure: " . $appointment['date_heure'] . "<br>";
        echo "- Médecin: " . $appointment['medecin'] . "<br>";
        echo "- Motif: " . ($appointment['motif'] ?? 'Non spécifié') . "<br>";
        echo "- Statut: " . $appointment['statut'] . "<br>";
        echo "- Commentaire: " . ($appointment['commentaire'] ?? 'Aucun') . "<br>";
        echo "- Spécialité: " . ($appointment['doctor_specialite'] ?? 'Non spécifiée') . "<br>";
        echo "- Téléphone: " . ($appointment['doctor_telephone'] ?? 'Non spécifié') . "<br>";
        
        // Test du formatage des dates
        echo "<h3>4. Test du formatage des dates</h3>";
        $date_heure = new DateTime($appointment['date_heure']);
        $date = $date_heure->format('d/m/Y');
        $heure = $date_heure->format('H:i');
        echo "Date formatée: $date<br>";
        echo "Heure formatée: $heure<br>";
        
        if ($appointment['date_creation']) {
            $date_creation = new DateTime($appointment['date_creation']);
            $date_creation_formatted = $date_creation->format('d/m/Y à H:i');
            echo "Date création formatée: $date_creation_formatted<br>";
        }
        
        // Test de la traduction des statuts
        echo "<h3>5. Test de la traduction des statuts</h3>";
        $statuts = [
            'en attente' => 'En attente',
            'confirmé' => 'À venir',
            'terminé' => 'Terminé',
            'annulé' => 'Annulé'
        ];
        
        $statut_traduit = $statuts[$appointment['statut']] ?? $appointment['statut'];
        echo "Statut original: " . $appointment['statut'] . "<br>";
        echo "Statut traduit: $statut_traduit<br>";
        
        // Test de la réponse JSON
        echo "<h3>6. Test de la réponse JSON</h3>";
        $appointment['date'] = $date;
        $appointment['heure'] = $heure;
        $appointment['statut'] = $statut_traduit;
        if ($appointment['date_creation']) {
            $appointment['date_creation'] = $date_creation_formatted;
        }
        
        $response = [
            'success' => true,
            'appointment' => $appointment
        ];
        
        echo "Réponse JSON:<br>";
        echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
        
    } else {
        echo "❌ Aucun rendez-vous trouvé avec l'ID $test_rdv_id pour le patient $patient_id<br>";
    }
} else {
    echo "❌ Aucun rendez-vous trouvé pour le patient $patient_id<br>";
    echo "Veuillez d'abord créer des rendez-vous de test.<br>";
}

echo "<h3>7. Test de l'URL</h3>";
echo "URL de test: <a href='get_appointment_details.php?id=1' target='_blank'>get_appointment_details.php?id=1</a><br>";
echo "Note: Cette URL ne fonctionnera que si vous êtes connecté en tant que patient.<br>";
?> 