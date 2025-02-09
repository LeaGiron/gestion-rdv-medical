<?php
// Inclure le fichier de configuration
include('config.php');


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
    $email = $_POST['email'];
    $mot_de_passe = $_POST['password'];

    // Validation de l'email (format email)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "L'email n'est pas valide.";
        exit;
    }

    // Hacher le mot de passe avant de l'insérer dans la base de données
    $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Vérifier si l'email existe déjà dans la base de données
    $requete = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE util_email = :email");
    $requete->execute(['email' => $email]);
    if ($requete->fetchColumn() > 0) {
        echo "Un compte avec cet email existe déjà.";
        exit;
    }

    // Insérer les données dans la table utilisateurs
    $requete = $pdo->prepare("INSERT INTO utilisateurs (util_email, util_mot_de_passe) VALUES (:email, :password)");
    $requete->execute([
        'email' => $email,
        'password' => $mot_de_passe_hache
    ]);

    echo "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
    // Redirection vers la page de connexion
    header("Location: connexion.html");
    exit;
}
?>