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

// Récupérer les examens
$examens = $pdo->query("SELECT * FROM examens")->fetchAll(PDO::FETCH_ASSOC);

// Gestion du filtre de date
$date = $_POST['date'] ?? '';
if (isset($_POST['filtrer'])) {
    // Si on filtre, on recharge la page avec la date sélectionnée
    $date = $_POST['date'] ?? '';
}

// Récupérer les plages horaires filtrées par date si une date est sélectionnée
if ($date) {
    $stmt = $pdo->prepare("SELECT * FROM plages_horaires WHERE quota_restant > 0 AND date = ? ORDER BY heure_debut");
    $stmt->execute([$date]);
    $plages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $plages = $pdo->query("SELECT * FROM plages_horaires WHERE quota_restant > 0 ORDER BY date, heure_debut")->fetchAll(PDO::FETCH_ASSOC);
}

$errors = [];
$success = false;

if (isset($_POST['prendre_rdv'])) {
    $sous_type_id = $_POST['sous_type_id'] ?? null;
    $plage_id = $_POST['plage_id'] ?? null;

    if ($sous_type_id && $plage_id && $patient_id) {
        // Récupérer la date et l'heure de la plage sélectionnée
        $stmt = $pdo->prepare("SELECT date, heure_debut, heure_fin FROM plages_horaires WHERE id = ?");
        $stmt->execute([$plage_id]);
        $plage_sel = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifier si le patient a déjà un RDV à la même date ET même heure
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM rendezvous r JOIN plages_horaires p ON r.plage_id = p.id WHERE r.patient_id = ? AND p.date = ? AND p.heure_debut = ? AND p.heure_fin = ?");
        $stmt->execute([$patient_id, $plage_sel['date'], $plage_sel['heure_debut'], $plage_sel['heure_fin']]);
        $existe = $stmt->fetchColumn();

        if ($existe > 0) {
            $errors[] = "Vous avez déjà un rendez-vous pour ce créneau horaire.";
        } else {
            try {
                $pdo->beginTransaction();
                // Insertion du RDV
                $stmt = $pdo->prepare("INSERT INTO rendezvous (patient_id, sous_type_id, plage_id) VALUES (?, ?, ?)");
                $stmt->execute([$patient_id, $sous_type_id, $plage_id]);
                // Décrémenter le quota
                $stmt = $pdo->prepare("UPDATE plages_horaires SET quota_restant = quota_restant - 1 WHERE id = ? AND quota_restant > 0");
                $stmt->execute([$plage_id]);
                $pdo->commit();
                $success = true;
            } catch (Exception $e) {
                $pdo->rollBack();
                $errors[] = "Erreur lors de la prise de rendez-vous : " . $e->getMessage();
            }
        }
    } else {
        $errors[] = "Tous les champs sont requis.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Prise de rendez-vous - Imagerie Médicale</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="assets/logo.svg" alt="Logo" height="40" class="me-2">
            Easy ImAgInG
        </a>
        <div class="ms-auto">
            <a href="dashboard.php" class="btn btn-outline-secondary me-2"><i class="fas fa-columns"></i> Tableau de bord</a>
            <a href="profil.php" class="btn btn-outline-primary me-2"><i class="fas fa-user"></i> Mon Profil</a>
            <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="mb-4">Prendre un rendez-vous</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">Rendez-vous enregistré avec succès.</div>
    <?php endif; ?>
    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <!-- Formulaire de filtre de date -->
    <!-- <form method="POST" class="mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-0">Choisir une date</label>
                <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>">
            </div>
            <div class="col-auto">
                <button type="submit" name="filtrer" class="btn btn-secondary">Filtrer les créneaux</button>
            </div>
        </div>
    </form> -->

    <!-- Formulaire de prise de rendez-vous -->
    <form method="POST" class="p-4 border rounded bg-white shadow-sm">
        <div class="mb-3">
            <label class="form-label">Type d'examen</label>
            <select name="examen_id" id="examen_id" class="form-select" required>
                <option value="">-- Sélectionner --</option>
                <?php foreach ($examens as $examen): ?>
                    <option value="<?= $examen['id'] ?>"><?= htmlspecialchars($examen['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Sous-type d'examen</label>
            <select name="sous_type_id" id="sous_type_id" class="form-select" required>
                <option value="">-- Sélectionner un type d'abord --</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Choisir une date</label>
            <input type="date" name="date" id="date_filter" class="form-control" value="<?= htmlspecialchars($date) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Plage horaire</label>
            <select name="plage_id" id="plage_id" class="form-select" required>
                <option value="">-- Sélectionner --</option>
                <?php foreach ($plages as $plage): ?>
                    <?php $label = $plage['date'] . ' | ' . $plage['heure_debut'] . ' - ' . $plage['heure_fin']; ?>
                    <option value="<?= $plage['id'] ?>"><?= htmlspecialchars($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" name="prendre_rdv" class="btn btn-primary w-100"><i class="fas fa-calendar-plus"></i> Prendre rendez-vous</button>
    </form>
</div>

<footer class="bg-primary text-white text-center py-3 mt-auto">
    &copy; 2025 Imagerie Médicale. Tous droits réservés. Réalisé par @william.
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('examen_id').addEventListener('change', function() {
        const examenId = this.value;
        const sousTypeSelect = document.getElementById('sous_type_id');
        sousTypeSelect.innerHTML = '<option value="">Chargement...</option>';
        fetch('./get-sous-types.php?examen_id=' + examenId)
            .then(response => response.json())
            .then(data => {
                sousTypeSelect.innerHTML = '<option value="">-- Sélectionner --</option>';
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nom;
                    sousTypeSelect.appendChild(option);
                });
            })
            .catch(error => {
                sousTypeSelect.innerHTML = '<option value="">Erreur lors du chargement</option>';
                console.error('Erreur AJAX :', error);
            });
    });
});
</script>
</body>
</html>
fetch('get-plages.php?date=' + encodeURIComponent(date))


