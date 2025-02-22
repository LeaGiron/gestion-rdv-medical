<?php
// Récupérer les informations de connexion dans un autre fichier sécurisé
$config = require 'config.php';

// DSN (Data Source Name)
$connexion = "mysql:host={$config['host']};dbname={$config['db']};charset={$config['charset']}";

try {
    $pdo = new PDO($connexion, $config['user'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    die("Erreur de connexion. Contactez l'administrateur.");
}
?>
