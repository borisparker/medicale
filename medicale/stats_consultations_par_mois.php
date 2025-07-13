<?php
require_once 'db.php';
header('Content-Type: application/json');

$year = date('Y');
// Adapter le nom de la table et de la colonne date si besoin
$stmt = $pdo->prepare("
  SELECT MONTH(date_consultation) as mois, COUNT(*) as total
  FROM consultations
  WHERE YEAR(date_consultation) = ?
  GROUP BY mois
  ORDER BY mois
");
$stmt->execute([$year]);
$data = array_fill(1, 12, 0);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $data[(int)$row['mois']] = (int)$row['total'];
}
echo json_encode([
  'success' => true,
  'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
  'data' => array_values($data)
]); 