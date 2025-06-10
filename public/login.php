<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
session_start();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $result = login_user($email, $password);
    if ($result['success']) {
        header("Location: dashboard.php");
        exit;
    } else {
        $errors = $result['errors'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion - Imagerie Médicale</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<header class="bg-white shadow-sm mb-4">
    <div class="container d-flex justify-content-between align-items-center py-3">
        <div class="d-flex align-items-center">
            <img src="assets/logo.svg" alt="Logo" height="50" class="me-2">
            <span class="fs-4 fw-bold text-primary">Easy ImAgInG </span>
        </div>
        <nav>
            <a href="index.php" class="btn btn-outline-primary me-2"><i class="fas fa-home"></i> Accueil</a>
            <a href="register.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Inscription</a>
        </nav>
    </div>
</header>

<main>
    <div class="container d-flex flex-column align-items-center justify-content-center" style="min-height: 70vh;">
        <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
            <h2 class="mb-4 text-center text-primary">Connexion</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger text-center">
                    <?php foreach ($errors as $error): ?>
                        <?= htmlspecialchars($error) ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse e-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-sign-in-alt"></i> Se connecter</button>
            </form>

            <div class="mt-3 text-center">
                <a href="register.php">Pas encore de compte ? Créez-en un</a>
            </div>
        </div>
    </div>
</main>

<footer class="bg-primary text-white text-center py-3 mt-5">
    &copy; 2025 Imagerie Médicale. Tous droits réservés. Réalisé par @william.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
