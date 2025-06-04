<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_role('patient');

session_start();
$user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rdv_id'])) {
    $rdv_id = intval($_POST['rdv_id']);

    // 1. Vérifie si le RDV appartient bien à ce patient et est annulable
    $stmt = $pdo->prepare("
        SELECT r.id, r.patient_id, r.plage_id
        FROM rendezvous r
        JOIN patients p ON r.patient_id = p.id
        WHERE r.id = ? AND p.user_id = ? AND r.statut = 'en_attente'
    ");
    $stmt->execute([$rdv_id, $user_id]);
    $rdv = $stmt->fetch();

    if ($rdv) {
        try {
            $pdo->beginTransaction();

            // 2. Met à jour le statut du rendez-vous
            $stmt = $pdo->prepare("UPDATE rendezvous SET statut = 'annule' WHERE id = ?");
            $stmt->execute([$rdv_id]);

            // 3. Ré-incrémente le quota
            $stmt = $pdo->prepare("UPDATE plages_horaires SET quota_restant = quota_restant + 1 WHERE id = ?");
            $stmt->execute([$rdv['plage_id']]);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Erreur lors de l'annulation : " . $e->getMessage());
        }
    }
}

// Redirection vers le dashboard
header("Location: dashboard.php");
exit;
