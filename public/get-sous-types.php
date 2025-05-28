<?php
require_once '../includes/db.php';

header("Content-Type: application/json");

if (isset($_GET['examen_id'])) {
    $examen_id = intval($_GET['examen_id']);
    $stmt = $pdo->prepare("SELECT id, nom FROM examens_sous_types WHERE examen_id = ? AND disponible = 1");
    $stmt->execute([$examen_id]);
    $sous_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($sous_types);
} else {
    echo json_encode([]);
}
