<?php
// Démarre la session
session_start();

// Détruit toutes les variables de session
session_unset();

// Détruit la session
session_destroy();
setcookie('remember_me', '', time() - 3600, "/"); // Supprime le cookie
header("Location: index.html");
exit();
?>
