<?php
$host = 'localhost'; 
$db = 'gestion-rendez-vous-medicaux';
$user = 'root';
$pass = '';
$charset = 'utf8mb4'; 

$connexion = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($connexion, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Échec de la connexion : " . $e->getMessage();
}
?>
