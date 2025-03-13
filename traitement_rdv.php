<?php
session_start();
require "connexion-bdd.php"; // Connexion à la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["util_id_utilisateur"])) {
    die("Erreur : Vous devez être connecté pour prendre un rendez-vous.");
}

// Vérifier si les données du formulaire sont envoyées
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $id_utilisateur = $_SESSION["util_id_utilisateur"]; // L'utilisateur connecté
    $id_medecin = $_POST["med_id_medecin"];
    $date_rdv = $_POST["rdv_date_rendez_vous"];
    $heure_rdv = $_POST["rdv_heure_rendez_vous"];
    $statut_rdv = "en attente"; // Le statut initial peut être "en attente" par défaut

    // Vérifier que les champs ne sont pas vides
    if (empty($id_medecin) || empty($date_rdv) || empty($heure_rdv)) {
        die("Erreur : Tous les champs sont obligatoires.");
    }

    // Préparer la requête d'insertion pour enregistrer le rendez-vous dans la base de données
    $sql = "INSERT INTO rendez_vous (rdv_id_medecin, rdv_date_rendez_vous, rdv_heure_rendez_vous, rdv_statut_rendez_vous) 
            VALUES (:rdv_id_medecin, :rdv_date_rendez_vous, :rdv_heure_rendez_vous, :rdv_statut_rendez_vous)";

    try {
        $stmt = $pdo->prepare($sql);
        
        // Lier les paramètres à la requête préparée
        $stmt->bindParam(':rdv_id_medecin', $id_medecin, PDO::PARAM_INT);
        $stmt->bindParam(':rdv_date_rendez_vous', $date_rdv);
        $stmt->bindParam(':rdv_heure_rendez_vous', $heure_rdv);
        $stmt->bindParam(':rdv_statut_rendez_vous', $statut_rdv);

        // Exécuter la requête
        $stmt->execute();

        // Rediriger vers la page de consultation des rendez-vous
        header("Location: voir-rdv.php");
        exit();

    } catch (PDOException $e) {
        die("Erreur de base de données : " . $e->getMessage());
    }
} else {
    die("Erreur : La méthode de requête n'est pas correcte.");
}
?>
