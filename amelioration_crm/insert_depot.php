<?php

// Connexion à la base de données MySQL
include("../../admin/databases/db_to_mysql.php"); 

if (!$conn) {
    die(json_encode(array('status' => 'error', 'message' => mysqli_connect_error())));
}

// Récupérer les données soumises via POST
$couleur = $_POST['couleur'];
$taille = $_POST['taille'];
$nom_depot = $_POST['nom_depot'];
$quantite = $_POST['quantite'];
$idcomdet = $_POST['idcomdet'];

// Préparer la requête d'insertion avec une requête préparée
$sql = "INSERT INTO depot_packing (desc_coul, desc_taille, nom_depot, qte_depot, idcomdet) 
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssii", $couleur, $taille, $nom_depot, $quantite, $idcomdet);

// Exécuter la requête
if ($stmt->execute()) {
    echo json_encode(array('status' => 'success', 'message' => 'Données insérées avec succès'));
} else {
    echo json_encode(array('status' => 'error', 'message' => $stmt->error));
}

// Fermer la connexion
$stmt->close();
$conn->close();

?>

