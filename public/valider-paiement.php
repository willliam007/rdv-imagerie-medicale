<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/guard.php';
require_once '../includes/auth.php';

require_role('secretaire');

$rdv_id = $_POST['rdv_id'] ?? null;

if ($rdv_id) {
    $stmt = $pdo->prepare("UPDATE rendezvous SET paiement = 'paye' WHERE id = ?");
    $stmt->execute([$rdv_id]);

    // Tu peux aussi notifier le patient si tu veux :
    $stmt = $pdo->prepare("SELECT patient_id FROM rendezvous WHERE id = ?");
    $stmt->execute([$rdv_id]);
    $patient_id = $stmt->fetchColumn();
    $stmt = $pdo->prepare("SELECT user_id FROM patients WHERE id = ?");
    $stmt->execute([$patient_id]);
    $user_id = $stmt->fetchColumn();
    ajouter_notification($user_id, "Votre paiement a été validé. Merci !");

    header("Location:gestion-rdv.php");
    exit;
}else {
    echo "Erreur : ID de rendez-vous manquant.";
}
?>
