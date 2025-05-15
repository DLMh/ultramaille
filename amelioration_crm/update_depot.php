<?php
// update_depot.php
if (isset($_POST['iddepot']) && isset($_POST['qteDepot'])) {
    include("../../admin/databases/db_to_mysql.php");

    $iddepot = $_POST['iddepot'];
    $qteDepot = $_POST['qteDepot'];

    // Requête de mise à jour dans depot_packing
    $sql = "UPDATE depot_packing SET qte_depot = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $qteDepot, $iddepot);

    if ($stmt->execute()) {
        // Mise à jour aussi dans packing (où iddepot correspond)
        $sql_update_packing = "UPDATE packing SET quantite = ? WHERE iddepot = ?";
        $stmt2 = $conn->prepare($sql_update_packing);
        $stmt2->bind_param('ii', $qteDepot, $iddepot);

        if ($stmt2->execute()) {
            echo "Quantité mise à jour avec succès dans les deux tables.";
        } else {
            echo "Erreur lors de la mise à jour de packing : " . $stmt2->error;
        }

        $stmt2->close();
    } else {
        echo "Erreur lors de la mise à jour de depot_packing : " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Données manquantes.";
}
?>
