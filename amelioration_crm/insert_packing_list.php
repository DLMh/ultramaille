<?php
include("../../admin/databases/db_to_mysql.php");

header('Content-Type: application/json'); // Réponse JSON

if (!$conn) {
    echo json_encode(array('status' => 'error', 'message' => 'Erreur de connexion : ' . mysqli_connect_error()));
    exit;
}

if (isset($_POST['ids']) && is_array($_POST['ids']) && isset($_POST['carton_id']) && isset($_POST['nomcli']) && isset($_POST['ref_exp'])) {
    $ids = $_POST['ids'];
    $carton_id = mysqli_real_escape_string($conn, $_POST['carton_id']);
    $nomcli = mysqli_real_escape_string($conn, $_POST['nomcli']);
    $ref_exp = mysqli_real_escape_string($conn, $_POST['ref_exp']);

    // Préparer les valeurs pour chaque combinaison
    $values = implode(',', array_map(function($id) use ($conn, $carton_id, $nomcli, $ref_exp) {
        $safe_id = mysqli_real_escape_string($conn, $id);
        return "('$carton_id', '$safe_id', '$nomcli', '$ref_exp')";
    }, $ids));

    // Construction de la requête SQL
    $sql = "INSERT INTO packing_list (idcarton, idpacking, nomcli, ref_exp) VALUES $values";
    // Exécution de la requête
    if (mysqli_query($conn, $sql)) {
        echo json_encode(array('status' => 'success', 'message' => 'Les données ont été enregistrés avec succès dans packing_list.'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Erreur lors de l\'enregistrement des IDs : ' . mysqli_error($conn)));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Données manquantes : IDs ou carton_id non fournis.'));
}

mysqli_close($conn);

