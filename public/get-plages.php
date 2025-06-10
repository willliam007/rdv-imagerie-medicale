<?php
require_once '../includes/db.php';
$date = $_GET['date'] ?? '';
if ($date) {
    $stmt = $pdo->prepare("SELECT * FROM plages_horaires WHERE quota_restant > 0 AND date = ? ORDER BY heure_debut");
    $stmt->execute([$date]);
    $plages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $plages = $pdo->query("SELECT * FROM plages_horaires WHERE quota_restant > 0 ORDER BY date, heure_debut")->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode($plages);