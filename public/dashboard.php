<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/guard.php';

session_start();
require_role('patient');




if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer l’ID du patient
$stmt = $pdo->prepare("SELECT id FROM patients WHERE user_id = ?");
$stmt->execute([$user_id]);
$patient_id = $stmt->fetchColumn();

// Récupérer les rendez-vous du patient avec infos liées
$stmt = $pdo->prepare("
    SELECT r.*, s.nom AS sous_type_nom, e.nom AS examen_nom, p.date, p.heure_debut, p.heure_fin
    FROM rendezvous r
    JOIN examens_sous_types s ON r.sous_type_id = s.id
    JOIN examens e ON s.examen_id = e.id
    JOIN plages_horaires p ON r.plage_id = p.id
    WHERE r.patient_id = ?
    ORDER BY p.date, p.heure_debut
");
$stmt->execute([$patient_id]);
$rendezvous = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Tableau de Bord</title>
    <div>
    <p><a href="logout.php">Se déconnecter</a></p>
    </div>
</head>
<body>
    <h2>Mes Rendez-vous</h2>

    <?php if (empty($rendezvous)): ?>
        <p>Vous n'avez pas encore pris de rendez-vous.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Examen</th>
                    <th>Sous-type</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rendezvous as $rdv): ?>
                    <tr>
                        <td><?= htmlspecialchars($rdv['date']) ?></td>
                        <td><?= htmlspecialchars($rdv['heure_debut']) ?> - <?= htmlspecialchars($rdv['heure_fin']) ?></td>
                        <td><?= htmlspecialchars($rdv['examen_nom']) ?></td>
                        <td><?= htmlspecialchars($rdv['sous_type_nom']) ?></td>
                        <td><?= htmlspecialchars($rdv['statut']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
