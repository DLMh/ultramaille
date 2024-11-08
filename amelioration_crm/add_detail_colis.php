<?php
// Connexion à la base de données MySQL
include("../../admin/databases/db_to_mysql.php");

if (!$conn) {
    die(json_encode(array('status' => 'error', 'message' => mysqli_connect_error())));
}

// Récupère les valeurs du formulaire
$refcde = $_POST['reference'];
$nomcli = $_POST['nomcli'];
$ref_exp = $_POST['ref_exp'];
$poids_colis = $_POST['poids']; // Tableau des poids par taille
$quantite_colis = $_POST['quantite_colis']; // Tableau des quantités par taille

// Prépare et exécute l'insertion pour chaque taille disponible
$success = true; // Variable pour suivre l'état global

foreach ($poids_colis as $taille => $poids) {
    $quantite = isset($quantite_colis[$taille]) ? $quantite_colis[$taille] : 0;

    $sql = "INSERT INTO detail_colis (refcde, poids, quantite, desc_taille, nomcli, ref_exp) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sdisss", $refcde, $poids, $quantite, $taille, $nomcli, $ref_exp);
        
        if (!$stmt->execute()) {
            $success = false;
            echo json_encode(array('status' => 'error', 'message' => 'Erreur lors de l\'insertion des données.'));
            break; // Stoppe l'insertion si une erreur survient
        }
        $stmt->close();
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Erreur de préparation de la requête.'));
        $success = false;
        break;
    }
}

if ($success) {
    echo json_encode(array('status' => 'success', 'message' => 'Données enregistrées avec succès.'));
}

$conn->close();
?>
