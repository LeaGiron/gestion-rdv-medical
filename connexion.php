<?php
session_start();
require 'connexion-bdd.php'; // Fichier de connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Format d'email invalide.");
    }

    try {
        $stmt = $pdo->prepare("SELECT util_id_utilisateur, util_mot_de_passe, util_role FROM utilisateurs WHERE util_email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['util_mot_de_passe'])) {
            $_SESSION['user_id'] = $user['util_id_utilisateur'];
            $_SESSION['role'] = $user['util_role'];
            header("Location: tableau-de-bord.php");
            exit;
        } else {
            echo "Email ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
