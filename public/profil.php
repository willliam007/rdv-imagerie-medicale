<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// $user_id = $_SESSION['user_id'];

// Récupérer l’ID du patient lié à l’utilisateur
// $stmt = $pdo->prepare('SELECT id FROM patients WHERE user_id = ?');
// $stmt->execute([$user_id]);
// $patient_id = $stmt->fetchColumn();

// if (!$patient_id) {
//     echo "Erreur : patient non trouvé.";
//     exit;
// }

// // Récupérer les données du patient
// $stmt = $pdo->prepare('SELECT nom, prenom, sexe, telephone, age FROM patients WHERE id = ?');
// $stmt->execute([$patient_id]);
// $user = $stmt->fetch();

// $success = $_SESSION['profil_success'] ?? '';
// $error = $_SESSION['profil_error'] ?? '';
// unset($_SESSION['profil_success'], $_SESSION['profil_error']);



require_once '../includes/auth.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer les données actuelles du profil
$stmt = $pdo->prepare("SELECT * FROM patients WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Message de succès/erreur
$success = '';
$error = '';

// Traitement de la mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $sexe = $_POST['sexe'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $age = $_POST['age'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $profession = $_POST['profession'] ?? '';

    // Vérification minimale (tu peux en ajouter plus)
    if (empty($nom) || empty($prenom) || empty($telephone)|| empty($age) || empty($sexe)) {
        $error = "Tous les champs sont requis.";
    } else {
        $stmt = $pdo->prepare("UPDATE patients SET nom = ?, prenom = ?, telephone = ?, age = ?, sexe = ?, adresse = ?, profession = ? WHERE user_id = ?");
        $stmt->execute([$nom, $prenom, $telephone, $age, $sexe, $adresse, $profession, $user_id]);
        $success = "Profil mis à jour avec succès.";
        // Rafraîchir les données à afficher
        $stmt = $pdo->prepare("SELECT nom, prenom, sexe, telephone, age, adresse, profession FROM patients WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
    }
}


?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mon Profil - Imagerie Médicale</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<header class="bg-white shadow-sm mb-4">
    <div class="container d-flex justify-content-between align-items-center py-3">
        <div class="d-flex align-items-center">
            <img src="assets/logo.svg" alt="Logo" height="50" class="me-2">
            <span class="fs-4 fw-bold text-primary">Easy ImAgInG</span>
        </div>
        <nav>
            <a href="index.php" class="btn btn-outline-primary me-2"><i class="fas fa-home"></i> Accueil</a>
            <a href="dashboard.php" class="btn btn-primary"><i class="fas fa-user"></i> Tableau de bord</a>
            <a href="logout.php" class="btn btn-danger ms-2"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </nav>
    </div>
</header>

<main>
    <div class="container d-flex flex-column align-items-center justify-content-center" style="min-height: 70vh;">
        <div class="card shadow p-4" style="max-width: 500px; width: 100%;">
            <h2 class="mb-4 text-center text-primary"> Mon Profile</h2>

            <?php if ($success): ?>
                <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="" method="post">
                <input type="hidden" name="action" value="update_profile">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for ="sexe" class="form-label">Sexe</label>
                    <select name="sexe">
                         <option value="">-- Sélectionner --</option>
                            <option value="H" <?= ($user['sexe'] ?? '') == 'H' ? 'selected' : '' ?>>Homme</option>
                            <option value="F" <?= ($user['sexe'] ?? '') == 'F' ? 'selected' : '' ?>>Femme</option>
                 </select>

                </div>
                <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="text" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="age" class="form-label">Âge</label>
                    <input type="number" class="form-control" id="age" name="age" value="<?= htmlspecialchars($user['age']) ?>">
                </div>
                <label for = "profession" class = "form-label">Profession </label><br>
                     <input type="text" class="form-control" id="profession" name="profession" value="<?= htmlspecialchars($user['profession'] ?? '') ?>">

             <label for="adresse" class="form-label">Adresse </label><br>
                    <input type ="text" class ="form-control" id="adesse" name="adresse" value="<?= htmlspecialchars($user['adresse'] ?? '') ?>"><br>
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> Mettre à jour</button>
            </form>
        </div>
    </div>
</main>

<footer class="bg-primary text-white text-center py-3 mt-5">
    &copy; 2025 Easy Imagerie. Tous droits réservés. Réalisé par @william.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
