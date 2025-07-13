<?php
require_once 'auth.php';
require_once 'db.php';

header('Content-Type: application/json');

// Debug: Log pour voir si le fichier est appelé
error_log("get_medications.php appelé");

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    error_log("get_medications.php - Accès non autorisé: " . json_encode($_SESSION));
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé', 'debug' => $_SESSION]);
    exit;
}

try {
    error_log("get_medications.php - Début de la récupération des médicaments");
    
    // Récupérer tous les médicaments (SANS la colonne description)
    $sql = "SELECT 
                id,
                nom,
                dosage,
                forme,
                
                stock
            FROM medications 
            WHERE stock > 0 
            ORDER BY nom ASC";
    
    error_log("get_medications.php - Requête SQL: " . $sql);
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $medications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("get_medications.php - Nombre de médicaments trouvés: " . count($medications));
    
    // Formater les données
    $formattedMedications = array_map(function($medication) {
        return [
            'id' => $medication['id'],
            'nom' => $medication['nom'],
            'dosage' => $medication['dosage'],
            'forme' => $medication['forme'],
            'description' => '', // Champ vide car non présent en base
            
            'stock' => $medication['stock'],
            'display_name' => $medication['nom'] . ' ' . $medication['dosage'] . ' - ' . $medication['forme']
        ];
    }, $medications);
    
    $response = [
        'success' => true,
        'medications' => $formattedMedications,
        'count' => count($formattedMedications),
        'debug' => [
            'user_id' => $_SESSION['user']['id'] ?? 'non défini',
            'medications_count' => count($medications)
        ]
    ];
    
    error_log("get_medications.php - Réponse: " . json_encode($response));
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("get_medications.php - Erreur: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des médicaments: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}
?>