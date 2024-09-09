<?php 
include("../../admin/databases/db_sql_server.php");

// Vérification des paramètres GET
if (isset($_GET['refcde'])) {
    $RefCde = urldecode($_GET['refcde']);
    $RefCRM = $_GET['refcrm'];
    $ClientID = $_GET['clientID'];
} else {
    echo "Paramètres manquants dans l'URL.";
    exit;
}

// Requête pour récupérer les informations sur les OF
$sql = "SELECT NumOF, NomCollect, RefCde, RefCRM,
        (SELECT COUNT(*) FROM OF_Liste WHERE Clos=0 AND ClientID =".$ClientID." AND RefCRM ='".$RefCRM."' AND RefCde ='".$RefCde."') AS total_lignes 
        FROM OF_Liste 
        WHERE Clos=0 AND ClientID =".$ClientID." AND RefCRM ='".$RefCRM."' AND RefCde ='".$RefCde."'";

$res = sqlsrv_query($con, $sql);
if ($res === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Initialisation des variables
$var = 0;
$ofList = [];
// Préparation pour stockage des sommes par nom d'opération
    $sums = []; // Tableau pour stocker les sommes de finis1 par nom d'opération
while ($row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)) {
    if ($row['total_lignes'] > 1) {
        $var = $row['total_lignes'];
        $ofList[] = $row['NumOF'];
    }
}

if ($var != 0) {
    // Boucle sur chaque OF récupéré
    foreach ($ofList as $of) {
        // Appel de la procédure stockée pour chaque OF
        $sqlprocess = "{CALL dbo.rptTraceCoulTail(?)}";
        $params = [$of];
        $stmt = sqlsrv_query($con, $sqlprocess, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

            // Calcul des sommes de finis1.2.retouches par nom d'opération
            $nom_operation = $row['NomOper'];
            $finis1 = $row['Finis1'];
            $finis2 = $row['Finis2'];
            $retouches = $row['Retouches'];
            $couleur = $row['Couleur']; 
            $taille = $row['Tailles']; 
            
            // Si le nom d'opération n'existe pas encore dans le tableau, on l'initialise à 0
            if (!isset($sums[$nom_operation])) {
                  $sums[$nom_operation] = [
                    'finis1' => 0,
                    'finis2' => 0,
                    'retouches' => 0,
                    'couleurs' => [],
                    'tailles' => []
                ];
            }

            // Ajouter la valeur de finis1 à la somme correspondante
                $sums[$nom_operation]['finis1'] += $finis1;
                $sums[$nom_operation]['finis2'] += $finis2;
                $sums[$nom_operation]['retouches'] += $retouches;
            // Ajouter la couleur et la taille si elles ne sont pas déjà présentes
            if (!in_array($couleur, $sums[$nom_operation]['couleurs'])) {
                $sums[$nom_operation]['couleurs'][] = $couleur;
            }
            if (!in_array($taille, $sums[$nom_operation]['tailles'])) {
                $sums[$nom_operation]['tailles'][] = $taille;
            }
        }

        sqlsrv_free_stmt($stmt);
    }

    // Fermeture de la connexion à la base de données
    sqlsrv_close($con);

} else {
    echo $_GET['OF'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultramaille</title>
</head>
<body>
    <?php var_dump($ofList); var_dump('ligne= '.$var) ?>
    
    <!-- Affichage des résultats -->
    <?php foreach ($sums as $operation => $totals) { ?>
        <p>
            Nom opération: <?php echo $operation; ?><br>
            Somme de finis1: <?php echo $totals['finis1']; ?><br>
            Somme de finis2: <?php echo $totals['finis2']; ?><br>
            Somme de retouches: <?php echo $totals['retouches']; ?><br>
            Couleurs: <?php echo implode('/ ', $totals['couleurs']); ?><br>
            Tailles: <?php echo implode('/ ', $totals['tailles']); ?><br>
        </p>
    <?php } ?>
</body>
</html>
