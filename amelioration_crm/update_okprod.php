<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include("../../admin/databases/db_to_mysql.php");
    $idcomdet = $_POST['idcomdet'];
    $okprod = $_POST['okprod'];

// Préparer et exécuter la requête avec mysqli
$stmt = $conn->prepare("UPDATE commande_mvt SET ok_prod = ? WHERE idcomdet = ?");
$stmt->bind_param("si", $okprod, $idcomdet); // 'si' signifie string et integer pour les types de paramètres

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Mise à jour réussie']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();

}
?>