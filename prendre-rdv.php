<?php
session_start();
include('connexion-bdd.php');

// PHP doit utiliser le fuseau horaire Europe/Paris
date_default_timezone_set('Europe/Paris');

// Récupérer l'ID de l'utilisateur depuis la session (vérifiez que l'utilisateur est connecté)
$util_id_utilisateur = isset($_SESSION['util_id_utilisateur']) ? $_SESSION['util_id_utilisateur'] : null;

if (!$util_id_utilisateur) {
    die("Erreur : Vous devez être connecté pour prendre un rendez-vous.");
}

try {
    // Récupérer l'ID du patient en fonction de l'utilisateur connecté
    $stmt = $pdo->prepare("SELECT pat_id_patient FROM patients WHERE util_id_utilisateur = :util_id_utilisateur");
    $stmt->execute(['util_id_utilisateur' => $util_id_utilisateur]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        die("Erreur : Aucun patient trouvé pour cet utilisateur.");
    }

    $rdv_id_patient = $patient['pat_id_patient']; // ID du patient pour l'enregistrement du rendez-vous

    // Récupérer la liste des médecins
    $stmt = $pdo->prepare("
        SELECT m.med_id_medecin, u.util_nom, u.util_prenom 
        FROM medecins m 
        JOIN utilisateurs u ON m.util_id_utilisateur = u.util_id_utilisateur
        WHERE u.util_role = 'medecin'
    ");
    $stmt->execute();
    $medecins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des médecins : " . $e->getMessage());
}

// Liste des horaires possibles en format HH:MM
$horaires_disponibles = [
    "08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30", 
    "12:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", 
    "17:00", "17:30", "18:00"
];

// Vérifier si une date et un médecin ont été sélectionnés
if (isset($_POST['rdv_id_medecin'], $_POST['rdv_date_rendez_vous'])) {
    $medecin_id = $_POST['rdv_id_medecin'];
    $rdv_date_rendez_vous = $_POST['rdv_date_rendez_vous'];

    // Récupérer les horaires déjà réservés pour ce médecin et cette date
    $stmt = $pdo->prepare("SELECT rdv_heure_rendez_vous FROM rendez_vous WHERE rdv_id_medecin = :medecin_id AND rdv_date_rendez_vous = :rdv_date_rendez_vous");
    $stmt->execute(['medecin_id' => $medecin_id, 'rdv_date_rendez_vous' => $rdv_date_rendez_vous]);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Convertir l'heure de la base de données en format HH:MM (ex : "15:30:00" -> "15:30")
        $heure_db = substr($row['rdv_heure_rendez_vous'], 0, 5);
        // Supprimer l'heure déjà réservée de la liste
        $horaires_disponibles = array_diff($horaires_disponibles, [$heure_db]);
    }
}

// Message de confirmation ou d'erreur
$message = "";

// Vérification et enregistrement du rendez-vous
if ($_SERVER["REQUEST_METHOD"] === "POST" 
    && isset($_POST['rdv_heure_rendez_vous'], $_POST['rdv_id_medecin'], $_POST['rdv_date_rendez_vous'])) {

    $heure = $_POST['rdv_heure_rendez_vous']; // L'heure envoyée par le formulaire (format attendu "HH:MM")
    $medecin_id = $_POST['rdv_id_medecin'];
    $rdv_date_rendez_vous = $_POST['rdv_date_rendez_vous'];

    // Formatage de l'heure en HH:MM via strtotime (au cas où)  
    $heure_formatee = date('H:i', strtotime($heure));

    // Vérifier que l'heure formatée figure bien dans la liste des horaires disponibles
    if (!in_array($heure_formatee, $horaires_disponibles)) {
        $message = "<p>L'heure sélectionnée est invalide. Veuillez choisir une heure valide.</p>";
    } else {
        // Vérifier si l'heure est toujours disponible avant d'enregistrer
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM rendez_vous WHERE rdv_id_medecin = :medecin_id AND rdv_date_rendez_vous = :rdv_date_rendez_vous AND rdv_heure_rendez_vous = :heure");
        $stmt->execute([ 
            'medecin_id' => $medecin_id, 
            'rdv_date_rendez_vous' => $rdv_date_rendez_vous, 
            'heure' => $heure_formatee
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row['total'] == 0) {
            // Insérer le rendez-vous en base de données avec le statut "en attente"
            $stmt = $pdo->prepare("INSERT INTO rendez_vous (rdv_id_medecin, rdv_date_rendez_vous, rdv_heure_rendez_vous, rdv_statut_rendez_vous, rdv_id_patient) VALUES (:medecin_id, :rdv_date_rendez_vous, :rdv_heure_rendez_vous, 'en attente', :rdv_id_patient)");
            $stmt->execute([
                'medecin_id' => $medecin_id, 
                'rdv_date_rendez_vous' => $rdv_date_rendez_vous, 
                'rdv_heure_rendez_vous' => $heure_formatee,
                'rdv_id_patient' => $rdv_id_patient
            ]);
            $message = "<p>Rendez-vous réservé avec succès !</p>";
            
            // Redirection pour éviter une double soumission du formulaire
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $message = "<p>L'heure sélectionnée est déjà prise. Veuillez choisir une autre heure.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Prendre un rendez-vous</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="prendre-rdv">
    <h2>Prendre un Rendez-vous</h2>

    <!-- Formulaire pour choisir un médecin et une date -->
    <form method="post">
        <label for="medecin">Médecin :</label>
        <select name="rdv_id_medecin" id="medecin" required>
            <option value="">Sélectionnez un médecin</option>
            <?php foreach ($medecins as $medecin) : ?>
                <option value="<?= $medecin['med_id_medecin']; ?>" <?= (isset($_POST['rdv_id_medecin']) && $_POST['rdv_id_medecin'] == $medecin['med_id_medecin']) ? 'selected' : ''; ?>>
                    <?= $medecin['util_nom'] . " " . $medecin['util_prenom']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <br>

        <label for="date">Date :</label>
        <input type="date" name="rdv_date_rendez_vous" id="date" value="<?= $_POST['rdv_date_rendez_vous'] ?? ''; ?>" required>

        <br>

        <button type="submit">Vérifier les disponibilités</button>
    </form>

    <!-- Affichage des horaires disponibles si un médecin et une date ont été sélectionnés -->
    <?php if (!empty($horaires_disponibles) && isset($_POST['rdv_id_medecin'], $_POST['rdv_date_rendez_vous'])) : ?>
        <form method="post">
            <input type="hidden" name="rdv_id_medecin" value="<?= $_POST['rdv_id_medecin']; ?>">
            <input type="hidden" name="rdv_date_rendez_vous" value="<?= $_POST['rdv_date_rendez_vous']; ?>">

            <label for="heure">Heure :</label>
            <select name="rdv_heure_rendez_vous" id="heure" required>
                <?php 
                // Convertir le format "HH:MM" en "HHhMM"
                foreach ($horaires_disponibles as $horaire) : 
                    $affichage = str_replace(":", "h", $horaire);
                ?>
                    <option value="<?= $horaire; ?>"><?= $affichage; ?></option>
                <?php endforeach; ?>
            </select>

            <br>

            <button type="submit">Confirmer le rendez-vous</button>
        </form>
    <?php endif; ?>

    <!-- Message de confirmation ou d'erreur -->
    <?= $message; ?>

    <form action="tableau-de-bord.php" method="get">
        <button type="submit">Retour au Tableau de Bord</button>
    </form>

</div>

</body>
</html>
