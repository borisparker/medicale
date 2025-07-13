<?php
// Configuration spécifique pour Railway
// Ce fichier utilise les variables d'environnement de Railway

// Récupération des variables d'environnement
$host = $_ENV['DB_HOST'] ?? 'localhost';
$db   = $_ENV['DB_NAME'] ?? 'medicale';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$charset = 'utf8mb4';

// Construction de la DSN
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Options PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Tentative de connexion
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // Optionnel : log de succès (à retirer en production)
    error_log("Connexion à la base de données réussie");
} catch (PDOException $e) {
    // Log de l'erreur
    error_log("Erreur de connexion à la base de données: " . $e->getMessage());
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// Fonction pour vérifier la connexion
function checkDatabaseConnection() {
    global $pdo;
    try {
        $pdo->query('SELECT 1');
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Fonction pour obtenir les informations de connexion (pour debug)
function getConnectionInfo() {
    global $host, $db, $user;
    return [
        'host' => $host,
        'database' => $db,
        'user' => $user,
        'status' => checkDatabaseConnection() ? 'connected' : 'disconnected'
    ];
}
?> 