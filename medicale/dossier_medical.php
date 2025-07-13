<?php
require_once 'auth.php';
require_role('docteur');
require_once 'db.php';

// Récupérer l'ID du patient depuis l'URL
$patient_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0;
if ($patient_id <= 0) {
    echo '<h2>Patient invalide.</h2>';
    exit;
}

// Récupérer les infos du patient
$stmt = $pdo->prepare('SELECT u.nom, u.prenom, u.date_naissance, u.sexe, u.telephone, u.photo, p.groupe_sanguin, p.allergies FROM patients p INNER JOIN users u ON p.user_id = u.id WHERE p.id = ?');
$stmt->execute([$patient_id]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$patient) {
    echo '<h2>Patient introuvable.</h2>';
    exit;
}

// Récupérer les consultations
$stmt = $pdo->prepare('SELECT c.id, c.date_consultation, c.type, c.motif, c.observations FROM consultations c INNER JOIN medical_records mr ON c.medical_record_id = mr.id WHERE mr.patient_id = ? ORDER BY c.date_consultation DESC');
$stmt->execute([$patient_id]);
$consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les ordonnances
$stmt = $pdo->prepare('SELECT p.id, p.date_creation, p.details FROM prescriptions p INNER JOIN consultations c ON p.consultation_id = c.id INNER JOIN medical_records mr ON c.medical_record_id = mr.id WHERE mr.patient_id = ? ORDER BY p.date_creation DESC');
$stmt->execute([$patient_id]);
$prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les médicaments pour chaque ordonnance
foreach ($prescriptions as &$prescription) {
    $stmt = $pdo->prepare("SELECT m.nom, m.dci, m.forme, m.dosage, pm.quantite, pm.instructions FROM prescription_medications pm INNER JOIN medications m ON pm.medication_id = m.id WHERE pm.prescription_id = ?");
    $stmt->execute([$prescription['id']]);
    $prescription['medications'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function calculAge($date_naissance) {
    if (!$date_naissance) return '-';
    $birth = new DateTime($date_naissance);
    $today = new DateTime();
    $age = $today->format('Y') - $birth->format('Y');
    if ($today->format('md') < $birth->format('md')) {
        $age--;
    }
    return $age;
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dossier médical de <?php echo htmlspecialchars($patient['nom'] . ' ' . $patient['prenom']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .timeline {
            position: relative;
            margin-left: 1.5rem;
        }
        .timeline:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #6366f1;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }
        .timeline-dot {
            position: absolute;
            left: -1.1rem;
            top: 0.5rem;
            width: 1.25rem;
            height: 1.25rem;
            background: #fff;
            border: 3px solid #6366f1;
            border-radius: 9999px;
            z-index: 10;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 to-white min-h-screen font-sans">
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur shadow p-4 flex items-center gap-4">
        <a href="docteur.php" class="text-indigo-600 hover:text-indigo-800 text-2xl"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-2xl md:text-3xl font-extrabold text-indigo-800 tracking-tight flex items-center gap-2">
            <i class="fas fa-notes-medical text-indigo-500"></i>
            Dossier médical de <?php echo htmlspecialchars($patient['nom'] . ' ' . $patient['prenom']); ?>
        </h1>
    </header>
    <main class="max-w-4xl mx-auto mt-8 mb-12 bg-white rounded-2xl shadow-2xl p-8">
        <section class="flex flex-col md:flex-row items-center md:items-start gap-8 mb-10">
            <img src="<?php echo htmlspecialchars(!empty($patient['photo']) ? '/medicale/uploads/' . basename($patient['photo']) : '/medicale/assets/images/default-user.png'); ?>" class="w-28 h-28 rounded-full border-4 border-indigo-200 shadow-lg object-cover" alt="Photo patient">
            <div class="flex-1 space-y-2">
                <h2 class="text-xl font-bold text-indigo-700 flex items-center gap-2"><i class="fas fa-user"></i> <?php echo htmlspecialchars($patient['nom'] . ' ' . $patient['prenom']); ?></h2>
                <div class="flex flex-wrap gap-3 text-gray-700 text-sm">
                    <span class="inline-flex items-center gap-1"><i class="fas fa-birthday-cake text-pink-400"></i> <?php echo htmlspecialchars($patient['date_naissance']); ?> (<?php echo calculAge($patient['date_naissance']); ?> ans)</span>
                    <span class="inline-flex items-center gap-1"><i class="fas fa-venus-mars text-blue-400"></i> <?php echo htmlspecialchars($patient['sexe']); ?></span>
                    <span class="inline-flex items-center gap-1"><i class="fas fa-phone text-green-400"></i> <?php echo htmlspecialchars($patient['telephone']); ?></span>
                </div>
                <div class="flex flex-wrap gap-3 text-gray-700 text-sm mt-2">
                    <span class="inline-flex items-center gap-1"><i class="fas fa-tint text-red-400"></i> Groupe sanguin : <span class="font-semibold ml-1"><?php echo htmlspecialchars($patient['groupe_sanguin']); ?></span></span>
                    <span class="inline-flex items-center gap-1"><i class="fas fa-allergies text-yellow-400"></i> Allergies : <span class="font-semibold ml-1"><?php echo htmlspecialchars($patient['allergies'] ?: 'Aucune'); ?></span></span>
                </div>
            </div>
        </section>
        <section class="mb-12">
            <h3 class="text-xl font-bold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-stethoscope"></i> Consultations</h3>
            <?php if (count($consultations) > 0): ?>
                <ol class="timeline">
                    <?php foreach ($consultations as $c): ?>
                        <li class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="ml-8 p-4 bg-indigo-50 rounded-xl shadow-sm">
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="font-semibold text-indigo-800"><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($c['date_consultation']); ?></span>
                                    <span class="ml-2 px-2 py-1 rounded-full bg-indigo-200 text-indigo-800 text-xs font-bold"><i class="fas fa-notes-medical"></i> <?php echo htmlspecialchars($c['type']); ?></span>
                                </div>
                                <div class="text-gray-700 text-sm mb-1"><span class="font-semibold">Motif :</span> <?php echo htmlspecialchars($c['motif']); ?></div>
                                <div class="text-gray-600 text-xs"><span class="font-semibold">Observations :</span> <?php echo nl2br(htmlspecialchars($c['observations'])); ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php else: ?>
                <p class="text-gray-500 mb-6">Aucune consultation trouvée.</p>
            <?php endif; ?>
        </section>
        <section>
            <h3 class="text-xl font-bold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-prescription-bottle-alt"></i> Ordonnances</h3>
            <?php if (count($prescriptions) > 0): ?>
                <ol class="timeline">
                    <?php foreach ($prescriptions as $p): ?>
                        <li class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="ml-8 p-4 bg-green-50 rounded-xl shadow-sm">
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="font-semibold text-green-800"><i class="fas fa-calendar-check"></i> <?php echo htmlspecialchars($p['date_creation']); ?></span>
                                </div>
                                <div class="text-gray-700 text-sm mb-1"><span class="font-semibold">Détails :</span> <?php echo nl2br(htmlspecialchars($p['details'])); ?></div>
                                <?php if (!empty($p['medications'])): ?>
                                    <div class="mt-2">
                                        <span class="font-semibold">Médicaments :</span>
                                        <ul class="list-disc ml-6">
                                            <?php foreach ($p['medications'] as $med): ?>
                                                <li class="mb-1">
                                                    <span class="font-medium text-green-900"><?php echo htmlspecialchars($med['nom']); ?></span>
                                                    <?php if ($med['dci']) echo ' ('.htmlspecialchars($med['dci']).')'; ?>
                                                    <?php if ($med['forme']) echo ', '.htmlspecialchars($med['forme']); ?>
                                                    <?php if ($med['dosage']) echo ', '.htmlspecialchars($med['dosage']); ?>
                                                    <span class="inline-block ml-2 px-2 py-0.5 rounded bg-green-200 text-green-900 text-xs">Quantité : <?php echo htmlspecialchars($med['quantite']); ?></span>
                                                    <?php if ($med['instructions']) echo '<span class=\'ml-2 italic text-gray-600\'>'.htmlspecialchars($med['instructions']).'</span>'; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php else: ?>
                <p class="text-gray-500">Aucune ordonnance trouvée.</p>
            <?php endif; ?>
        </section>
        <div class="mt-12 text-center">
            <a href="docteur.php" class="inline-block px-6 py-2 rounded-lg bg-indigo-600 text-white font-bold shadow hover:bg-indigo-700 transition"><i class="fas fa-arrow-left mr-2"></i>Retour à la liste des patients</a>
        </div>
    </main>
</body>
</html> 