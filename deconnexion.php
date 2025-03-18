<?php
// Démarre la session
session_start();

// Détruit toutes les variables de session
session_unset();

// Détruit la session
session_destroy();
header("Location: index.html");
exit();
?>
