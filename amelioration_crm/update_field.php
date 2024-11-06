<?php
include("../../admin/databases/db_to_mysql.php");

if (isset($_POST['idcom'],$_POST['ref_exp'], $_POST['field'], $_POST['value'])) {
    $idcom = $_POST['idcom'];
    $ref_exp = $_POST['ref_exp'];
    $field = $_POST['field'];
    $value = $_POST['value'];

    // Whitelist allowed fields to avoid SQL injection
    $allowedFields = ['date_depot_packing', 'date_prevu_exp', 'date_depart_usine', 'transitaire'];
    if (!in_array($field, $allowedFields)) {
        echo "Invalid field"; 
        exit;
    }

    // Update query asio filtre ref exp eto requete eto 
    $query = "UPDATE packing SET $field = ? WHERE idcom = ? AND ref_exp = ?"; 
    var_dump($query,$ref_exp);
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sis", $value, $idcom,$ref_exp);

    if ($stmt->execute()) {
        echo "Field updated successfully";
    } else {
        echo "Error updating field";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request";
}
// UPDATE packing
// SET date_depot_packing = '2024-11-01'  -- Remplacez cette date par la date souhaitée
// WHERE idcom = 176 AND ref_exp = 'EXP10' AND desc_coul = 'Aqua chiné';
?>

