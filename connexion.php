<?php
session_start();
if (session_status() !== PHP_SESSION_ACTIVE) {
    die("Erreur : la session ne démarre pas.");
}
require 'connexion-bdd.php'; // Fichier de connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format d'email invalide.";
    }

    try {
        // Récupérer l'utilisateur en fonction de l'email
        $stmt = $pdo->prepare("SELECT util_id_utilisateur, util_mot_de_passe, util_role FROM utilisateurs WHERE util_email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        if ($user && password_verify($password, $user['util_mot_de_passe'])) {
            $_SESSION['util_id_utilisateur'] = $user['util_id_utilisateur'];  // Stocke l'ID de l'utilisateur
            $_SESSION['role'] = $user['util_role'];  // Stocke le rôle (médecin ou patient)

        // Créer un cookie qui dure 30 jours pour ne pas que l'utilisateur ait besoin de se reconnecter quand le cache est supprimé
        setcookie("remember_me", $user['util_id_utilisateur'], time() + (30 * 24 * 60 * 60), "/");

        // Si l'utilisateur est un médecin, récupérer son ID de médecin
        if ($user['util_role'] == 'medecin') {
        $stmtMed = $pdo->prepare("SELECT med_id_medecin FROM medecins WHERE util_id_utilisateur = :id_utilisateur");
        $stmtMed->execute(['id_utilisateur' => $user['util_id_utilisateur']]);
        $medecin = $stmtMed->fetch(PDO::FETCH_ASSOC);

        if ($medecin) {
            $_SESSION['med_id_medecin'] = $medecin['med_id_medecin']; // Stocker l'ID du médecin
        }
    }

            // Redirection selon le rôle
            if ($user['util_role'] == 'medecin') {
                header("Location: tableau-de-bord.php");  // Rediriger vers le tableau de bord du médecin
            } else {
                header("Location: tableau-de-bord.php");  // Rediriger vers le tableau de bord du patient
            }
            exit;
        } else {
            $error_message = "Email ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        $error_message = "Erreur : " . $e->getMessage();
    }
}
?>