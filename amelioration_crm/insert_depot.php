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
$RefCRM= $_POST['refcrm'];
$client=$_POST['client'];

// Vérifier si une entrée existe déjà avec le même idcomdet, couleur et nom_depot dans la table depot_packing
$sql_check = "SELECT id FROM depot_packing WHERE idcomdet = ? AND desc_coul = ? AND nom_depot = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("iss", $idcomdet, $couleur, $nom_depot);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Si une ligne existe déjà, renvoyer une erreur
    echo json_encode(array('status' => 'error', 'message' => 'Une entrée avec ces informations existe déjà.'));
} else {
    // Si la ligne n'existe pas, insérer une nouvelle ligne dans depot_packing
    $sql_insert_depot = "INSERT INTO depot_packing (desc_coul, desc_taille, nom_depot, qte_depot, idcomdet) 
                         VALUES (?, ?, ?, ?, ?)";
    $stmt_insert_depot = $conn->prepare($sql_insert_depot);
    $stmt_insert_depot->bind_param("sssii", $couleur, $taille, $nom_depot, $quantite, $idcomdet);
    
    if ($stmt_insert_depot->execute()) {
        // Insérer également dans la table packing si l'insertion depot_packing est réussie
        $sql_insert_packing = "INSERT INTO packing (desc_coul, desc_taille, ref_exp, quantite, idcomdet, nomcli,date_depot_packing,date_prevu_exp,date_depart_usine,transitaire,idcom) 
                               VALUES (?, ?, ?, ?, ?, ?,NOW(),NOW(),NOW(),'n/a',?)";
        $stmt_insert_packing = $conn->prepare($sql_insert_packing);
        $stmt_insert_packing->bind_param("sssiisi", $couleur, $taille, $nom_depot, $quantite, $idcomdet, $client,$RefCRM);
        
        if ($stmt_insert_packing->execute()) {
            echo json_encode(array('status' => 'success', 'message' => 'Données insérées avec succès dans les deux tables'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => $stmt_insert_packing->error));
        }
        $stmt_insert_packing->close();
    } else {
        echo json_encode(array('status' => 'error', 'message' => $stmt_insert_depot->error));
    }
    $stmt_insert_depot->close();
}

// Fermer la connexion
$stmt_check->close();
$conn->close();

?>
