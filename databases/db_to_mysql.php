<?php
// Informations de connexion
$servername = "localhost"; // ou l'adresse de votre serveur
$username = "root";
$password = "";
$dbname = "ultramaille";

// Créer une connexion
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Vérifier la connexion
if (!$conn) {
    die("La connexion a échoué: " . mysqli_connect_error());
}
// echo "Connexion réussie";
?>
