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

// Prépare et exécute l'insertion pour chaque taille disponible
$success = true; // Variable pour suivre l'état global

foreach ($poids_colis as $taille => $poids) {
    $quantite = isset($quantite_colis[$taille]) ? $quantite_colis[$taille] : 0;

    $sql = "INSERT INTO detail_colis (refcde, poids, quantite, desc_taille, nomcli, ref_exp, idcarton) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sdisssi", $refcde, $poids, $quantite, $taille, $nomcli, $ref_exp, $carton_param);
        
        if (!$stmt->execute()) {
            $success = false;
            echo json_encode(array('status' => 'error', 'message' => 'Erreur lors de l\'insertion des données.'));
            break; // Stoppe l'insertion si une erreur survient
        }
        $stmt->close();
    } else {
        $success = false;
        echo json_encode(array('status' => 'error', 'message' => 'Erreur de préparation de la requête.'));
        break;
    }
}

// Envoie une réponse JSON finale si tout est réussi
if ($success) {
    echo json_encode(array('status' => 'success', 'message' => 'Données enregistrées avec succès.'));
}

// Fermeture de la connexion à la base de données
$conn->close();
?>
