<?php
$host = 'localhost';
$dbname = 'imagerie'; // À adapter selon le nom de ta base
$user = 'root';
$pass = ''; // Laisse vide si pas de mot de passe WAMP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur connexion DB : " . $e->getMessage());
}
?>
