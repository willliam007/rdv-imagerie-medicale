<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

$success = '';
$error = '';

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Inscription de l'utilisateur (email / mdp)
    $result = register_user($email, $password, $confirm_password);

    if ($result['success']) {
        // Mise à jour du profil patient (nom, prénom)
        $user_id = $_SESSION['user_id'];
        $stmt = $pdo->prepare("UPDATE patients SET nom = ?, prenom = ? WHERE user_id = ?");
        $stmt->execute([$nom, $prenom, $user_id]);

        // Redirection vers le profil
        header('Location: profil.php');
        exit;
    } else {
        $error = implode('<br>', $result['errors']);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription - Imagerie Médicale</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
  <header class="bg-white shadow-sm mb-4">
    <div class="container d-flex justify-content-between align-items-center py-3">
      <div class="d-flex align-items-center">
        <img src="assets/logo.svg" alt="Logo" height="50" class="me-2">
        <span class="fs-4 fw-bold text-primary">Imagerie Médicale</span>
      </div>
      <nav>
        <a href="index.php" class="btn btn-outline-primary me-2"><i class="fas fa-home"></i> Accueil</a>
        <a href="login.php" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Connexion</a>
      </nav>
    </div>
  </header>

  <main>
    <div class="container d-flex flex-column align-items-center justify-content-center" style="min-height: 70vh;">
      <div class="card shadow p-4" style="max-width: 500px; width: 100%;">
        <h2 class="mb-4 text-center text-primary">Créer un compte</h2>
        <?php if ($success): ?>
          <div class="alert alert-success text-center"><?= $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="alert alert-danger text-center"><?= $error; ?></div>
        <?php endif; ?>

        <!-- ⚠️ Correction ici : action="" pour soumettre au même fichier -->
        <form action="" method="post">
          <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
          </div>
          <div class="mb-3">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Adresse e-mail</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
        </form>

        <div class="mt-3 text-center">
          <a href="login.php">Déjà inscrit ? Se connecter</a>
        </div>
      </div>
    </div>
  </main>

  <footer class="bg-primary text-white text-center py-3 mt-5">
    &copy; 2024 Imagerie Médicale. Tous droits réservés.
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
