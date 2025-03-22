<?php
session_start();
require "connexion-bdd.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["util_id_utilisateur"])) {
    die("Erreur : Vous devez être connecté pour voir vos rendez-vous.");
}

$id_utilisateur = $_SESSION["util_id_utilisateur"];

// Récupérer l'ID du patient à partir de l'utilisateur connecté
$sql_patient = "SELECT pat_id_patient FROM patients WHERE util_id_utilisateur = :id_utilisateur";
try {
    $stmt_patient = $pdo->prepare($sql_patient);
    $stmt_patient->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
    $stmt_patient->execute();
    $patient = $stmt_patient->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        die("Erreur : Aucun patient trouvé pour cet utilisateur.");
    }

    $id_patient = $patient['pat_id_patient']; // On récupère l'ID patient
} catch (PDOException $e) {
    die("Erreur de requête : " . $e->getMessage());
}

// Récupérer tous les rendez-vous du patient
$sql = "SELECT rdv.rdv_id_rendez_vous, rdv.rdv_date_rendez_vous, rdv.rdv_heure_rendez_vous, rdv.rdv_statut_rendez_vous,
               u.util_nom AS nom_medecin, u.util_prenom AS prenom_medecin
        FROM rendez_vous rdv
        JOIN medecins m ON rdv.rdv_id_medecin = m.med_id_medecin
        JOIN utilisateurs u ON m.util_id_utilisateur = u.util_id_utilisateur
        WHERE rdv.rdv_id_patient = :id_patient
        ORDER BY rdv.rdv_date_rendez_vous ASC, rdv.rdv_heure_rendez_vous ASC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_patient', $id_patient, PDO::PARAM_INT);
    $stmt->execute();
    $rendezvous = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de requête : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Rendez-vous</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="voir-rdv">
<h2>Mes Rendez-vous</h2>

<?php if (count($rendezvous) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Nom du Médecin</th>
                <th>Prénom du Médecin</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rendezvous as $rdv): ?>
                <tr>
                    <td><?php echo htmlspecialchars($rdv['nom_medecin']); ?></td>
                    <td><?php echo htmlspecialchars($rdv['prenom_medecin']); ?></td>
                    <td><?php echo htmlspecialchars($rdv['rdv_date_rendez_vous']); ?></td>
                    <td><?php echo htmlspecialchars($rdv['rdv_heure_rendez_vous']); ?></td>
                    <td><?php echo htmlspecialchars($rdv['rdv_statut_rendez_vous']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucun rendez-vous trouvé.</p>
<?php endif; ?>

<div>
    <form action="tableau-de-bord.php" method="get">
        <button type="submit">Retour au Tableau de bord</button>
    </form>
</div>

</div>

</body>
</html>
