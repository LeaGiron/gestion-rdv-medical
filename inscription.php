<?php
$config = require('connexion-bdd.php');
$errors = []; // Tableau pour stocker les erreurs

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['password'];
    $telephone = $_POST['telephone'];

    // Vérifier que le nom ne contient que des lettres et des espaces
    if (!preg_match("/^[a-zA-Z\s]+$/", $nom)) {
        $errors['nom'] = "Le nom ne peut contenir que des lettres et des espaces.";
    }

    // Vérifier que le nom ne contient que des lettres et des espaces
    if (!preg_match("/^[a-zA-Z\s]+$/", $prenom)) {
        $errors['prenom'] = "Le prénom ne peut contenir que des lettres et des espaces.";
    }

    // Validation du numéro de téléphone : doit contenir exactement 10 chiffres
    if (!preg_match('/^\d{10}$/', $telephone)) {
        $errors['telephone'] = "Le numéro de téléphone doit contenir exactement 10 chiffres.";
    }

    // Vérification du format de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !strpos($email, '.') || !strpos($email, '@')) {
        $errors['email'] = "L'email doit être valide et contenir un '@' ainsi qu'un '.'.";
    }

    // Vérifier si l'email existe déjà dans la base de données utilisateurs
    $requeteUtilisateur = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE util_email = :email");
    $requeteUtilisateur->execute(['email' => $email]);
    if ($requeteUtilisateur->fetchColumn() > 0) {
        $errors['email'] = "Un compte avec cet email existe déjà.";
    }

    // Vérifier si l'utilisateur est déjà inscrit comme patient
    $requetePatient = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE util_id_utilisateur = (SELECT util_id_utilisateur FROM utilisateurs WHERE util_email = :email)");
    $requetePatient->execute(['email' => $email]);
    if ($requetePatient->fetchColumn() > 0) {
        $errors['email'] = "Cet utilisateur est déjà inscrit en tant que patient.";
    }

    // Si pas d'erreurs, procéder à l'insertion des données
    if (empty($errors)) {
        // Hacher le mot de passe avant de l'insérer dans la base de données
        $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        // Insérer les données dans la table utilisateurs
        $requeteUtilisateur = $pdo->prepare("INSERT INTO utilisateurs (util_nom, util_prenom, util_email, util_mot_de_passe, util_telephone) VALUES (:nom, :prenom, :email, :mot_de_passe, :telephone)");
        $requeteUtilisateur->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'mot_de_passe' => $mot_de_passe_hache,
            'telephone' => $telephone
        ]);

        // Récupérer l'ID de l'utilisateur inséré
        $ID_utilisateur = $pdo->lastInsertId();

        // Vérifier si l'utilisateur a déjà un patient associé
        $requetePatientAssocie = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE util_id_utilisateur = :util_id_utilisateur");
        $requetePatientAssocie->execute(['util_id_utilisateur' => $ID_utilisateur]);

        // Si l'utilisateur n'a pas encore de patient associé, l'ajouter
        if ($requetePatientAssocie->fetchColumn() == 0) {
            // Insérer dans la table 'patients'
            $requetePatientInsert = $pdo->prepare("INSERT INTO patients (util_id_utilisateur) VALUES (:util_id_utilisateur)");
            $requetePatientInsert->execute(['util_id_utilisateur' => $ID_utilisateur]);
        }

        // Redirection vers la page de connexion
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
    <title>Inscription</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="formulaire">
        <img src="images/logo.png" alt="Logo de la plateforme" class="logo-formulaire">
        <h2>Créer un compte</h2>

        <form action="inscription.php" method="POST">
            <label for="prenom">Prénom</label>
            <input type="text" placeholder="Entrer votre prénom" id="prenom" name="prenom" value="<?php echo htmlspecialchars($prenom ?? ''); ?>" required>
            <?php if (isset($errors['prenom'])): ?>
                <p><?php echo htmlspecialchars($errors['prenom']); ?></p>
            <?php endif; ?>
        
            <label for="nom">Nom</label>
            <input type="text" placeholder="Entrer votre nom de famille" id="nom" name="nom" value="<?php echo htmlspecialchars($nom ?? ''); ?>" required>
            <?php if (isset($errors['nom'])): ?>
                <p><?php echo htmlspecialchars($errors['nom']); ?></p>
            <?php endif; ?>
        
            <label for="email">Email</label>
            <input type="email" placeholder="Entrer votre email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            <?php if (isset($errors['email'])): ?>
                <p><?php echo htmlspecialchars($errors['email']); ?></p>
            <?php endif; ?>
        
            <label for="telephone">Numéro de téléphone</label>
            <input type="tel" placeholder="Entrer votre numéro de téléphone" id="telephone" name="telephone" value="<?php echo htmlspecialchars($telephone ?? ''); ?>" required>
            <?php if (isset($errors['telephone'])): ?>
                <p><?php echo htmlspecialchars($errors['telephone']); ?></p>
            <?php endif; ?>
        
            <label for="date_naissance">Date de naissance</label>
            <input type="date" placeholder="Entrer votre date de naissance" id="date_naissance" name="date_naissance" required>
                    
            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" placeholder="Entrer votre mot de passe" id="mot_de_passe" name="password" required>
            <?php if (isset($errors['mot_de_passe'])): ?>
                <p><?php echo htmlspecialchars($errors['mot_de_passe']); ?></p>
            <?php endif; ?>

            <button type="submit">Créer un compte</button>
        </form>

        <a href="connexion.php">Déjà un compte ? Connectez-vous</a>
    </div>
</body>
</html>
