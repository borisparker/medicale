<?php
require_once 'auth.php';
require_role('docteur');
require_once 'db.php';
require_once('fpdf/fpdf.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Ordonnance invalide.');
}
$prescription_id = intval($_GET['id']);
$user_id = $_SESSION['user']['id'];

// Vérifier que l'ordonnance appartient bien à ce docteur
$stmt = $pdo->prepare('SELECT p.*, c.type as consultation_type, c.motif, c.date_consultation, mr.patient_id, u.nom as patient_nom, u.prenom as patient_prenom FROM prescriptions p JOIN consultations c ON p.consultation_id = c.id JOIN medical_records mr ON c.medical_record_id = mr.id JOIN patients pt ON mr.patient_id = pt.id JOIN users u ON pt.user_id = u.id WHERE p.id = ?');
$stmt->execute([$prescription_id]);
$prescription = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$prescription) {
    die('Ordonnance introuvable.');
}
// Vérifier que le docteur connecté est bien le propriétaire
$stmt2 = $pdo->prepare('SELECT doctor_id FROM consultations WHERE id = ?');
$stmt2->execute([$prescription['consultation_id']]);
$doctor_id = $stmt2->fetchColumn();
$stmt3 = $pdo->prepare('SELECT id FROM doctors WHERE user_id = ?');
$stmt3->execute([$user_id]);
$my_doctor_id = $stmt3->fetchColumn();
if ($doctor_id != $my_doctor_id) {
    die('Accès refusé.');
}
// Récupérer les médicaments
$stmt = $pdo->prepare('SELECT m.nom, pm.quantite, pm.instructions FROM prescription_medications pm JOIN medications m ON pm.medication_id = m.id WHERE pm.prescription_id = ?');
$stmt->execute([$prescription_id]);
$meds = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Ordonnance',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Ln(5);
$pdf->Cell(0,8,'Patient : '.$prescription['patient_prenom'].' '.$prescription['patient_nom'],0,1);
$pdf->Cell(0,8,'Date : '.date('d/m/Y', strtotime($prescription['date_prescription'])),0,1);
$pdf->Cell(0,8,'Consultation : '.$prescription['consultation_type'].' - '.$prescription['motif'],0,1);
$pdf->Ln(5);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Médicaments prescrits :',0,1);
$pdf->SetFont('Arial','',12);
foreach($meds as $med) {
    $pdf->MultiCell(0,7,'- '.$med['nom'].' | '.$med['quantite'].' | '.$med['instructions']);
}
$pdf->Ln(5);
$pdf->SetFont('Arial','I',10);
$pdf->MultiCell(0,6,'Détails : '.($prescription['details'] ?? ''));
$pdf->Ln(10);
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,8,'Signature du médecin :',0,1);
$pdf->Ln(15);
$pdf->Cell(0,8,'_________________________',0,1);

$pdf->Output('I', 'Ordonnance_'.$prescription_id.'.pdf'); 