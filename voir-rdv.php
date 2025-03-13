<?php
session_start();
require "connexion-bdd.php"; // Connexion à la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["util_id_utilisateur"])) {
    die("Erreur : Vous devez être connecté pour voir vos rendez-vous.");
}

$id_utilisateur = $_SESSION["util_id_utilisateur"]; // L'utilisateur connecté

// Récupérer tous les rendez-vous de l'utilisateur
$sql = "SELECT rdv.rdv_id_rendez_vous, rdv.rdv_date_rendez_vous, rdv.rdv_heure_rendez_vous, rdv.rdv_statut_rendez_vous, 
               med.util_nom AS nom_medecin, med.util_prenom AS prenom_medecin
        FROM rendez_vous rdv
        JOIN utilisateurs med ON rdv.rdv_id_medecin = med.util_id_utilisateur
        WHERE rdv.rdv_id_patient = :id_utilisateur 
        ORDER BY rdv.rdv_date_rendez_vous ASC, rdv.rdv_heure_rendez_vous ASC";

try {
    $stmt = $pdo->prepare($sql);
    
    // Lier le paramètre :id_utilisateur à la variable PHP
    $stmt->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
    
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
