<?php
include("../../admin/databases/db_to_mysql.php");
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['refExp'], $data['nomCli'])) {
    $refExp = $conn->real_escape_string($data['refExp']);
    $nomCli = $conn->real_escape_string($data['nomCli']);

    // Requête UPDATE
    $sql = "UPDATE packing SET etat = 1 WHERE ref_exp = '$refExp' AND nomcli = '$nomCli'";


    if ($conn->query($sql) === TRUE) {
        if ($conn->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Mise à jour réussie.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour : ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Données manquantes.']);
}

// Fermer la connexion
$conn->close();
?>
