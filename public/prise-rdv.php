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

// Récupérer les plages horaires disponibles
$plages = $pdo->query("SELECT * FROM plages_horaires WHERE quota_restant > 0 ORDER BY date, heure_debut")->fetchAll(PDO::FETCH_ASSOC);

// Gestion du formulaire
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sous_type_id = $_POST['sous_type_id'] ?? null;
    $plage_id = $_POST['plage_id'] ?? null;

    if ($sous_type_id && $plage_id && $patient_id) {
        // Vérifier si le patient a déjà un RDV sur cette plage
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM rendezvous WHERE patient_id = ? AND plage_id = ?");
        $stmt->execute([$patient_id, $plage_id]);
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
    <title>Prise de rendez-vous</title>
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
                    sousTypeSelect.innerHTML = '<option value="">Erreur AJAX</option>';
                    console.error('Erreur AJAX :', error);
                });
        });
    });
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Prise de rendez-vous</h2>

        <?php if ($success): ?>
            <p style="color: green;">Rendez-vous enregistré avec succès.</p>
        <?php endif; ?>

        <?php foreach ($errors as $error): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>

        <form method="POST">
            <label>Type d'examen :</label><br>
            <select name="examen_id" id="examen_id" required>
                <option value="">-- Sélectionner --</option>
                <?php foreach ($examens as $examen): ?>
                    <option value="<?= $examen['id'] ?>"><?= htmlspecialchars($examen['nom']) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label>Sous-type d'examen :</label><br>
            <select name="sous_type_id" id="sous_type_id" required>
                <option value="">-- Sélectionner un type d'abord --</option>
            </select><br><br>

            <label>Plage horaire :</label><br>
            <select name="plage_id" required>
                <option value="">-- Sélectionner --</option>
                <?php foreach ($plages as $plage): ?>
                    <?php $label = $plage['date'] . ' | ' . $plage['heure_debut'] . ' - ' . $plage['heure_fin']; ?>
                    <option value="<?= $plage['id'] ?>"><?= htmlspecialchars($label) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <button type="submit">Prendre rendez-vous</button>
        </form>
    </div>
</body>
</html>
