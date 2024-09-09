<?php
try {
    // Connexion à la base de données SQL Server
    $dsn = "sqlsrv:Server=LAPTOP-ADJVF762;Database=ProdStockMailleDB";
    $username = "sa";
    $password = "12345678";

    // Créer une instance PDO
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Préparer l'exécution de la procédure stockée
    $sql = "{CALL dbo.rptTraceCoulTail(:of)}";
    $stmt = $pdo->prepare($sql);

    // Lier le paramètre @of
    $of = $_GET["of"];
    $stmt->bindParam(':of', $of, PDO::PARAM_INT);

    // Exécuter la procédure
    $stmt->execute();

    // Récupérer le résultat
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Afficher les résultats

    // // Si vous avez besoin du retour de valeur
    // $return_value = $pdo->query("SELECT 'Return Value' = @return_value")->fetchColumn();
    // echo "Return Value: " . $return_value;

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
  <?php foreach ($result as $row) {
        var_dump($row);
    } ?>  
</body>
</html>
