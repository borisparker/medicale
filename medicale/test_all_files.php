<?php
session_start();
header('Content-Type: application/json');

// Test de base de la session
$tests = [];

// Test 1: Vérifier la session
$tests['session'] = [
    'session_exists' => isset($_SESSION),
    'user_exists' => isset($_SESSION['user']),
    'user_data' => $_SESSION['user'] ?? null
];

// Test 2: Vérifier les fichiers PHP
$files_to_test = [
    'get_consultations.php',
    'create_consultation.php', 
    'create_prescription.php',
    'get_patient_medical_record.php',
    'get_doctor_appointments.php',
    'update_appointment_status.php',
    'test_consultations_simple.php',
    'debug_session.php'
];

$tests['files'] = [];
foreach ($files_to_test as $file) {
    $tests['files'][$file] = [
        'exists' => file_exists($file),
        'readable' => is_readable($file),
        'size' => file_exists($file) ? filesize($file) : 0
    ];
}

// Test 3: Vérifier la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=medicale", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $tables = ['consultations', 'prescriptions', 'medicaments', 'rendez_vous', 'patients', 'docteurs'];
    $tests['database'] = [];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            $tests['database'][$table] = [
                'exists' => true,
                'count' => $count
            ];
        } catch (Exception $e) {
            $tests['database'][$table] = [
                'exists' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
} catch (Exception $e) {
    $tests['database'] = [
        'connection_error' => $e->getMessage()
    ];
}

// Test 4: Vérifier les permissions
$tests['permissions'] = [
    'session_writeable' => is_writable(session_save_path()),
    'current_dir_writable' => is_writable('.'),
    'php_version' => PHP_VERSION
];

// Résumé
$tests['summary'] = [
    'total_files' => count($files_to_test),
    'files_exist' => count(array_filter($tests['files'], function($f) { return $f['exists']; })),
    'user_logged_in' => isset($_SESSION['user']),
    'user_role' => $_SESSION['user']['role'] ?? 'none'
];

echo json_encode($tests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?> 