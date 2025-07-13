<?php
require_once 'auth.php';
require_role('admin');
require_once 'db.php';

header('Content-Type: application/json');

try {
    // Récupérer tous les médecins
    $sql = "SELECT 
                d.id,
                CONCAT(u.nom, ' ', u.prenom) as name,
                u.specialite,
                u.telephone,
                u.email
            FROM doctors d
            INNER JOIN users u ON d.user_id = u.id
            ORDER BY u.nom, u.prenom";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'doctors' => $doctors
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des médecins: ' . $e->getMessage()
    ]);
}
?> 