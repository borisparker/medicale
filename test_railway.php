<?php
// Test de configuration pour Railway
// Ce fichier permet de vérifier que tout fonctionne correctement

echo "<h1>🔧 Test de Configuration Railway</h1>";
echo "<hr>";

// Test 1: Variables d'environnement
echo "<h2>📋 Test des Variables d'Environnement</h2>";
$env_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'PORT'];
$all_vars_present = true;

foreach ($env_vars as $var) {
    $value = $_ENV[$var] ?? 'NON DÉFINI';
    $status = isset($_ENV[$var]) ? '✅' : '❌';
    echo "<p>$status $var: $value</p>";
    if (!isset($_ENV[$var])) {
        $all_vars_present = false;
    }
}

echo "<hr>";

// Test 2: Connexion à la base de données
echo "<h2>🗄️ Test de Connexion à la Base de Données</h2>";

if ($all_vars_present) {
    try {
        require_once 'medicale/db_railway.php';
        echo "<p>✅ Connexion à la base de données réussie</p>";
        
        // Test d'une requête simple
        $stmt = $pdo->query('SELECT COUNT(*) as count FROM information_schema.tables');
        $result = $stmt->fetch();
        echo "<p>✅ Requête de test réussie. Nombre de tables: " . $result['count'] . "</p>";
        
    } catch (Exception $e) {
        echo "<p>❌ Erreur de connexion: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ Variables d'environnement manquantes</p>";
}

echo "<hr>";

// Test 3: Extensions PHP
echo "<h2>🔧 Test des Extensions PHP</h2>";
$required_extensions = ['pdo', 'pdo_mysql', 'mysqli', 'gd', 'mbstring'];

foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? '✅' : '❌';
    echo "<p>$status Extension $ext: " . (extension_loaded($ext) ? 'Installée' : 'Manquante') . "</p>";
}

echo "<hr>";

// Test 4: Permissions des dossiers
echo "<h2>📁 Test des Permissions</h2>";
$directories = ['uploads', 'medicale/uploads'];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $writable = is_writable($dir) ? '✅' : '❌';
        echo "<p>$writable Dossier $dir: " . (is_writable($dir) ? 'Écriture autorisée' : 'Écriture refusée') . "</p>";
    } else {
        echo "<p>⚠️ Dossier $dir: N'existe pas</p>";
    }
}

echo "<hr>";

// Test 5: Informations système
echo "<h2>💻 Informations Système</h2>";
echo "<p>📊 Version PHP: " . phpversion() . "</p>";
echo "<p>🌐 Serveur: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Inconnu') . "</p>";
echo "<p>📂 Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Inconnu') . "</p>";
echo "<p>🔗 URL: " . ($_SERVER['REQUEST_URI'] ?? 'Inconnu') . "</p>";

echo "<hr>";

// Résumé
echo "<h2>📊 Résumé</h2>";
if ($all_vars_present) {
    echo "<p style='color: green; font-weight: bold;'>✅ Configuration Railway prête !</p>";
    echo "<p>Votre application devrait fonctionner correctement sur Railway.</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Configuration incomplète</p>";
    echo "<p>Veuillez vérifier les variables d'environnement sur Railway.</p>";
}

echo "<hr>";
echo "<p><small>Test généré le " . date('Y-m-d H:i:s') . "</small></p>";
?> 