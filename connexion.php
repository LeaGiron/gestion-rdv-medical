<?php
session_start();
require 'connexion-bdd.php'; 

// Vérifie s'il y a un message d'erreur en session
$error_message = "";
if (!empty($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']); 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format d'email invalide.";
    } else {
        try {
            // Récupérer l'utilisateur
            $stmt = $pdo->prepare("SELECT util_id_utilisateur, util_mot_de_passe, util_role FROM utilisateurs WHERE util_email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $error_message = "Aucun utilisateur trouvé avec cet email.";
            } elseif (!password_verify($password, $user['util_mot_de_passe'])) {
                $error_message = "Email ou mot de passe incorrect.";
            } else {
                // Stocker les informations de session
                $_SESSION['util_id_utilisateur'] = $user['util_id_utilisateur'];
                $_SESSION['role'] = $user['util_role'];

                // Si médecin, récupérer ID
                if ($user['util_role'] == 'medecin') {
                    $stmtMed = $pdo->prepare("SELECT med_id_medecin FROM medecins WHERE util_id_utilisateur = :id_utilisateur");
                    $stmtMed->execute(['id_utilisateur' => $user['util_id_utilisateur']]);
                    $medecin = $stmtMed->fetch(PDO::FETCH_ASSOC);
                    if ($medecin) {
                        $_SESSION['med_id_medecin'] = $medecin['med_id_medecin'];
                    }
                }

                // Redirection
                header("Location: tableau-de-bord.php");
                exit;
            }
        } catch (PDOException $e) {
            $error_message = "Erreur : " . $e->getMessage();
        }
    }

    // Stocker et recharger la page pour afficher l'erreur
    if (!empty($error_message)) {
        $_SESSION['error_message'] = $error_message;
        header("Location: connexion.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page-connexion">
        <img src="images/logo.png" alt="Logo de la plateforme" class="logo">
        <h1>Connectez-vous</h1>

        <form action="connexion.php" method="POST">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

        <!-- Affichage du message d'erreur d'email introuvable -->
        <?php if (!empty($error_message)) : ?>
            <p><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

            <button type="submit">Se connecter</button>
            <a href="index.html" class="bouton">Retour à la page d'accueil</a>
        </form>
    </div>
</body>
</html>
