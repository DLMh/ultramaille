<?php
include("../../admin/databases/db_to_mysql.php");
// Vérifier si les données sont envoyées
if (isset($_POST['dimensions']) && isset($_POST['poids'])) {
    $dimensions = mysqli_real_escape_string($conn, $_POST['dimensions']);
    $poids = (float)$_POST['poids'];

    $sql = "INSERT INTO detail_carton (dimension, poids) VALUES ('$dimensions', '$poids')";

    if (mysqli_query($conn, $sql)) {
        echo "Enregistrement réussi";
    } else {
        echo "Erreur : " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
