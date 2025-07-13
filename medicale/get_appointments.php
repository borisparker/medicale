<?php
require_once 'auth.php';
require_role('patient');
require_once 'db.php';
header('Content-Type: application/json');

// Vérification de la session
$user = $_SESSION['user'] ?? null;
if (!$user || !is_array($user) || !isset($user['id'])) {
    echo json_encode([
        'success' => false, 
        'appointments' => [], 
        'message' => 'Session invalide.',
        'debug' => 'Session: ' . json_encode($_SESSION)
    ]);
    exit;
}

$user_id = $user['id'];

// Récupérer l'id du patient
try {
    $stmt = $pdo->prepare('SELECT id FROM patients WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $patient = $stmt->fetch();
    
    if (!$patient) {
        echo json_encode([
            'success' => false, 
            'appointments' => [], 
            'message' => 'Patient introuvable pour user_id ' . $user_id,
            'debug' => 'user_id: ' . $user_id
        ]);
        exit;
    }
    
    $patient_id = $patient['id'];
    
    // Récupérer les rendez-vous du patient avec LEFT JOIN pour inclure tous les rendez-vous
    $stmt = $pdo->prepare('
        SELECT 
            a.*, 
            d.id as doctor_id, 
            u.nom as doc_nom, 
            u.prenom as doc_prenom 
        FROM appointments a 
        LEFT JOIN doctors d ON a.doctor_id = d.id 
        LEFT JOIN users u ON d.user_id = u.id 
        WHERE a.patient_id = ? 
        ORDER BY a.date_heure DESC
    ');
    $stmt->execute([$patient_id]);
    $rdvs = $stmt->fetchAll();
    
    $appointments = [];
    foreach ($rdvs as $rdv) {
        // Formatage date/heure
        $dt = new DateTime($rdv['date_heure']);
        $date = $dt->format('d/m/Y');
        $heure = $dt->format('H:i');
        
        // Gestion du statut
        $statut = $rdv['statut'] ?? 'en attente';
        $statut_class = 'bg-yellow-100 text-yellow-800';
        
        if ($statut === 'confirmé') {
            $statut_class = 'bg-green-100 text-green-800';
        } elseif ($statut === 'annulé') {
            $statut_class = 'bg-red-100 text-red-800';
        } elseif ($statut === 'terminé') {
            $statut_class = 'bg-gray-100 text-gray-800';
        }
        
        // Nom du médecin
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
    
    echo json_encode([
        'success' => true, 
        'appointments' => $appointments, 
        'patient_id' => $patient_id,
        'count' => count($appointments),
        'debug_total' => count($rdvs)
    ]);
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false, 
        'appointments' => [], 
        'message' => 'Erreur lors de la récupération des rendez-vous: ' . $e->getMessage(),
        'debug' => 'Exception: ' . $e->getMessage()
    ]);
} 