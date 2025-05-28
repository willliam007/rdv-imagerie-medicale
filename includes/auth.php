<?php
require_once 'db.php';

// Fonction d'inscription
function register_user($email, $password, $confirm_password) {
    $errors = [];

    // Validation de base
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "Tous les champs sont obligatoires.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }

    if (empty($errors)) {
        global $pdo;

        // Vérifie si l'utilisateur existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Un utilisateur avec cet email existe déjà.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $pdo->beginTransaction();

            try {
                // Créer l'utilisateur
                $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'patient')");
                $stmt->execute([$email, $hashed_password]);
                $user_id = $pdo->lastInsertId();

                // Créer le profil patient lié
                $stmt = $pdo->prepare("INSERT INTO patients (user_id) VALUES (?)");
                $stmt->execute([$user_id]);

                $pdo->commit();

                // Démarre la session automatiquement
                session_start();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_role'] = 'patient';

                return ['success' => true];

            } catch (Exception $e) {
                $pdo->rollBack();
                $errors[] = "Erreur lors de l'inscription : " . $e->getMessage();
            }
        }
    }

    return ['success' => false, 'errors' => $errors];
}


// Fonction de connexion
function login_user($email, $password) {
    $errors = [];

    if (empty($email) || empty($password)) {
        $errors[] = "Tous les champs sont obligatoires.";
    }

    if (empty($errors)) {
        global $pdo;

        $stmt = $pdo->prepare("SELECT id, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            return ['success' => true];
        } else {
            $errors[] = "Email ou mot de passe incorrect.";
        }
    }

    return ['success' => false, 'errors' => $errors];
}
?>
