<?php
// update_depot.php
if (isset($_POST['iddepot']) && isset($_POST['qteDepot'])) {
include("../../admin/databases/db_to_mysql.php");
    $iddepot = $_POST['iddepot'];
    $qteDepot = $_POST['qteDepot'];

    // Requête de mise à jour
    $sql = "UPDATE depot_packing SET qte_depot = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $qteDepot, $iddepot);  

    if ($stmt->execute()) {
        echo "Quantité mise à jour avec succès.";
    } else {
        echo "Erreur lors de la mise à jour : " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Données manquantes.";
}
?>
