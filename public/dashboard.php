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

// RDV actifs
$stmt = $pdo->prepare("
    SELECT r.*, s.nom AS sous_type_nom, e.nom AS examen_nom, p.date, p.heure_debut, p.heure_fin
    FROM rendezvous r
    JOIN examens_sous_types s ON r.sous_type_id = s.id
    JOIN examens e ON s.examen_id = e.id
    JOIN plages_horaires p ON r.plage_id = p.id
    WHERE r.patient_id = ?
      AND r.statut IN ('en_attente', 'valide')
    ORDER BY p.date, p.heure_debut
");
$stmt->execute([$patient_id]);
$rendezvous = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les notifications non lues
$notifications = get_notifications($user_id, true);
// Historique des RDV
$stmt = $pdo->prepare("
    SELECT r.*, s.nom AS sous_type_nom, e.nom AS examen_nom, p.date, p.heure_debut, p.heure_fin
    FROM rendezvous r
    JOIN examens_sous_types s ON r.sous_type_id = s.id
    JOIN examens e ON s.examen_id = e.id
    JOIN plages_horaires p ON r.plage_id = p.id
    WHERE r.patient_id = ?
      AND r.statut IN ('termine', 'rejete', 'annule')
    ORDER BY p.date DESC, p.heure_debut
");
$stmt->execute([$patient_id]);
$historique_rdv = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Mon Tableau de Bord</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/styles.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="#"><img src="assets/logo.svg" alt="Logo" height="40" class="me-2">Easy Imagerie</a>
    <div class="ms-auto">
      <a href="index.php" class="btn btn-outline-primary me-2"><i class="fas fa-home"></i> Accueil </a>
      <a href="profil.php" class="btn btn-outline-primary me-2"><i class="fas fa-user"></i> Mon Profil </a>
      <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>
  </div>
</nav>

<div class="container">
  <h2 class="mb-4">Mes Rendez-vous</h2>

  <?php if (!empty($notifications)): ?>
    <div class="alert alert-info">
        <h5>Notifications :</h5>
        <ul class="mb-0">
            <?php foreach ($notifications as $notif): ?>
                <li><?= htmlspecialchars($notif['message']) ?> (<?= htmlspecialchars($notif['date_creation']) ?>)</li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php
    // Marquer les notifications comme lues après affichage
    marquer_notifications_lues($user_id);
    ?>
<?php endif; ?>


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
          <th>Paiement</th>
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
            <?php elseif ($rdv['statut'] === 'rejete'): ?>
              <span class="badge bg-danger">Rejeté</span><br>
              <small class="text-muted"><?= htmlspecialchars($rdv['commentaire']) ?></small>
            <?php else: ?>
              <span class="badge bg-success">Validé</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($rdv['paiement'] === 'non_payé' && $rdv['statut'] === 'valide'): ?>
              <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#paiementModal" data-rdv="<?= $rdv['id'] ?>">Effectuer paiement</button>
            <?php elseif ($rdv['paiement'] === 'en_attente'): ?>
              <span class="badge bg-warning text-dark">En attente validation</span>
            <?php elseif ($rdv['paiement'] === 'paye'): ?>
              <span class="badge bg-success">Payé</span>
            <?php else: ?>
              -
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
                    <button type="submit" class="btn btn-sm btn-success">telecharger le bulletin</button>
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

  <div class="text-center mt-4">
    <a href="prise-rdv.php" class="btn btn-primary">Prendre un rendez-vous</a>
  </div>
</div>

<hr class="my-5">
<h3>Historique de mes Rendez-vous</h3>
  <?php if (empty($historique_rdv)): ?>
    <div class="alert alert-light">Aucun historique disponible.</div>
  <?php else: ?>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Date</th>
          <th>Heure</th>
          <th>Examen</th>
          <th>Sous-type</th>
          <th>Statut</th>
          <th>Commentaire</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($historique_rdv as $histo): ?>
        <tr>
          <td><?= htmlspecialchars($histo['date']) ?></td>
          <td><?= htmlspecialchars($histo['heure_debut']) ?> - <?= htmlspecialchars($histo['heure_fin']) ?></td>
          <td><?= htmlspecialchars($histo['examen_nom']) ?></td>
          <td><?= htmlspecialchars($histo['sous_type_nom']) ?></td>
          <td>
            <?php if ($histo['statut'] === 'termine'): ?>
              <span class="badge bg-success">Terminé</span>
            <?php elseif ($histo['statut'] === 'rejete'): ?>
              <span class="badge bg-danger">Rejeté</span>
            <?php elseif ($histo['statut'] === 'annule'): ?>
              <span class="badge bg-secondary">Annulé</span>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($histo['commentaire'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<!-- 
<?php if (empty($historique_rdv)): ?>
    <div class="alert alert-light">Aucun historique disponible.</div>
  <?php else: ?>
    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>Date</th>
          <th>Heure</th>
          <th>Examen</th>
          <th>Sous-type</th>
          <th>Statut</th>
          <th>Commentaire</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($historique_rdv as $histo): ?>
        <tr>
          <td><?= htmlspecialchars($histo['date']) ?></td>
          <td><?= htmlspecialchars($histo['heure_debut']) ?> - <?= htmlspecialchars($histo['heure_fin']) ?></td>
          <td><?= htmlspecialchars($histo['examen_nom']) ?></td>
          <td><?= htmlspecialchars($histo['sous_type_nom']) ?></td>
          <td>
            <?php if ($histo['statut'] === 'termine'): ?>
              <span class="badge bg-success">Terminé</span>
            <?php elseif ($histo['statut'] === 'rejete'): ?>
              <span class="badge bg-danger">Rejeté</span>
            <?php elseif ($histo['statut'] === 'annule'): ?>
              <span class="badge bg-secondary">Annulé</span>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($histo['commentaire'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?> -->

<!-- Modal pour paiement -->
<div class="modal fade" id="paiementModal" tabindex="-1" aria-labelledby="paiementModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="payer.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="paiementModalLabel">Effectuer le paiement</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Veuillez transférer le montant de la consultation vers le numéro <strong>+237671146081</strong> (NJINKEU SYMPHONIE).</p>
          <p>Une fois le transfert effectué, cliquez sur "J'ai payé" pour informer le secrétariat.</p>
          <p class="text-muted">Le secrétariat validera votre paiement dès réception.</p> <!-- 👈 Ici le paragraphe rassurant ajouté -->
          <input type="hidden" name="rdv_id" id="rdvIdInput">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">J'ai payé</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const paiementModal = document.getElementById('paiementModal');
  paiementModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    const rdvId = button.getAttribute('data-rdv');
    document.getElementById('rdvIdInput').value = rdvId;
  });
</script>
</body>
</html>
