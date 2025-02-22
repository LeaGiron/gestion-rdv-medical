<?php
// Inclure le fichier de configuration
$config = require('config.php');

try {
    // Tentative de connexion à la base de données en utilisant les informations du fichier config.php
    $connexion = "mysql:host={$config['host']};dbname={$config['db']};charset={$config['charset']}";
    $pdo = new PDO($connexion, $config['user'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En cas d'erreur de connexion
    die("Échec de la connexion : " . $e->getMessage());
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $telephone = $_POST['telephone'];

    // Vérifier que tous les champs sont remplis
    if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) || empty($mot_de_passe)) {
        echo "Tous les champs sont obligatoires.";
        exit;
    }

    // Validation de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "L'email n'est pas valide.";
        exit;
    }

    // Vérifier si l'email existe déjà dans la base de données
    $requete = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE util_email = :email");
    $requete->execute(['email' => $email]);
    if ($requete->fetchColumn() > 0) {
        echo "Un compte avec cet email existe déjà.";
        exit;
    }

    // Hacher le mot de passe avant de l'insérer dans la base de données
    $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Insérer les données dans la table utilisateurs
    $requete = $pdo->prepare("INSERT INTO utilisateurs (util_nom, util_prenom, util_email, util_mot_de_passe, util_telephone) VALUES (:nom, :prenom, :email, :mot_de_passe, :telephone)");
    $requete->execute([
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'mot_de_passe' => $mot_de_passe_hache,
        'telephone' => $telephone
    ]);

    // Récupérer l'ID de l'utilisateur inséré
    $ID_utilisateur = $pdo->lastInsertId();

    // Insérer dans la table 'patient'
    $requetePatient = $pdo->prepare("INSERT INTO patients (util_id_utilisateur) VALUES (:util_id_utilisateur)");
    $requetePatient->execute(['util_id_utilisateur' => $ID_utilisateur]);

    echo "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";

    // Redirection vers la page de connexion
    header("Location: connexion.html");
    exit;
}
?>
