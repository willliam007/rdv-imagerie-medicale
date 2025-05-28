<?php
require_once '../includes/auth.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = register_user($_POST['email'], $_POST['password'], $_POST['confirm_password']);
    if ($result['success']) {
        header("Location: profil.php");
        exit;
    }
    
    $errors = $result['errors'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js" defer></script>

    <title>Inscription</title>
</head>
<body>
    <div class="form-container">
        <h2>Inscription</h2>
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
    </div>
</body>
</html>
