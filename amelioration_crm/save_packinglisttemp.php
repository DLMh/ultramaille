<?php
include("../../admin/databases/db_to_mysql.php");
header('Content-Type: application/json');

// Vérifie la connexion à la base de données
if (!$conn) {
    echo json_encode(array('status' => 'error', 'message' => 'Erreur de connexion : ' . mysqli_connect_error()));
    exit;
}

// Récupère les données envoyées par le client
$data = json_decode(file_get_contents('php://input'), true);

// Vérifie que les données requises sont présentes
if (isset($data['id']) && isset($data['sizes'])) {
    $idPackingList = $data['id']; // ID de la packing list parent
    $sizes = $data['sizes']; // Données des tailles et quantités

    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'error' => $conn->connect_error]);
        exit;
    }

    // Parcourt chaque taille et insère les données
    foreach ($sizes as $taille => $quantite) {
        $sql = "INSERT INTO temp_packinglist (idpacking_list, taille, quantite) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('isi', $idPackingList, $taille, $quantite);
            $stmt->execute();
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur dans la requête SQL : ' . $conn->error]);
            exit;
        }
    }

    $conn->close();

    // Renvoie une réponse de succès
    echo json_encode(['success' => true, 'message' => 'Données insérées avec succès']);
} else {
    // Renvoie une réponse d'erreur si les données sont invalides
    echo json_encode(['success' => false, 'error' => 'Invalid data received']);
}
?>

