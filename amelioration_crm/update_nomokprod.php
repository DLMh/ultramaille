<?php
if (isset($_POST['nomokprod']) && isset($_POST['idcomdetList'])) {

    $nomokprod = $_POST['nomokprod'];
    $idcomdetList = explode(',', $_POST['idcomdetList']); 
    include("../../admin/databases/db_to_mysql.php");

    foreach ($idcomdetList as $idcomdet) {
        $sql = "UPDATE commande_mvt SET nom_ok_prod = ? WHERE idcomdet = ?";
        
       
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $nomokprod, $idcomdet); 
        
      
        if (!$stmt->execute()) {
            echo "Erreur lors de la mise à jour : " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();

  
    echo "Mise à jour effectuée avec succès";
}
?>
