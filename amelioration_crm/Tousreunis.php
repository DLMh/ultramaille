<?php
// Connexion à la base de données (ajustez selon votre configuration)
$serverName = "server_name";
$connectionOptions = array(
    "Database" => "db_name",
    "Uid" => "db_user",
    "PWD" => "db_password"
);
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Traitement de la mise à jour AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idcomdet'])) {
    $idcomdet = $_POST['idcomdet'];
    $okprod = $_POST['okprod'];

    if (!empty($idcomdet) && is_numeric($okprod)) {
        // Requête pour mettre à jour les données dans la base
        $sql = "UPDATE your_table_name SET okprod = ? WHERE idcomdet = ?";
        $params = array($okprod, $idcomdet);
        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            echo json_encode(['status' => 'error', 'message' => 'Échec de la mise à jour']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'OK PROD mis à jour']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Données invalides']);
    }
    exit;
}

// Requête pour récupérer les données à afficher dans le tableau
$sql = "SELECT * FROM your_table_name";
$stmt = sqlsrv_query($conn, $sql);
$okprodParTaille = [];
$idcomdetParTaille = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $taille = $row['taille'];
    $okprodParTaille[$taille] = $row['okprod'];
    $idcomdetParTaille[$taille] = $row['idcomdet'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau Modifiable</title>
    <script>
        function updateOkProd(inputElement) {
            var newValue = inputElement.value; // La nouvelle valeur
            var idcomdet = inputElement.getAttribute('data-id'); // Récupérer l'ID

            // Création de la requête AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // Reste dans le même fichier
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            // Paramètres à envoyer avec la requête
            var params = 'idcomdet=' + idcomdet + '&okprod=' + newValue;
            
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        console.log('Mise à jour réussie');
                    } else {
                        console.error('Erreur : ' + response.message);
                    }
                } else {
                    console.error('Erreur AJAX');
                }
            };
            
            xhr.send(params); // Envoyer les données
        }
    </script>
</head>
<body>

    <table border="1">
        <thead>
            <tr>
                <th>Taille</th>
                <th>OK PROD</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($okprodParTaille as $taille => $okprod) { ?>
                <tr>
                    <td><?php echo $taille; ?></td>
                    <td>
                        <input type="number" value="<?php echo $okprod; ?>" 
                            data-id="<?php echo $idcomdetParTaille[$taille]; ?>" 
                            onchange="updateOkProd(this)">
                        ID: <?php echo $idcomdetParTaille[$taille]; ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>
</html>
