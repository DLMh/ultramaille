<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Inclure la connexion à la base de données
    include("../../admin/databases/db_to_mysql.php");

    // Vérifier si les IDs ont été envoyés
    if (isset($_POST['ids'])) {
        $ids = $_POST['ids']; // Récupérer les IDs
        $idsArray = explode(',', $ids); // Convertir la chaîne d'IDs en tableau
        $idsArray = array_map('intval', $idsArray); // Sécuriser les IDs

        // Construire la requête de suppression
        if (!empty($idsArray)) {
            $ids = implode(',', $idsArray); // Rassembler les IDs sécurisés
            $sql = "DELETE FROM depot_packing WHERE id IN ($ids)";
            

            // Exécuter la requête de suppression
            if ($conn->query($sql) === TRUE) {
                echo "Dépôts supprimés avec succès.";
            } else {
                echo "Erreur : " . $conn->error;
            }
        } else {
            echo "Aucun ID valide fourni.";
        }
    } else {
        echo "Aucun ID reçu.";
    }

    // Fermer la connexion à la base de données
    $conn->close();
} else {
    echo "Méthode de requête invalide.";
}
?>
