<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
// require_once '../includes/guard.php';

session_start();
require_role('patient');


// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $age = intval($_POST['age']);
    $sexe = $_POST['sexe'];
    $telephone = trim($_POST['telephone']);
    $profession = trim($_POST['profession']);
    $adresse = trim($_POST['adresse']);

    if ($nom && $prenom && $age && $sexe && $telephone) {
        $stmt = $pdo->prepare("UPDATE patients SET nom=?, prenom=?, age=?, sexe=?, telephone=?, profession=?, adresse=? WHERE user_id=?");
        if ($stmt->execute([$nom, $prenom, $age, $sexe, $telephone, $profession, $adresse, $user_id])) {
            $success = true;
        } else {
            $errors[] = "Erreur lors de la mise à jour.";
        }
    } else {
        $errors[] = "Tous les champs requis (*) doivent être remplis.";
    }
}

// Récupère les infos existantes
$stmt = $pdo->prepare("SELECT * FROM patients WHERE user_id=?");
$stmt->execute([$user_id]);
$patient = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
</head>
<body>
    <h2>Compléter mon profil</h2>

    <?php if ($success): ?>
        <p style="color: green;">Mise à jour réussie !</p>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endforeach; ?>

    <form method="POST">
        <label>Nom* :</label><br>
        <input type="text" name="nom" value="<?= htmlspecialchars($patient['nom'] ?? '') ?>"><br><br>

        <label>Prénom* :</label><br>
        <input type="text" name="prenom" value="<?= htmlspecialchars($patient['prenom'] ?? '') ?>"><br><br>

        <label>Âge* :</label><br>
        <input type="number" name="age" value="<?= htmlspecialchars($patient['age'] ?? '') ?>"><br><br>

        <label>Sexe* :</label><br>
        <select name="sexe">
            <option value="">-- Sélectionner --</option>
            <option value="H" <?= ($patient['sexe'] ?? '') == 'H' ? 'selected' : '' ?>>Homme</option>
            <option value="F" <?= ($patient['sexe'] ?? '') == 'F' ? 'selected' : '' ?>>Femme</option>
        </select><br><br>

        <label>Téléphone* :</label><br>
        <input type="text" name="telephone" value="<?= htmlspecialchars($patient['telephone'] ?? '') ?>"><br><br>

        <label>Profession :</label><br>
        <input type="text" name="profession" value="<?= htmlspecialchars($patient['profession'] ?? '') ?>"><br><br>

        <label>Adresse :</label><br>
        <textarea name="adresse"><?= htmlspecialchars($patient['adresse'] ?? '') ?></textarea><br><br>

        <button type="submit">Enregistrer</button>
    </form>
</body>
</html>
