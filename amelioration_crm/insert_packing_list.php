<?php
include("../../admin/databases/db_to_mysql.php");

header('Content-Type: application/json'); // Réponse JSON

if (!$conn) {
    echo json_encode(array('status' => 'error', 'message' => 'Erreur de connexion : ' . mysqli_connect_error()));
    exit;
}

if (isset($_POST['ids']) && is_array($_POST['ids']) && isset($_POST['nomcli']) && isset($_POST['ref_exp'])) {
    $ids = $_POST['ids'];
    $nomcli = mysqli_real_escape_string($conn, $_POST['nomcli']);
    $ref_exp = mysqli_real_escape_string($conn, $_POST['ref_exp']);
    
    $newValues = []; // Pour stocker les nouvelles données à insérer

    foreach ($ids as $id) {
        $safe_id = mysqli_real_escape_string($conn, $id);

        // Vérifier si la combinaison existe déjà
        $check_sql = "SELECT 1 FROM packing_list WHERE idpacking = '$safe_id' AND nomcli = '$nomcli' AND ref_exp = '$ref_exp'";
        $result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($result) === 0) {
            // Ajouter à la liste des nouvelles valeurs
            $newValues[] = "('$safe_id', '$nomcli', '$ref_exp')";
        }
    }

    if (!empty($newValues)) {
        // Insérer les nouvelles données
        $values = implode(',', $newValues);
        $sql = "INSERT INTO packing_list (idpacking, nomcli, ref_exp) VALUES $values";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(array('status' => 'success', 'message' => 'Les nouvelles données ont été enregistrées avec succès.'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Erreur lors de l\'enregistrement : ' . mysqli_error($conn)));
        }
    } else {
        echo json_encode(array('status' => 'info', 'message' => 'Toutes les données existent déjà.'));
        // Redirection (par exemple, si vous voulez envoyer vers une autre page)
        // header("Location: /page_packing_list.php");
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Données manquantes : IDs ou autres informations.'));
}

mysqli_close($conn);
