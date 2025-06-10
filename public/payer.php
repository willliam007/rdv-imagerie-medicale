<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/guard.php';
require_role('patient');

$rdv_id = $_POST['rdv_id'] ?? null;

if ($rdv_id) {
    $stmt = $pdo->prepare("UPDATE rendezvous SET paiement = 'paye' WHERE id = ?");
    $stmt->execute([$rdv_id]);

    // Optionnel: ajouter une notification au secrétaire
    
}

header("Location: dashboard.php");
exit;
?>
