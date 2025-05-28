<?php
require_once '../includes/auth.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = login_user($_POST['email'], $_POST['password']);
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
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js" defer></script>

    <title>Connexion</title>
</head>
<body>
    <div class="form-container">
        <h2>Connexion</h2>
        <?php foreach ($errors as $error): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
        <form method="POST" action="">
            <label>Email :</label><br>
            <input type="email" name="email" required><br><br>
            <label>Mot de passe :</label><br>
            <input type="password" name="password" required><br><br>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
