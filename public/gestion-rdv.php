<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/guard.php';

require_role('secretaire');
// Filtrer les RDV par statut
$statut = isset($_GET['statut']) ? $_GET['statut'] : 'en_attente';

// Récupérer les rendez-vous en attente
$stmt = $pdo->query("
    SELECT r.*, p.nom AS patient_nom, p.prenom AS patient_prenom, e.nom AS examen_nom, s.nom AS sous_type_nom, ph.date, ph.heure_debut, ph.heure_fin
    FROM rendezvous r
    JOIN patients p ON r.patient_id = p.id
    JOIN examens_sous_types s ON r.sous_type_id = s.id
    JOIN examens e ON s.examen_id = e.id
    JOIN plages_horaires ph ON r.plage_id = ph.id
    WHERE r.statut IN ('en_attente', 'valide','paye') 
    ORDER BY ph.date, ph.heure_debut ASC
");
$rendezvous = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement validation ou rejet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rdv_id = $_POST['rdv_id'] ?? null;
    $action = $_POST['action'] ?? null;
    $commentaire = $_POST['commentaire'] ?? null;

    if ($rdv_id && $action && in_array($action, ['valide', 'rejete'])) {
        $stmt = $pdo->prepare("UPDATE rendezvous SET statut = ?, commentaire = ? WHERE id = ?");
        $stmt->execute([$action, $commentaire, $rdv_id]);

        // Notifier le patient
        $stmt = $pdo->prepare("SELECT patient_id FROM rendezvous WHERE id = ?");
        $stmt->execute([$rdv_id]);
        $patient_id = $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT user_id FROM patients WHERE id = ?");
        $stmt->execute([$patient_id]);
        $user_id = $stmt->fetchColumn();

        if ($action === 'valide') {
            ajouter_notification($user_id, "Votre rendez-vous a été validé. Merci de procéder au paiement.");
        } elseif ($action === 'rejete') {
            ajouter_notification($user_id, "Votre rendez-vous a été rejeté. Raison : $commentaire");
        }

        header("Location: gestion-rdv.php");
        exit;
    }
}

// valider paiement 
$rdv_id = $_POST['rdv_id'] ?? null;

function valider_paiement($rdv_id) 
{
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

    header("Location: gestion-rdv.php");
    exit;
}else {
    echo "Erreur : ID de rendez-vous manquant.";
}

}





?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des RDV - Imagerie Médicale</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
  <div class="container">
    <a class="navbar-brand" href="#">
      <img src="assets/logo.svg" alt="Logo" height="40" class="me-2">
      Imagerie Médicale
    </a>
    <div class="ms-auto">
      <a href="dashboard.php" class="btn btn-outline-secondary me-2"><i class="fas fa-columns"></i> Tableau de bord</a>
      <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>
  </div>
</nav>

<div class="container">
  <h2 class="mb-4">Gestion des rendez-vous en attente</h2>

  <?php if (empty($rendezvous)): ?>
    <div class="alert alert-info">Aucun rendez-vous en attente actuellement.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead class="table-primary">
          <tr>
            <th>Patient</th>
            <th>Examen</th>
            <th>Sous-type</th>
            <th>Date</th>
            <th>Heure</th>
            <th>Bulletin</th>
            <th>Paiement</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
  <?php foreach ($rendezvous as $rdv): ?>
    <tr>
      <td><?= htmlspecialchars($rdv['patient_nom'] . ' ' . $rdv['patient_prenom']) ?></td>
      <td><?= htmlspecialchars($rdv['examen_nom']) ?></td>
      <td><?= htmlspecialchars($rdv['sous_type_nom']) ?></td>
      <td><?= htmlspecialchars($rdv['date']) ?></td>
      <td><?= htmlspecialchars($rdv['heure_debut']) ?> - <?= htmlspecialchars($rdv['heure_fin']) ?></td>
      <td>
        <?php if ($rdv['bulletin']): ?>
          <a href="../uploads/<?= htmlspecialchars($rdv['bulletin']) ?>" target="_blank" class="btn btn-sm btn-info">
            <i class="fas fa-eye"></i> Voir
          </a>
        <?php else: ?>
          <span class="text-muted">Aucun</span>
        <?php endif; ?>
      </td>
      <td>
        <?php if ($rdv['paiement'] === 'paye'): ?>
          <!-- <span class="badge bg-success">Payé</span> -->
          <form method="POST" action="valider-paiement.php" class="d-inline">
              <input type="hidden" name="rdv_id" value="<?= $rdv['id'] ?>">
              <button type="submit" class="btn btn-success btn-sm">
                <i class="fas fa-check"></i> Paiement reçu </button>

            </form>

        <?php elseif ($rdv['paiement'] === 'en_attente'): ?>
          <span class="badge bg-warning text-dark">En attente</span>

          <?php if ($rdv['paiement'] === 'paye'): ?>
            <form method="POST" action="valider-paiement.php" class="d-inline">
              <input type="hidden" name="rdv_id" value="<?= $rdv['id'] ?>">
              <button type="submit" class="btn btn-success btn-sm">
                <i class="fas fa-check"></i> Paiement reçu
              </button>
            </form>
          <?php endif; ?>

        <?php elseif ($rdv['paiement'] === 'non_payé'): ?>
          <span class="badge bg-danger">Non payé</span>
        <?php endif; ?>
      </td>
      <td>
  <?php if ($rdv['statut'] === 'en_attente'): ?>
    <form method="POST" class="d-inline">
      <input type="hidden" name="rdv_id" value="<?= $rdv['id'] ?>">
      <input type="hidden" name="action" value="valide">
      <button type="submit" class="btn btn-success btn-sm me-1">
        <i class="fas fa-check"></i> Valider
      </button>
    </form>
    <form method="POST" class="d-inline">
      <input type="hidden" name="rdv_id" value="<?= $rdv['id'] ?>">
      <input type="hidden" name="action" value="rejete">
      <input type="text" name="commentaire" placeholder="Raison du rejet" class="form-control form-control-sm d-inline-block w-auto me-1" required>
      <button type="submit" class="btn btn-danger btn-sm">
        <i class="fas fa-times"></i> Rejeter
      </button>
    </form>
  <?php else: ?>
    <span class="badge bg-success">Validé</span>
  <?php endif; ?>
</td>

    </tr>
  <?php endforeach; ?>
</tbody>

      </table>
    </div>
  <?php endif; ?>
</div>

<footer class="bg-primary text-white text-center py-3 mt-auto">
  &copy; 2025 Imagerie Médicale. Tous droits réservés. Réalisé par @william.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
