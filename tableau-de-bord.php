<?php
session_start();
include('connexion-bdd.php'); 

// Vérifie que l'utilisateur est bien connecté
if (!isset($_SESSION['util_id_utilisateur'])) {
    header("Location: connexion.html");
    exit;
}

// Récupérer le rôle de l'utilisateur
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="tableau-de-bord">
        <h1>Bienvenue sur votre tableau de bord</h1>

        <!-- Vérification du rôle -->
            <!-- Si l'utilisateur est un patient -->
            <?php if ($role == 'patient'): ?>
            <a href="prendre-rdv.php" class="bouton">Prendre un rendez-vous</a>
            <a href="voir-rdv.php" class="bouton">Voir mes rendez-vous</a>
            <?php endif; ?>

            <!-- Si l'utilisateur est un médecin, afficher "Voir tous les rendez-vous" -->
            <?php if ($role == 'medecin'): ?>
                <a href="voir-tous-rdv.php" class="bouton">Voir tous les rendez-vous</a>
            <?php endif; ?>

        <a href="deconnexion.php" class="bouton-deconnexion">Se déconnecter</a>
    </div>

</body>
</html>
