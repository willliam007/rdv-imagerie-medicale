<?php
require_once 'db.php';

/**
 * Fonction d'inscription (patient uniquement).
 */
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
                // Créer l'utilisateur avec rôle par défaut 'patient'
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

/**
 * Fonction de connexion.
 */
function login_user($email, $password) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Authentification réussie
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];

        // Redirection selon le rôle
        if ($user['role'] === 'patient') {
            header("Location: dashboard.php");
        } elseif ($user['role'] === 'secretaire') {
            header("Location: gestion-rdv.php");
            exit;
        } elseif ($user['role'] === 'medecin') {
            header("Location: gestion-medecin.php");
        } elseif ($user['role'] === 'admin') {
            header("Location: admin-dashboard.php");
        } else {
            return ['success' => false, 'errors' => ['Accès interdit pour ce rôle.']];
        }

        exit;
    } else {
        return ['success' => false, 'errors' => ['Email ou mot de passe incorrect.']];
    }
}

/**
 * Vérifie la session et le rôle (protection des pages).
 */
function require_role($role = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Vérifie la connexion
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../public/login.php");
        exit;
    }

    // Vérifie le rôle si précisé
    if ($role !== null && (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role)) {
        die("Accès interdit : cette page est réservée au rôle '$role'.");
    }
}


/**
 * Ajoute une notification pour un utilisateur
 */
function ajouter_notification($user_id, $message) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->execute([$user_id, $message]);
}



/**
 * Récupère les notifications d'un utilisateur
 */
function get_notifications($user_id, $only_unread = false) {
    global $pdo;
    $sql = "SELECT * FROM notifications WHERE user_id = ?";
    if ($only_unread) {
        $sql .= " AND lu = 0";
    }
    $sql .= " ORDER BY date_creation DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Marque les notifications comme lues
 */
function marquer_notifications_lues($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE notifications SET lu = 1 WHERE user_id = ?");
    $stmt->execute([$user_id]);
}


?>
