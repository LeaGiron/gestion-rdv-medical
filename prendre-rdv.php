<?php
session_start();
require "connexion-bdd.php"; 

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["util_id_utilisateur"])) {
    die("Erreur : Vous devez être connecté pour prendre un rendez-vous.");
}

// Récupérer la liste des médecins
$sql = "SELECT util_id_utilisateur, util_nom, util_prenom FROM utilisateurs WHERE util_role = 'medecin'";
try {
    $stmt = $pdo->query($sql);
    $medecins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de requête : " . $e->getMessage());
}

// Définir les horaires fixes dès qu'une date est choisie
$horaires_disponibles = [];
if (isset($_POST['rdv_date_rendez_vous']) && !empty($_POST['rdv_date_rendez_vous'])) {
    $horaires_disponibles = [
        "08:00:00",
        "09:00:00",
        "10:00:00",
        "11:00:00",
        "12:00:00",
        "14:00:00",
        "15:00:00",
        "16:00:00",
        "17:00:00",
        "18:00:00"
    ];
}

// Enregistrer le rendez-vous dans la base de données
if ($_SERVER['REQUEST_METHOD'] == 'POST' 
    && isset($_POST['rdv_date_rendez_vous'], $_POST['rdv_heure_rendez_vous'], $_POST['med_id_medecin'])
    && !empty($_POST['rdv_heure_rendez_vous'])) {
    
    $patient_id = $_SESSION["util_id_utilisateur"];  
    $rdv_date = $_POST['rdv_date_rendez_vous'];
    $rdv_heure = $_POST['rdv_heure_rendez_vous'];
    $med_id = $_POST['med_id_medecin'];
    $rdv_statut = "en attente"; 

    // Insérer le rendez-vous avec le statut "en attente"
    $sql_insert = "INSERT INTO rendez_vous (rdv_id_patient, rdv_id_medecin, rdv_date_rendez_vous, rdv_heure_rendez_vous, rdv_statut_rendez_vous) 
                   VALUES (:patient_id, :med_id, :rdv_date, :rdv_heure, :rdv_statut)";
    try {
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':med_id', $med_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':rdv_date', $rdv_date, PDO::PARAM_STR);
        $stmt_insert->bindParam(':rdv_heure', $rdv_heure, PDO::PARAM_STR);
        $stmt_insert->bindParam(':rdv_statut', $rdv_statut, PDO::PARAM_STR);
        $stmt_insert->execute();

        header("Location: voir-rdv.php");
        exit(); 
    } catch (PDOException $e) {
        die("Erreur de requête d'insertion : " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Prendre un rendez-vous</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="prendre-rdv">
  <h2>Prendre un rendez-vous</h2>
  <form action="prendre-rdv.php" method="POST">
      <label for="med_id_medecin">Médecin :</label>
      <select id="med_id_medecin" name="med_id_medecin" required>
          <option value="">Sélectionnez un médecin</option>
          <?php foreach ($medecins as $row): ?>
              <option value="<?= htmlspecialchars($row['util_id_utilisateur']); ?>" <?= (isset($_POST['med_id_medecin']) && $_POST['med_id_medecin'] == $row['util_id_utilisateur']) ? 'selected' : ''; ?>>
                  <?= htmlspecialchars($row['util_nom'] . ' ' . $row['util_prenom']); ?>
              </option>
          <?php endforeach; ?>
      </select>

      <label for="date">Date du rendez-vous :</label>
      <input type="date" id="date" name="rdv_date_rendez_vous" required value="<?= isset($_POST['rdv_date_rendez_vous']) ? htmlspecialchars($_POST['rdv_date_rendez_vous']) : ''; ?>" onchange="this.form.submit()">

      <label for="heure">Heure du rendez-vous :</label>
      <select id="heure" name="rdv_heure_rendez_vous" required>
          <option value="">Sélectionnez l'heure</option>
          <?php
          // Si la date est choisie, on affiche les horaires fixes
          if (!empty($horaires_disponibles)) {
              foreach ($horaires_disponibles as $heure) {
                  echo "<option value='" . htmlspecialchars($heure) . "'>" . htmlspecialchars($heure) . "</option>";
              }
          }
          ?>
      </select>

      <button type="submit">Prendre rendez-vous</button>
  </form>
</div>

</body>
</html>
