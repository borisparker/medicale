<?php
require_once 'auth.php';
require_once 'db.php';

// Vérifier que l'utilisateur est connecté (patient ou docteur)
if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
    http_response_code(403);
    echo 'Accès non autorisé';
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo 'ID d\'ordonnance manquant ou invalide';
    exit;
}
$prescription_id = intval($_GET['id']);

// Récupérer l'ordonnance et vérifier l'accès
$stmt = $pdo->prepare('SELECT p.*, c.date_consultation, c.type as consultation_type, CONCAT(u.nom, " ", u.prenom) as doctor_name, pt.id as patient_id, CONCAT(up.nom, " ", up.prenom) as patient_name FROM prescriptions p INNER JOIN consultations c ON p.consultation_id = c.id INNER JOIN doctors d ON p.doctor_id = d.id INNER JOIN users u ON d.user_id = u.id INNER JOIN patients pt ON p.patient_id = pt.id INNER JOIN users up ON pt.user_id = up.id WHERE p.id = ?');
$stmt->execute([$prescription_id]);
$prescription = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prescription) {
    http_response_code(404);
    echo 'Ordonnance introuvable';
    exit;
}

// Vérifier que le patient ou le médecin a le droit d'accéder à l'ordonnance
$user = $_SESSION['user'];
if ($user['role'] === 'patient') {
    // Le patient ne peut voir que ses propres ordonnances
    $stmt = $pdo->prepare('SELECT id FROM patients WHERE user_id = ?');
    $stmt->execute([$user['id']]);
    $patient = $stmt->fetch();
    if (!$patient || $patient['id'] != $prescription['patient_id']) {
        http_response_code(403);
        echo 'Accès refusé';
        exit;
    }
}
if ($user['role'] === 'docteur') {
    // Le médecin ne peut voir que ses propres ordonnances
    $stmt = $pdo->prepare('SELECT id FROM doctors WHERE user_id = ?');
    $stmt->execute([$user['id']]);
    $doctor = $stmt->fetch();
    if (!$doctor || $doctor['id'] != $prescription['doctor_id']) {
        http_response_code(403);
        echo 'Accès refusé';
        exit;
    }
}

// Récupérer les médicaments
$stmt = $pdo->prepare('SELECT m.nom, m.dosage, pm.quantite, pm.instructions FROM prescription_medications pm INNER JOIN medications m ON pm.medication_id = m.id WHERE pm.prescription_id = ?');
$stmt->execute([$prescription_id]);
$medications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Génération du PDF avec FPDF
$pdf_possible = false;

// Essayer de charger FPDF depuis le dossier fpdf/
if (file_exists(__DIR__ . '/fpdf/fpdf.php')) {
    require_once __DIR__ . '/fpdf/fpdf.php';
    if (class_exists('FPDF')) {
        $pdf_possible = true;
    }
} else if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    if (class_exists('FPDF')) {
        $pdf_possible = true;
    }
} else if (file_exists(__DIR__ . '/fpdf.php')) {
    require_once __DIR__ . '/fpdf.php';
    if (class_exists('FPDF')) {
        $pdf_possible = true;
    }
}

if ($pdf_possible) {
    class PrescriptionPDF extends FPDF {
        function __construct() {
            parent::__construct();
            $this->fontpath = __DIR__ . '/fpdf/font/';
        }
        function Header() {
            // Logo
            if (file_exists(__DIR__ . '/assets/images/logo.jpg')) {
                $this->Image(__DIR__ . '/assets/images/logo.jpg', 10, 8, 22);
            }
            $this->SetFont('Arial','B',16);
            $this->SetTextColor(33,37,41);
            $this->Cell(0,10,'VAIDYA MITRA',0,1,'C');
            $this->SetFont('Arial','',10);
            $this->SetTextColor(100,100,100);
            $this->Cell(0,6,'Douala',0,1,'C');
            $this->Cell(0,6,'Tél : 678760117 - Email : vaidya@gmail.com',0,1,'C');
            $this->Ln(2);
            $this->SetDrawColor(100, 100, 255);
            $this->Line(10, $this->GetY(), 200, $this->GetY());
            $this->Ln(6);
        }
        function Footer() {
            $this->SetY(-20);
            $this->SetFont('Arial','I',9);
            $this->SetTextColor(120,120,120);
            $this->Cell(0,10,'Ordonnance générée le '.date('d/m/Y'),0,0,'C');
        }
    }
    $pdf = new PrescriptionPDF();
    $pdf->AddPage();
    // Encadré infos patient
    $pdf->SetFillColor(230,240,255);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,8,'Patient',0,1,'L',true);
    $pdf->SetFont('Arial','',11);
    $pdf->Cell(0,6,'Nom : ' . $prescription['patient_name'],0,1);
    $pdf->Cell(0,6,'Date de naissance : ' . ($prescription['patient_birthdate'] ?? '-'),0,1);
    $pdf->Ln(2);
    // Encadré infos médecin
    $pdf->SetFillColor(240,230,255);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,8,'Médecin',0,1,'L',true);
    $pdf->SetFont('Arial','',11);
    $pdf->Cell(0,6,'Dr. ' . $prescription['doctor_name'],0,1);
    $pdf->Cell(0,6,'Consultation : ' . $prescription['consultation_type'] . ' du ' . date('d/m/Y', strtotime($prescription['date_consultation'])),0,1);
    $pdf->Ln(4);
    // Tableau des médicaments
    $pdf->SetFillColor(220,220,220);
    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(60,8,'Médicament',1,0,'C',true);
    $pdf->Cell(40,8,'Dosage',1,0,'C',true);
    $pdf->Cell(30,8,'Quantité',1,0,'C',true);
    $pdf->Cell(60,8,'Instructions',1,1,'C',true);
    $pdf->SetFont('Arial','',11);
    foreach ($medications as $med) {
        $pdf->Cell(60,8,$med['nom'],1);
        $pdf->Cell(40,8,$med['dosage'],1);
        $pdf->Cell(30,8,$med['quantite'],1);
        $pdf->Cell(60,8,$med['instructions'],1,1);
    }
    $pdf->Ln(8);
    // Instructions générales
    if (!empty($prescription['details'])) {
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,10,'Instructions complémentaires :',0,1);
        $pdf->SetFont('Arial','',11);
        $pdf->MultiCell(0,8,$prescription['details']);
    }
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="ordonnance_'.$prescription_id.'.pdf"');
    $pdf->Output('D','ordonnance_'.$prescription_id.'.pdf');
    exit;
} else {
    // Fallback : génération d'un fichier texte simple
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="ordonnance_'.$prescription_id.'.txt"');
    echo "Ordonnance\n";
    echo "Patient : " . $prescription['patient_name'] . "\n";
    echo "Médecin : Dr. " . $prescription['doctor_name'] . "\n";
    echo "Date : " . date('d/m/Y', strtotime($prescription['date_creation'])) . "\n";
    echo "Consultation : " . $prescription['consultation_type'] . " du " . date('d/m/Y', strtotime($prescription['date_consultation'])) . "\n";
    echo "\nMédicaments :\n";
    foreach ($medications as $med) {
        echo '- ' . $med['nom'] . ' (' . $med['dosage'] . ') x' . $med['quantite'] . ' : ' . $med['instructions'] . "\n";
    }
    if (!empty($prescription['details'])) {
        echo "\nInstructions :\n" . $prescription['details'] . "\n";
    }
    exit;
} 