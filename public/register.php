<?php
require_once '../includes/auth.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = register_user($_POST['email'], $_POST['password'], $_POST['confirm_password']);
    $success = $result['success'];
    $errors = $result['errors'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
</head>
<body>
    <h2>Inscription</h2>

    <?php if ($success): ?>
        <p style="color: green;">Inscription réussie ! <a href="login.php">Connectez-vous ici</a></p>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endforeach; ?>

    <form method="POST" action="">
        <label>Email :</label><br>
        <input type="email" name="email" required><br><br>

        <label>Mot de passe :</label><br>
        <input type="password" name="password" required><br><br>

        <label>Confirmer le mot de passe :</label><br>
        <input type="password" name="confirm_password" required><br><br>

        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>
