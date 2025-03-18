<?php
session_start();
include('connexion-bdd.php'); 

$medecin_id = $_SESSION['med_id_medecin'];  // Récupérer l'ID du médecin connecté

// Requête pour récupérer les rendez-vous "en attente"
$rdv_en_attente = "
    SELECT r.rdv_id_rendez_vous, 
           p_u.util_nom AS patient_nom, p_u.util_prenom AS patient_prenom, 
           m_u.util_nom AS medecin_nom, m_u.util_prenom AS medecin_prenom, 
           r.rdv_date_rendez_vous, r.rdv_heure_rendez_vous, r.rdv_statut_rendez_vous
    FROM rendez_vous r
    JOIN utilisateurs p_u ON r.rdv_id_patient = p_u.util_id_utilisateur
    JOIN utilisateurs m_u ON r.rdv_id_medecin = m_u.util_id_utilisateur
    WHERE r.rdv_id_medecin = :medecin_id AND r.rdv_statut_rendez_vous = 'En attente'
    ORDER BY r.rdv_date_rendez_vous ASC, r.rdv_heure_rendez_vous ASC
";

// Requête pour récupérer les rendez-vous "confirmés"
$rdv_confirmes = "
    SELECT r.rdv_id_rendez_vous, 
           p_u.util_nom AS patient_nom, p_u.util_prenom AS patient_prenom, 
           m_u.util_nom AS medecin_nom, m_u.util_prenom AS medecin_prenom, 
           r.rdv_date_rendez_vous, r.rdv_heure_rendez_vous, r.rdv_statut_rendez_vous
    FROM rendez_vous r
    JOIN utilisateurs p_u ON r.rdv_id_patient = p_u.util_id_utilisateur
    JOIN utilisateurs m_u ON r.rdv_id_medecin = m_u.util_id_utilisateur
    WHERE r.rdv_id_medecin = :medecin_id AND r.rdv_statut_rendez_vous = 'Confirmé'
    ORDER BY r.rdv_date_rendez_vous ASC, r.rdv_heure_rendez_vous ASC
";

// Requête pour récupérer les rendez-vous "annulés"
$rdv_annules = "
    SELECT r.rdv_id_rendez_vous, 
           p_u.util_nom AS patient_nom, p_u.util_prenom AS patient_prenom, 
           m_u.util_nom AS medecin_nom, m_u.util_prenom AS medecin_prenom, 
           r.rdv_date_rendez_vous, r.rdv_heure_rendez_vous, r.rdv_statut_rendez_vous
    FROM rendez_vous r
    JOIN utilisateurs p_u ON r.rdv_id_patient = p_u.util_id_utilisateur
    JOIN utilisateurs m_u ON r.rdv_id_medecin = m_u.util_id_utilisateur
    WHERE r.rdv_id_medecin = :medecin_id AND r.rdv_statut_rendez_vous = 'Annulé'
    ORDER BY r.rdv_date_rendez_vous ASC, r.rdv_heure_rendez_vous ASC
";

// Préparer et exécuter les requêtes pour chaque statut
$stmt_en_attente = $pdo->prepare($rdv_en_attente);
$stmt_en_attente->bindParam(':medecin_id', $medecin_id, PDO::PARAM_INT);
$stmt_en_attente->execute();
$rdv_en_attente = $stmt_en_attente->fetchAll(PDO::FETCH_ASSOC);

$stmt_confirmes = $pdo->prepare($rdv_confirmes);
$stmt_confirmes->bindParam(':medecin_id', $medecin_id, PDO::PARAM_INT);
$stmt_confirmes->execute();
$rdv_confirmes = $stmt_confirmes->fetchAll(PDO::FETCH_ASSOC);

$stmt_annules = $pdo->prepare($rdv_annules);
$stmt_annules->bindParam(':medecin_id', $medecin_id, PDO::PARAM_INT);
$stmt_annules->execute();
$rdv_annules = $stmt_annules->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voir tous les RDV</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>

  <div class="voir-tous-rdv">
<!-- Section des rendez-vous à accepter -->
<section class="rdv-a-accepter">
  <h2>Rendez-vous en attente de confirmation</h2>
  <ul class="rdv-liste">
    <?php if (!empty($rdv_en_attente)): ?>
      <?php foreach ($rdv_en_attente as $rdv): ?>
        <li>
          <p><strong>Nom du patient :</strong> <?= htmlspecialchars($rdv['patient_nom']) ?> <?= htmlspecialchars($rdv['patient_prenom']) ?></p>
          <p><strong>Date du rendez-vous :</strong> <?= htmlspecialchars($rdv['rdv_date_rendez_vous']) ?></p>
          <p><strong>Heure du rendez-vous :</strong> <?= htmlspecialchars($rdv['rdv_heure_rendez_vous']) ?></p>

          <!-- Bouton Confirmer le RDV -->
          <form action="accepter-rdv.php" method="POST">
        <input type="hidden" name="rdv_id" value="<?= htmlspecialchars($rdv['rdv_id_rendez_vous']) ?>">
        <button type="submit">Accepter ce rendez-vous</button>
        </form>
        </li>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Aucun rendez-vous en attente.</p>
    <?php endif; ?>
  </ul>
</section>

<!-- Section des rendez-vous confirmés -->
<section class="tous-les-rdv">
  <h2>Rendez-vous confirmés</h2>
  <ul class="tous-les-rdv-liste">
    <?php if (!empty($rdv_confirmes)): ?>
      <?php foreach ($rdv_confirmes as $rdv): ?>
        <li>
          <p><strong>Nom du patient :</strong> <?= htmlspecialchars($rdv['patient_nom']) ?> <?= htmlspecialchars($rdv['patient_prenom']) ?></p>
          <p><strong>Date du rendez-vous :</strong> <?= htmlspecialchars($rdv['rdv_date_rendez_vous']) ?></p>
          <p><strong>Heure du rendez-vous :</strong> <?= htmlspecialchars($rdv['rdv_heure_rendez_vous']) ?></p>

            <!-- Formulaire pour annuler le rendez-vous -->
            <form action="annuler-rdv.php" method="POST">
            <input type="hidden" name="rdv_id" value="<?= htmlspecialchars($rdv['rdv_id_rendez_vous']) ?>">
            <button type="submit">Annuler ce rendez-vous</button>
          </form>

        </li>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Aucun rendez-vous confirmé.</p>
    <?php endif; ?>
  </ul>
</section>

    <!-- Section des rendez-vous annulés -->
<section class="rdv-annules">
  <h2>Rendez-vous annulés</h2>
  <ul class="rdv-annules-liste">
    <?php if (!empty($rdv_annules)): ?>
      <?php foreach ($rdv_annules as $rdv): ?>
        <li>
          <p><strong>Nom du patient :</strong> <?= htmlspecialchars($rdv['patient_nom']) ?> <?= htmlspecialchars($rdv['patient_prenom']) ?></p>
          <p><strong>Date du rendez-vous :</strong> <?= htmlspecialchars($rdv['rdv_date_rendez_vous']) ?></p>
          <p><strong>Heure du rendez-vous :</strong> <?= htmlspecialchars($rdv['rdv_heure_rendez_vous']) ?></p>
          <form action="restaurer-rdv.php" method="POST">
          <input type="hidden" name="rdv_id" value="<?= htmlspecialchars($rdv['rdv_id_rendez_vous']) ?>">
          <button type="submit">Restaurer ce rendez-vous</button>
        </form>

        </li>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Aucun rendez-vous annulé.</p>
    <?php endif; ?>
  </ul>
</section>

<div>
    <form action="tableau-de-bord.php" method="get">
        <button type="submit">Retour au Tableau de bord</button>
    </form>
</div>

    </div>

</body>
</html>
