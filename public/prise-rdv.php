<?php
require_once '../includes/db.php';
session_start();

// Rediriger si utilisateur non connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer l'ID du patient
$stmt = $pdo->prepare("SELECT id FROM patients WHERE user_id = ?");
$stmt->execute([$user_id]);
$patient_id = $stmt->fetchColumn();

// Récupérer les examens
$examens = $pdo->query("SELECT * FROM examens")->fetchAll(PDO::FETCH_ASSOC);

// Si formulaire soumis
$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sous_type_id = $_POST['sous_type_id'];
    $plage_id = $_POST['plage_id'];

    if ($sous_type_id && $plage_id && $patient_id) {
        $stmt = $pdo->prepare("INSERT INTO rendezvous (patient_id, sous_type_id, plage_id) VALUES (?, ?, ?)");
        if ($stmt->execute([$patient_id, $sous_type_id, $plage_id])) {
            $success = true;
        } else {
            $errors[] = "Erreur lors de l'enregistrement.";
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
        async function chargerSousTypes() {
            const examenId = document.getElementById('examen_id').value;
            const response = await fetch('get-sous-types.php?examen_id=' + examenId);
            const data = await response.json();
            const sousTypeSelect = document.getElementById('sous_type_id');
            sousTypeSelect.innerHTML = '<option value="">-- Sélectionner --</option>';
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.nom;
                sousTypeSelect.appendChild(option);
            });
        }
    </script>
</head>
<body>
    <h2>Prise de rendez-vous</h2>

    <?php if ($success): ?>
        <p style="color: green;">Rendez-vous enregistré avec succès.</p>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endforeach; ?>

    <form method="POST">
        <label>Type d'examen :</label><br>
        <select name="examen_id" id="examen_id" onchange="chargerSousTypes()" required>
            <option value="">-- Sélectionner --</
