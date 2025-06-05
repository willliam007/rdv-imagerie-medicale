<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/guard.php';

require_role('patient');

$user_id = $_SESSION['user_id'] ?? null;

// Récupérer l'ID du patient
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
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Custom CSS -->
<link href="assets/styles.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="assets/logo.svg" alt="Logo" height="40" class="me-2">
            Imagerie Médicale
        </a>
        <div class="ms-auto">
            <a href="logout.php" class="btn btn-outline-secondary">Se déconnecter</a>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="mb-4">Mes Rendez-vous</h2>

    <?php if (empty($rendezvous)): ?>
        <div class="alert alert-info">Vous n'avez pas encore pris de rendez-vous.</div>
    <?php else: ?>
        <table class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Examen</th>
                    <th>Sous-type</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rendezvous as $rdv): ?>
                    <tr>
                        <td><?= htmlspecialchars($rdv['date']) ?></td>
                        <td><?= htmlspecialchars($rdv['heure_debut']) ?> - <?= htmlspecialchars($rdv['heure_fin']) ?></td>
                        <td><?= htmlspecialchars($rdv['examen_nom']) ?></td>
                        <td><?= htmlspecialchars($rdv['sous_type_nom']) ?></td>
                        <td>
                            <?php if ($rdv['statut'] === 'en_attente'): ?>
                                <span class="badge bg-warning text-dark">En attente</span>
                            <?php elseif ($rdv['statut'] === 'annule'): ?>
                                <span class="badge bg-danger">Annulé</span>
                            <?php else: ?>
                                <span class="badge bg-success">Validé</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($rdv['statut'] === 'en_attente'): ?>
                                <form method="POST" action="annuler-rdv.php" class="d-inline" onsubmit="return confirm('Confirmer l\'annulation ?');">
                                    <input type="hidden" name="rdv_id" value="<?= $rdv['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Annuler</button>
                                </form>

                                <form method="POST" action="upload-bulletin.php" enctype="multipart/form-data" class="d-inline">
                                    <input type="hidden" name="rdv_id" value="<?= $rdv['id'] ?>">
                                    <input type="file" name="bulletin" required class="form-control form-control-sm d-inline-block" style="width:auto;">
                                    <button type="submit" class="btn btn-sm btn-success">Uploader</button>
                                </form>
                            <?php elseif ($rdv['bulletin']): ?>
                                <a href="../uploads/<?= htmlspecialchars($rdv['bulletin']) ?>" target="_blank" class="btn btn-sm btn-info">Voir bulletin</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
