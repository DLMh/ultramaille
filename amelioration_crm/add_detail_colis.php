<?php
// Connexion à la base de données MySQL
include("../../admin/databases/db_to_mysql.php");

if (!$conn) {
    die(json_encode(array('status' => 'error', 'message' => mysqli_connect_error())));
}

// Récupère les valeurs du formulaire
$refcde = $_POST['reference'] ?? null;
$nomcli = $_POST['nomcli'] ?? null;
$ref_exp = $_POST['ref_exp'] ?? null;
$poids_colis = $_POST['poids'] ?? []; // Tableau des poids par taille
$quantite_colis = $_POST['quantite_colis'] ?? []; // Tableau des quantités par taille
$carton_param = $_POST['cartons_param'] ?? null;

// Vérification des données reçues
if (is_null($refcde) || is_null($nomcli) || is_null($ref_exp) || empty($poids_colis) || empty($carton_param)) {
    echo json_encode(array('status' => 'error', 'message' => 'Paramètres manquants ou invalides.'));
    exit;
}

// Prépare et exécute l'insertion ou la mise à jour pour chaque taille disponible
$success = true; // Variable pour suivre l'état global

foreach ($poids_colis as $taille => $poids) {
    $quantite = isset($quantite_colis[$taille]) ? $quantite_colis[$taille] : 0;

    // Vérifier si le refcde existe déjà
    $check_sql = "SELECT 1 FROM detail_colis WHERE refcde = ? AND desc_taille = ?";
    $stmt_check = $conn->prepare($check_sql);

    if ($stmt_check) {
        $stmt_check->bind_param("ss", $refcde, $taille);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            // Mise à jour de l'enregistrement existant
            $update_sql = "UPDATE detail_colis SET poids = ?, quantite = ?, nomcli = ?, ref_exp = ?, idcarton = ? WHERE refcde = ? AND desc_taille = ?";
            $stmt_update = $conn->prepare($update_sql);

            if ($stmt_update) {
                $stmt_update->bind_param("dississ", $poids, $quantite, $nomcli, $ref_exp, $carton_param, $refcde, $taille);

                if (!$stmt_update->execute()) {
                    $success = false;
                    echo json_encode(array('status' => 'error', 'message' => 'Erreur lors de la mise à jour des données.'));
                    break;
                }
                $stmt_update->close();
            } else {
                $success = false;
                echo json_encode(array('status' => 'error', 'message' => 'Erreur de préparation de la requête UPDATE.'));
                break;
            }
        } else {
            // Insertion d'un nouvel enregistrement
            $insert_sql = "INSERT INTO detail_colis (refcde, poids, quantite, desc_taille, nomcli, ref_exp, idcarton) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($insert_sql);

            if ($stmt_insert) {
                $stmt_insert->bind_param("sdisssi", $refcde, $poids, $quantite, $taille, $nomcli, $ref_exp, $carton_param);

                if (!$stmt_insert->execute()) {
                    $success = false;
                    echo json_encode(array('status' => 'error', 'message' => 'Erreur lors de l\'insertion des données.'));
                    break;
                }
                $stmt_insert->close();
            } else {
                $success = false;
                echo json_encode(array('status' => 'error', 'message' => 'Erreur de préparation de la requête INSERT.'));
                break;
            }
        }
        $stmt_check->close();
    } else {
        $success = false;
        echo json_encode(array('status' => 'error', 'message' => 'Erreur de préparation de la requête SELECT.'));
        break;
    }
}

// Envoie une réponse JSON finale si tout est réussi
if ($success) {
    echo json_encode(array('status' => 'success', 'message' => 'Données enregistrées ou mises à jour avec succès.'));
}

// Fermeture de la connexion à la base de données
$conn->close();
?>
