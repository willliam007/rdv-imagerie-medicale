<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_role('patient');

session_start();
$user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bulletin']) && isset($_POST['rdv_id'])) {
    $rdv_id = intval($_POST['rdv_id']);
    $file = $_FILES['bulletin'];

    // Vérifie la propriété du RDV
    $stmt = $pdo->prepare("
        SELECT r.id, r.patient_id
        FROM rendezvous r
        JOIN patients p ON r.patient_id = p.id
        WHERE r.id = ? AND p.user_id = ? AND r.statut = 'en_attente'
    ");
    $stmt->execute([$rdv_id, $user_id]);
    $rdv = $stmt->fetch();

    if ($rdv) {
        // Vérifie l'extension du fichier
        $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_extensions)) {
            die("Extension non autorisée.");
        }

        // Crée un nom unique
        $new_name = uniqid('bulletin_', true) . '.' . $ext;
        $destination = '../uploads/' . $new_name;

        // Déplace le fichier
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Sauvegarde en BDD
            $stmt = $pdo->prepare("UPDATE rendezvous SET bulletin = ? WHERE id = ?");
            $stmt->execute([$new_name, $rdv_id]);
        } else {
            die("Erreur lors de l'upload.");
        }
    }
}

// Redirige vers le dashboard
header("Location: dashboard.php");
exit;
