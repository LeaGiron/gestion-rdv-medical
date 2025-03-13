<?php
session_start();
require 'connexion-bdd.php';

// Vérifier si l'utilisateur est bien un médecin (session active)
if (!isset($_SESSION['med_id_medecin'])) {
    header("Location: connexion.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["rdv_id"])) {
    $rdv_id = $_POST["rdv_id"];

    try {
        // Mise à jour du statut du rendez-vous en "Annulé"
        $stmt = $pdo->prepare("UPDATE rendez_vous SET rdv_statut_rendez_vous = 'Annulé' WHERE rdv_id_rendez_vous = ?");
        $stmt->execute([$rdv_id]);

        // Récupération des informations du patient et du médecin
        $stmt = $pdo->prepare("SELECT p_u.util_nom AS patient_nom, p_u.util_prenom AS patient_prenom, 
                                      m_u.util_nom AS medecin_nom, m_u.util_prenom AS medecin_prenom 
                               FROM rendez_vous r
                               JOIN utilisateurs p_u ON r.rdv_id_patient = p_u.util_id_utilisateur
                               JOIN utilisateurs m_u ON r.rdv_id_medecin = m_u.util_id_utilisateur
                               WHERE r.rdv_id_rendez_vous = ?");
        $stmt->execute([$rdv_id]);
        $rdv = $stmt->fetch(PDO::FETCH_ASSOC);

        // Stocker un message de confirmation dans la session
        $_SESSION['confirmation_message'] = "Le rendez-vous avec " . htmlspecialchars($rdv['patient_nom']) . " " . htmlspecialchars($rdv['patient_prenom']) . " a été annulé.";

        header("Location: tableau-de-bord.php"); // Redirection vers le tableau de bord
        exit;

    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
} else {
    header("Location: tableau-de-bord.php");
    exit;
}
?>
