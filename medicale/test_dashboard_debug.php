<?php
require_once 'auth.php';
require_once 'db.php';

$user_id = $_SESSION['user']['id'];
echo "<h3>User ID: $user_id</h3>";

$stmt = $pdo->prepare('SELECT id FROM patients WHERE user_id = ?');
$stmt->execute([$user_id]);
$patient = $stmt->fetch();
if (!$patient) {
    echo "Aucun patient trouvé pour user_id = $user_id<br>";
    exit;
}
$patient_id = $patient['id'];
echo "<h3>Patient ID: $patient_id</h3>";

$stmt = $pdo->prepare('SELECT id FROM medical_records WHERE patient_id = ?');
$stmt->execute([$patient_id]);
$medical_record = $stmt->fetch();
if (!$medical_record) {
    echo "Aucun dossier médical trouvé pour patient_id = $patient_id<br>";
    exit;
}
$medical_record_id = $medical_record['id'];
echo "<h3>Medical Record ID: $medical_record_id</h3>";

$count = $pdo->query("SELECT COUNT(*) FROM appointments WHERE patient_id = $patient_id")->fetchColumn();
echo "<b>Rendez-vous trouvés:</b> $count<br>";
$count = $pdo->query("SELECT COUNT(*) FROM consultations WHERE medical_record_id = $medical_record_id")->fetchColumn();
echo "<b>Consultations trouvées:</b> $count<br>";
$count = $pdo->query("SELECT COUNT(*) FROM prescriptions p INNER JOIN consultations c ON p.consultation_id = c.id WHERE c.medical_record_id = $medical_record_id")->fetchColumn();
echo "<b>Prescriptions trouvées:</b> $count<br>";
?> 