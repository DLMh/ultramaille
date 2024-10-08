 <?php
  //Initialisation des variables

 include("../../admin/databases/db_sql_server.php");
 include("../../admin/databases/db_to_mysql.php");
 

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

$var = 0;
$ofList = [];
$singleOFValues = [];
$previous_color = null; // Variable pour suivre la couleur précédente

// Préparation pour stockage des valeurs par nom d'opération et couleur
$operation_values = []; // Tableau pour stocker les valeurs par opération et couleur

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
            $nom_operation = $row['NomOper'];
            $finis1 = $row['Finis1'];
            $finis2 = $row['Finis2'];
            $retouches = $row['Retouches'];
            $couleur = $row['Couleur'];
            $taille = $row['Tailles'];
           
            // Si l'opération n'existe pas encore dans le tableau, on l'initialise
            if (!isset($operation_values[$nom_operation])) {
                $operation_values[$nom_operation] = [];
            }
            // Si la couleur n'existe pas encore pour l'opération, on l'ajoute sans somme
            if (!isset($operation_values[$nom_operation][$couleur])) {
                $operation_values[$nom_operation][$couleur] = [];
            }
            // Si la couleur n'existe pas encore pour l'opération, on l'ajoute sans somme
            if (!isset($operation_values[$nom_operation][$couleur][$taille])) {
                $operation_values[$nom_operation][$couleur][$taille] = [
                    'finis1' => $finis1,
                    'finis2' => $finis2,
                    'retouches' => $retouches
                ];
            } else {
             // Si la taille existe déjà pour cette couleur, on met à jour les sommes
                $operation_values[$nom_operation][$couleur][$taille]['finis1'] += $finis1;
                $operation_values[$nom_operation][$couleur][$taille]['finis2'] += $finis2;
                $operation_values[$nom_operation][$couleur][$taille]['retouches'] += $retouches;
            }
        }

        sqlsrv_free_stmt($stmt);
    }

    // Fermeture de la connexion à la base de données
    sqlsrv_close($con);

} else {
    $sqlprocess = "{CALL dbo.rptTraceCoulTail(?)}";
    $params = [$_GET['OF']]; // Utiliser le premier et seul OF
    $stmt = sqlsrv_query($con, $sqlprocess, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    $singleOFValues = []; // Tableau pour stocker les valeurs par opération, couleur et taille

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            // Extraction des données
            $nom_operation = $row['NomOper'];
            $couleur = $row['Couleur'];
            $taille = $row['Tailles'];
            $finis1 = $row['Finis1'];
            $finis2 = $row['Finis2'];
            $retouches = $row['Retouches'];

            // Si l'opération n'existe pas encore dans le tableau $operation_values, on l'initialise
            if (!isset($singleOFValues[$nom_operation])) {
                $singleOFValues[$nom_operation] = [];
            }

            // Si la couleur n'existe pas encore pour l'opération, on l'ajoute
            if (!isset($singleOFValues[$nom_operation][$couleur])) {
                $singleOFValues[$nom_operation][$couleur] = [];
            }

            // Si la taille n'existe pas encore pour l'opération et la couleur, on l'ajoute avec ses valeurs
            if (!isset($singleOFValues[$nom_operation][$couleur][$taille])) {
                $singleOFValues[$nom_operation][$couleur][$taille] = [
                    'finis1' => $finis1,
                    'finis2' => $finis2,
                    'retouches' => $retouches
                ];
            } else {
                // Si l'opération, la couleur et la taille existent déjà, on met à jour les valeurs
                $singleOFValues[$nom_operation][$couleur][$taille]['finis1'] += $finis1;
                $singleOFValues[$nom_operation][$couleur][$taille]['finis2'] += $finis2;
                $singleOFValues[$nom_operation][$couleur][$taille]['retouches'] += $retouches;
        }
    }
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($con);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Packing List</title>
    <link rel="stylesheet" href="../general/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&amp;display=swap">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../general/assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="../general/assets/fonts/material-icons.min.css">
    <link rel="stylesheet" href="../general/assets/css/aos.min.css">
    <link rel="stylesheet" href="../general/assets/css/Dark-Mode-Switch.css">
    <link rel="icon" href="../general/image/UTM_logo_sans_fond.png">
</head>
<body>
      <nav class="navbar navbar-expand-md sticky-top navbar-shrink py-3 navbar-light" id="mainNav">
        <div class="container"><img src="../general/assets/img/Logo-Ultramaille-1.png" style="width: 97px;"><button class="navbar-toggler" data-bs-toggle="collapse"><span class="visually-hidden">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <h1 class="text-light-emphasis" data-aos="fade-down">Ultramaille Tools Management</h1>
            <div><a href="#"><i class="fa fa-cog" style="font-size: 20px;margin-right: 20px;margin-top: 0px;"></i></a><a href="../helpdesk/nouvelle-page.php"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-circle-fill text-warning" style="margin-right: 20px;font-size: 17px;">
                <circle cx="8" cy="8" r="8"></circle>
                </svg></a><a href="https://www.facebook.com/ULTRAMAILLEKNIT" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-facebook text-primary" style="margin-right: 20px;font-size: 20px;">
                    <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"></path>
                </svg></a><a href="https://www.ultramaille.com/" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-globe2 text-info" style="font-size: 21px;margin-right: 20px;">
                    <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855-.143.268-.276.56-.395.872.705.157 1.472.257 2.282.287V1.077zM4.249 3.539c.142-.384.304-.744.481-1.078a6.7 6.7 0 0 1 .597-.933A7.01 7.01 0 0 0 3.051 3.05c.362.184.763.349 1.198.49zM3.509 7.5c.036-1.07.188-2.087.436-3.008a9.124 9.124 0 0 1-1.565-.667A6.964 6.964 0 0 0 1.018 7.5h2.49zm1.4-2.741a12.344 12.344 0 0 0-.4 2.741H7.5V5.091c-.91-.03-1.783-.145-2.591-.332zM8.5 5.09V7.5h2.99a12.342 12.342 0 0 0-.399-2.741c-.808.187-1.681.301-2.591.332zM4.51 8.5c.035.987.176 1.914.399 2.741A13.612 13.612 0 0 1 7.5 10.91V8.5H4.51zm3.99 0v2.409c.91.03 1.783.145 2.591.332.223-.827.364-1.754.4-2.741H8.5zm-3.282 3.696c.12.312.252.604.395.872.552 1.035 1.218 1.65 1.887 1.855V11.91c-.81.03-1.577.13-2.282.287zm.11 2.276a6.696 6.696 0 0 1-.598-.933 8.853 8.853 0 0 1-.481-1.079 8.38 8.38 0 0 0-1.198.49 7.01 7.01 0 0 0 2.276 1.522zm-1.383-2.964A13.36 13.36 0 0 1 3.508 8.5h-2.49a6.963 6.963 0 0 0 1.362 3.675c.47-.258.995-.482 1.565-.667zm6.728 2.964a7.009 7.009 0 0 0 2.275-1.521 8.376 8.376 0 0 0-1.197-.49 8.853 8.853 0 0 1-.481 1.078 6.688 6.688 0 0 1-.597.933zM8.5 11.909v3.014c.67-.204 1.335-.82 1.887-1.855.143-.268.276-.56.395-.872A12.63 12.63 0 0 0 8.5 11.91zm3.555-.401c.57.185 1.095.409 1.565.667A6.963 6.963 0 0 0 14.982 8.5h-2.49a13.36 13.36 0 0 1-.437 3.008zM14.982 7.5a6.963 6.963 0 0 0-1.362-3.675c-.47.258-.995.482-1.565.667.248.92.4 1.938.437 3.008h2.49zM11.27 2.461c.177.334.339.694.482 1.078a8.368 8.368 0 0 0 1.196-.49 7.01 7.01 0 0 0-2.275-1.52c.218.283.418.597.597.932zm-.488 1.343a7.765 7.765 0 0 0-.395-.872C9.835 1.897 9.17 1.282 8.5 1.077V4.09c.81-.03 1.577-.13 2.282-.287z"></path>
                </svg></a><a href="../../admin/logout.php?logout=true"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-power text-danger" style="margin-right: 0px;font-size: 27px;">
                    <path d="M7.5 1v7h1V1h-1z"></path>
                    <path d="M3 8.812a4.999 4.999 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812z"></path>
                </svg></a>
            </div>
        </div>
    </nav>
      <div class="container mt-5">
        <a href="client_lists.php">
            <button class="btn btn-primary" type="button" style="border-radius: 50%;padding: 8.6px 32px;padding-right: 10px;padding-left: 10px;padding-bottom: 10px;padding-top: 10px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-arrow-left-circle-fill" style="font-size: 41px;">
                        <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"></path>
                </svg>
            </button>
        </a>
    </div>
    <!-- zone d'utilisation mysql -->
<?php 
// Requête SQL
$totalqte=0;
$totalokprod=0;
if($RefCRM!='VIDE'){
    $sql = "SELECT desc_type,numcde,desc_ref,qte,desc_taille,ok_prod,idcomdet,desc_coul FROM `commande_mvt` WHERE idcom=".$RefCRM." and (desc_type='".$RefCde."' OR desc_ref='".$RefCde."')";
    $result = mysqli_query($conn, $sql);

// Vérifier si des résultats ont été retournés

    // Tableau pour stocker les résultats
$donnees = [];


if (mysqli_num_rows($result) > 0) {
    // Parcourir les résultats et stocker dans le tableau
    while($row = mysqli_fetch_assoc($result)) {
        $donnees[] = [
            'desc_type' => $row["desc_type"],
            'numcde'    => $row["numcde"],
            'desc_ref'  => $row["desc_ref"],
            'desc_taille' => $row["desc_taille"],
            'ok_prod' => $row["ok_prod"],
            'idcomdet'=> $row["idcomdet"],
            'qte'       => $row["qte"],
            'desc_coul'       => $row["desc_coul"]

        ];
    }
        } else {
            echo "0 résultats pour qte commande";
        }

        // Fermer la connexion
        mysqli_close($conn);
     ?>
     <?php if (!empty($donnees)) {
        $qte=0;$desc_ref=0;$desc_type=0;$numcde=0;$quantitesParTaille = array();$okprodParTaille= array();$idcomdet=0;$idcomdetParTaille= array();$idcomdetParCouleur= array();
        
    foreach ($donnees as $donnee) {
         $desc_type=$donnee['desc_type'] ;
         $numcde=$donnee['numcde'] ;
         $desc_ref=$donnee['desc_ref'] ;
         $desc_taille=$donnee['desc_taille'];
         $okprod=$donnee['ok_prod'];
         $qte=$donnee['qte'] ;
         $idcomdet=$donnee['idcomdet'];
         $desc_coul = $donnee['desc_coul']; 
        // Vérifier si la taille est déjà dans le tableau
        if (!isset($quantitesParTaille[$desc_taille])) {
            $quantitesParTaille[$desc_taille] = 0; // Initialiser la quantité pour cette taille
        }
         if (!isset($okprodParTaille[$idcomdet][$desc_taille])) {
            $okprodParTaille[$idcomdet][$desc_taille] = 0; // Initialiser l'ok prod pour cette taille
        }
        if(!isset($idcomdetParTaille[$desc_taille][$desc_coul])){
            $idcomdetParTaille[$desc_taille][$desc_coul] = 0;
        }

        // Ajouter la quantité à la taille correspondante
        $quantitesParTaille[$desc_taille] = (int)$qte;
        $okprodParTaille[$idcomdet][$desc_taille] = (int)$okprod;

        $idcomdetParTaille[$desc_taille][$desc_coul]=(int)$idcomdet;

    }
    var_dump($okprodParTaille);
 foreach ($okprodParTaille as $ids => $tailles) { 
         echo $ids; 
        foreach ($tailles as $taille => $ok) { 
             var_dump( $taille,$ok); 
      } 
   
 }
    // foreach ($quantitesParTaille as $taille => $qte) { 
    //      $totalqte+=$qte;
    // } 
    // foreach ($okprodParTaille as $taille => $ok) { 
    //      $totalokprod+=$ok;
    // } 
}
}?>
     <!-- fin zone -->
    <!-- si une seule OF -->
     <?php $grouped_by_color = [];?>
    <?php if ($var == 0) { ?>
    <div class="container mt-5">
    
        <h1 class="text-center">Suivi de production</h1>
        <h2 class="text-center">OF <?php echo $_GET['OF']?></h2>

        <div class="row mt-4">
            <div class="col-6">
     
                <?php $collection= mb_convert_encoding($_GET['collection'], 'ISO-8859-1', 'UTF-8');?>
                <p><strong>Commande </strong> <?php  echo htmlspecialchars($collection, ENT_QUOTES, 'UTF-8');?></p>
                <p><strong>DESCRIPTION:</strong> <?php echo $desc_type ?? 'n/a' ;?></p>
            </div>
            <div class="col-6 text-end">
                <p><strong>N° DE COMMANDE:</strong>   <?php echo $numcde ?? 'n/a' ?> </p>
                <p><strong>REFERENCE:</strong> <?php echo mb_convert_encoding($RefCde, 'UTF-8', 'ISO-8859-1')  ?> - <?php echo  $desc_ref ?? 'n/a' ; ?> </p>
            </div>
        </div>         
            <?php
            
            $finis1_total = 0;
            // Regrouper les opérations par couleur et taille
            foreach ($singleOFValues as $operation => $colors) {
                foreach ($colors as $couleur => $tailles) {
                    if (!isset($grouped_by_color[$couleur])) {
                        $grouped_by_color[$couleur] = [];
                    }
                    $grouped_by_color[$couleur][$operation] = $tailles; // Regrouper par taille aussi
                }
            }
            var_dump(count($grouped_by_color));
        ?>
            <div class="container mt-4">
                <h1 class="text-center mb-4">Détails des Opérations</h1>
                
                <!-- Détails des opérations -->
                <div class="row">
                    <?php foreach ($grouped_by_color as $couleur => $operations) { ?>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header  text-blue">
                                    <h5>Couleur: <?php echo $couleur; ?></h5>
                                </div>
                                <div class="card-body">
                              
                                  
                                    <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Opération</th> <!-- Colonne pour les noms d'opérations -->
                                            <?php foreach ($tailles as $taille => $values) { ?>
                                                <th><?php echo $taille; ?></th> <!-- Les tailles sont dans une seule ligne, une par colonne -->
                                            <?php } ?>
                                            <th>TOTAL</th> <!-- Colonne pour le total -->
                                            <th>EN COURS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php   $encours = 0;$totalMending=0;$totalLavage=0;$totalPose=0;$totalQC=0;$tricoter=0;?>
                                        <!-- Affichage des données par opération -->
                                        <?php foreach ($operations as $operation => $tailles) { ?>
                                            
                                            <!-- Première ligne pour l'opération -->
                                            <tr>
                                                <?php $Total=0 ?>
                                                <td class="bg-info"><?php echo mb_convert_encoding($operation, 'UTF-8', 'ISO-8859-1'); ?></td>
                                                <!-- Affiche le nom de l'opération -->
                                                <?php $op=mb_convert_encoding($operation, 'UTF-8', 'ISO-8859-1')?>
                                                <?php foreach ($tailles as $taille => $values) { ?>
                                                    <td> <?php echo $values['finis1'] ?? 'n/a'; $Total+=$values['finis1'] ?></td> <!-- Valeur finis1 -->
                                                <?php } ?>
                                                <td><?php echo $Total ?></td> <!-- Colonne vide pour le total (peut être calculé si nécessaire) -->
                                                <td> 
                                                    <?php 
                                                      
                                                        // Exemple de calcul en fonction de l'opération
                                                        if ($operation == 'Tricotage Machine Auto' || $operation=='Tricotage main' ) {
                                                            if(isset($qte)){
                                                                $tricoter=$Total;
                                                                $encours = $Total - $totalqte;
                                                            }
                                                            else{
                                                                $encours = $Total;
                                                            }
                                                            
                                                        } elseif ($operation == 'Mending' || $operation== 'Surfilage panneau') {
                                                            $totalMending=$Total;
                                                            $encours = $tricoter - $Total; // Un autre calcul
                                                    
                                                        }
                                                        elseif ($operation == 'Lavage') {
                                                            $totalLavage = $Total;
                                                              $encours = $totalLavage - $totalMending; 
                                                        }
                                                        
                                                        elseif ($operation == 'POSE ETIQUETTE'|| $operation == 'Petit_main') {
                                                            $totalPose=$Total;
                                                              $encours = $Total - $totalLavage; 
                                                        }
                                                        elseif ($op== 'Qc mending') {
                                                            $totalQC=$Total;
                                                              $encours = $Total - $totalPose; 
                                                        }

                                                        elseif ($op== 'Entrée Packing') {
                                                              $encours = $Total - $totalQC; 
                                                        }
                                                        else {
                                                            $encours = 0;
                                                        }

                                                        echo $encours;
                                                    ?>
                                                </td>
                                            </tr>
                                            
                                            <!-- Ligne suivante pour le second choix (finis2) -->
                                            <tr>
                                                <?php $somme=0 ?>
                                                <td>2eme Choix</td> <!-- Cellule vide sous l'opération -->
                                                <?php foreach ($tailles as $taille => $values) { ?>
                                                    <td><?php echo $values['finis2'] ?? '';$somme+=$values['finis2'] ?></td> <!-- Valeur finis2 -->
                                                <?php } ?>
                                                <td><?php echo $somme?></td> <!-- Colonne vide pour le total -->
                                                <td>
                                                      <?php 
                                                        $totalM=0;$totalLav=0;$tP=0;$TQC=0;$tricot=0;
                                                        // Exemple de calcul en fonction de l'opération
                                                        if ($operation == 'Tricotage Machine Auto' || $operation=='Tricotage main' ) {
                                                            if(isset($qte)){
                                                                $tricot=$somme;
                                                                $encours = $somme - $totalqte;
                                                            }
                                                            else{
                                                                $encours = $somme;
                                                            }
                                                            
                                                        } elseif ($operation == 'Mending' || $operation== 'Surfilage panneau') {
                                                            $totalM=$somme;
                                                            $encours = $tricot - $somme; // Un autre calcul
                                                    
                                                        }
                                                        elseif ($operation == 'Lavage') {
                                                            $totalLav = $somme;
                                                              $encours = $totalLav - $totalM; 
                                                        }
                                                        
                                                        elseif ($operation == 'POSE ETIQUETTE'|| $operation == 'Petit_main') {
                                                            $tP=$somme;
                                                              $encours = $somme - $totalLav; 
                                                        }
                                                        elseif ($op== 'Qc mending') {
                                                            $TQC=$somme;
                                                              $encours = $somme - $tP; 
                                                        }

                                                        elseif ($op== 'Entrée Packing') {
                                                              $encours = $somme - $TQC; 
                                                        }
                                                        else {
                                                            $encours = 0;
                                                        }

                                                        echo $encours;
                                                    ?>
                                                </td>
                                            </tr>
                                            
                                            <!-- Ligne suivante pour les retouches -->
                                            <tr>
                                                <td>Retouches</td> <!-- Cellule vide sous l'opération -->
                                                <?php $total=0 ?>
                                                <?php foreach ($tailles as $taille => $values) { ?>
                                                    <td><?php echo $values['retouches'] ?? '';$total+=$values['retouches'] ?></td> <!-- Valeur retouches -->
                                                <?php } ?>
                                                <td> <?php echo $total ?></td> <!-- Colonne vide pour le total -->
                                                <td><?php 
                                                        $RM=0;$Lav=0;$P=0;$QC=0;$tri=0;
                                                        // Exemple de calcul en fonction de l'opération
                                                        if ($operation == 'Tricotage Machine Auto' || $operation=='Tricotage main' ) {
                                                            if(isset($qte)){
                                                                $tri=$total;
                                                                $encours = $total - $totalqte;
                                                            }
                                                            else{
                                                                $encours = $total;
                                                            }
                                                            
                                                        } elseif ($operation == 'Mending' || $operation== 'Surfilage panneau') {
                                                            $RM=$total;
                                                            $encours = $tri - $total; // Un autre calcul
                                                    
                                                        }
                                                        elseif ($operation == 'Lavage') {
                                                            $Lav = $total;
                                                              $encours = $Lav - $RM; 
                                                        }
                                                        
                                                        elseif ($operation == 'POSE ETIQUETTE'|| $operation == 'Petit_main') {
                                                            $P=$total;
                                                              $encours = $total - $Lav; 
                                                        }
                                                        elseif ($op== 'Qc mending') {
                                                            $QC=$total;
                                                              $encours = $total - $P; 
                                                        }

                                                        elseif ($op== 'Entrée Packing') {
                                                              $encours = $total - $QC; 
                                                        }
                                                        else {
                                                            $encours = 0;
                                                        }

                                                        echo $encours;
                                                    ?>
                                                    </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                
                                <table class="table table-bordered  ">
                                    <thead>
                                        <tr> 
                                            
                                            
                                           <th>Situation</th>
                                    
                                           <?php  if(isset($quantitesParTaille)){?>
                                                <?php foreach ($quantitesParTaille as $taille => $quantite) { ?>
                                                    <th><?php echo $taille;  ?></th> <!-- Les tailles sont dans une seule ligne, une par colonne -->
                                                <?php } ?>
                                            <?php }else{?>
                                            
                                            <?php } ?>
                                            <th>TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Qte Commande</td>
                                             <?php  if(isset($quantitesParTaille)){?>
                                                <?php foreach ($quantitesParTaille as $taille => $qte) { ?>
                                                    <td><?php echo $qte ?? 0 ;?></td> 
                                                <?php } ?>
                                                 <td><?php echo $totalqte;?></td>
                                             <?php }else{?>
                                             
                                            <?php } ?>
                                               
                                        </tr>
                                        <tr>
                                            <td>OK SHIP(<?php $couleur_split = explode(" ", $couleur);
                                                $couleur_principale = $couleur_split[0]; echo $couleur_principale;?>)
                                            </td>
                                            <?php 
                                                    foreach ($okprodParTaille as $ids => $tailles) { 
                                                        echo $ids; 
                                                        foreach ($tailles as $taille => $ok) { 
                                                            var_dump( $taille,$ok); 
                                                    } 
                                                
                                                    }
                                            ?>
                                            <?php if (isset($okprodParTaille)) { ?>
                                            <?php foreach ($idcomdetParTaille as $taille => $couleurs) { ?>
                                                <td>
                                                    <?php 
                                                        // Affiche la valeur et rend le champ modifiable
                                                        if (isset($okprodParTaille[$taille])) {
                                                            echo '<input type="number" value="' . $okprodParTaille[$taille] . '" ';

                                                            // Boucle à travers les couleurs pour récupérer les IDs
                                                            foreach ($couleurs as $couleur => $idcomdet) {
                                                                echo 'data-id="' . $idcomdet . '" ';
                                                                
                                                            }

                                                            echo 'onchange="updateOkProd(this)">'; // Appel de la fonction AJAX lors de la modification
                                                        }
                                                    ?>
                                                    
                                                </td>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <td>Aucune donnée</td>
                                        <?php } ?>
                                            <td><?php echo $totalokprod ;?></td>
                                        </tr>
                                        <tr>
                                            <td>Reste à envoyé</td>
                                           
                                            <?php if (isset($quantitesParTaille) && isset($okprodParTaille)) { ?>
                                                <?php foreach ($quantitesParTaille as $taille => $qte) { ?>
                                                    <td>
                                                        <?php 
                                                            // Calcule la différence entre $qte et $values (par taille)
                                                            $values = $okprodParTaille[$taille] ?? 0;
                                                            $difference = $qte - $values;
                                                            echo $difference;
                                                        ?>
                                                    </td>
                                                <?php } ?>
                                                <td><?php echo $totalqte - $totalokprod; ?></td> <!-- Différence totale -->
                                            <?php } ?>
                                        </tr>
                                           <tr>
                                            <td>Difference</td>
                                             <?php foreach ($tailles as $taille => $values) { ?>
                                                    <td></td> 
                                            <?php } ?>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Pourcentage</td>
                                             <?php foreach ($tailles as $taille => $values) { ?>
                                                    <td></td> 
                                            <?php } ?>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!-- fin -->
        
   
    </div>    
    <?php } else { ?>
        <!-- debut  -->
    <?php

        // Regrouper les opérations par couleur et taille
        foreach ($operation_values as $operation => $colors) {
            foreach ($colors as $couleur => $tailles) {
                if (!isset($grouped_by_color[$couleur])) {
                    $grouped_by_color[$couleur] = [];
                }
                $grouped_by_color[$couleur][$operation] = $tailles; // Regrouper par taille aussi
            }
        }
    ?>

    <!-- <?php foreach ($grouped_by_color as $couleur => $operations) { ?>
        <h3 class="text-info">Couleur: <?php echo $couleur; ?></h3>
        <?php foreach ($operations as $operation => $tailles) { ?>
            <h4 class="text-warning">Nom opération: <?php echo $operation; ?></h4>
            <?php foreach ($tailles as $taille => $totals) { ?>
                <p>
                    Taille: <?php echo $taille; ?><br>
                    Finis1: <?php echo $totals['finis1']; ?><br>
                    Finis2: <?php echo $totals['finis2']; ?><br>
                    Retouches: <?php echo $totals['retouches']; ?><br>
                </p>
            <?php } ?>
        <?php } ?>
    <?php } ?> -->

  <div class="container mt-5">
  
            <h1 class="text-center">Suivi de production</h1>
            <h2 class="text-center">OF <?php echo $_GET['OF']?></h2>

            <div class="row mt-4">
                <div class="col-6">
                    <p><strong>Commande </strong> <?php echo $_GET['collection']?></p>
                    <p><strong>DESCRIPTION:</strong> <?php echo $desc_type ?? 'n/a' ;?></p>
                </div>
                <div class="col-6 text-end">
                    <p><strong>N° DE COMMANDE:</strong>   <?php echo $RefCRM ?> </p>
                    <p><strong>REFERENCE:</strong> <?php echo $RefCde ?> - <?php echo $desc_ref ?? 'n/a' ;?></p>
                </div>
            </div>  
            <!-- Détails des opérations -->
                <div class="row">
                    <?php foreach ($grouped_by_color as $couleur => $operations) { ?>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header  text-blue">
                                    <h5>Couleur: <?php echo $couleur; ?></h5>
                                </div>
                                <div class="card-body">
                              
                                  
                                    <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Opération</th> <!-- Colonne pour les noms d'opérations -->
                                            <?php foreach ($tailles as $taille => $values) { ?>
                                                <th><?php echo $taille; ?></th> <!-- Les tailles sont dans une seule ligne, une par colonne -->
                                            <?php } ?>
                                            <th>TOTAL</th> <!-- Colonne pour le total -->
                                            <th>En cours</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php   $encours = 0;$totalMending=0;$totalLavage=0;$totalPose=0;$totalQC=0;$tricoter=0;?>
                                        <!-- Affichage des données par opération -->
                                        <?php foreach ($operations as $operation => $tailles) { ?>
                                            <!-- Première ligne pour l'opération -->
                                            <tr>
                                                <?php $Total=0 ?>
                                                <td class="bg-info"><?php echo mb_convert_encoding($operation, 'UTF-8', 'ISO-8859-1'); ?></td> <!-- Affiche le nom de l'opération -->
                                                <?php $op=mb_convert_encoding($operation, 'UTF-8', 'ISO-8859-1')?>
                                                <?php foreach ($tailles as $taille => $values) { ?>
                                                    <td><?php echo $values['finis1'] ?? 'n/a'; $Total+=$values['finis1'] ?></td> <!-- Valeur finis1 -->
                                                <?php } ?>
                                                <td><?php echo $Total ?></td> <!-- Colonne vide pour le total (peut être calculé si nécessaire) -->
                                                <td>  <?php 
                                                     
                                                        // Exemple de calcul en fonction de l'opération
                                                        if ($operation == 'Tricotage Machine Auto' || $operation=='Tricotage main' ) {
                                                           
                                                            if(isset($qte)){
                                                                $tricoter=$Total;
                                                                $encours = $Total - $totalqte;
                                                            }else{
                                                                $encours = $Total -0;
                                                            }
                                                            
                                                        } elseif ($operation == 'Mending' || $operation== 'Surfilage panneau') {
                                                            $totalMending=$Total;
                                                            $encours = $tricoter - $Total; // Un autre calcul
                                                    
                                                        }
                                                        elseif ($operation == 'Lavage') {
                                                            $totalLavage = $Total;
                                                              $encours = $totalLavage - $totalMending; 
                                                        }
                                                        
                                                        elseif ($operation == 'POSE ETIQUETTE' || $operation == 'Petit_main') {
                                                            $totalPose=$Total;
                                                              $encours = $Total - $totalLavage; 
                                                        }
                                                        elseif ($op== 'Qc mending') {
                                                            $totalQC=$Total;
                                                              $encours = $Total - $totalPose; 
                                                        }

                                                        elseif ($op== 'Entrée Packing') {
                                                              $encours = $Total - $totalQC; 
                                                        }
                                                        else {
                                                            $encours = 0;
                                                        }

                                                        echo $encours;
                                                    ?>
                                                    </td>
                                            </tr>
                                            
                                            <!-- Ligne suivante pour le second choix (finis2) -->
                                            <tr>
                                                <?php $somme=0 ?>
                                                <td>Second choix</td> <!-- Cellule vide sous l'opération -->
                                                <?php foreach ($tailles as $taille => $values) { ?>
                                                    <td><?php echo $values['finis2'] ?? 'n/a';$somme+=$values['finis2'] ?></td> <!-- Valeur finis2 -->
                                                <?php } ?>
                                                <td><?php echo $somme?></td> <!-- Colonne vide pour le total -->
                                                <td><?php 
                                                        $totalM=0;$totalLav=0;$tP=0;$TQC=0;$tricot=0;
                                                        // Exemple de calcul en fonction de l'opération
                                                        if ($operation == 'Tricotage Machine Auto' || $operation=='Tricotage main' ) {
                                                            if(isset($qte)){
                                                                $tricot=$somme;
                                                                $encours = $somme - $totalqte;
                                                            }
                                                            else{
                                                                $encours = $somme;
                                                            }
                                                            
                                                        } elseif ($operation == 'Mending' || $operation== 'Surfilage panneau') {
                                                            $totalM=$somme;
                                                            $encours = $tricot - $somme; // Un autre calcul
                                                    
                                                        }
                                                        elseif ($operation == 'Lavage') {
                                                            $totalLav = $somme;
                                                              $encours = $totalLav - $totalM; 
                                                        }
                                                        
                                                        elseif ($operation == 'POSE ETIQUETTE'|| $operation == 'Petit_main') {
                                                            $tP=$somme;
                                                              $encours = $somme - $totalLav; 
                                                        }
                                                        elseif ($op== 'Qc mending') {
                                                            $TQC=$somme;
                                                              $encours = $somme - $tP; 
                                                        }

                                                        elseif ($op== 'Entrée Packing') {
                                                              $encours = $somme - $TQC; 
                                                        }
                                                        else {
                                                            $encours = 0;
                                                        }

                                                        echo $encours;
                                                    ?>
                                                    </td>
                                            </tr>
                                            
                                            <!-- Ligne suivante pour les retouches -->
                                            <tr>
                                                <td>Retouches</td> <!-- Cellule vide sous l'opération -->
                                                <?php $total=0 ?>
                                                <?php foreach ($tailles as $taille => $values) { ?>
                                                    <td><?php echo $values['retouches'] ?? 'n/a';$total+=$values['retouches'] ?></td> <!-- Valeur retouches -->
                                                <?php } ?>
                                                <td> <?php echo $total ?></td> <!-- Colonne vide pour le total -->
                                                <td><?php 
                                                        $RM=0;$Lav=0;$P=0;$QC=0;$tri=0;
                                                        // Exemple de calcul en fonction de l'opération
                                                        if ($operation == 'Tricotage Machine Auto' || $operation=='Tricotage main' ) {
                                                            if(isset($qte)){
                                                                $tri=$total;
                                                                $encours = $total - $totalqte;
                                                            }
                                                            else{
                                                                $encours = $total;
                                                            }
                                                            
                                                        } elseif ($operation == 'Mending' || $operation== 'Surfilage panneau') {
                                                            $RM=$total;
                                                            $encours = $tri - $total; // Un autre calcul
                                                    
                                                        }
                                                        elseif ($operation == 'Lavage') {
                                                            $Lav = $total;
                                                              $encours = $Lav - $RM; 
                                                        }
                                                        
                                                        elseif ($operation == 'POSE ETIQUETTE'|| $operation == 'Petit_main') {
                                                            $P=$total;
                                                              $encours = $total - $Lav; 
                                                        }
                                                        elseif ($op== 'Qc mending') {
                                                            $QC=$total;
                                                              $encours = $total - $P; 
                                                        }

                                                        elseif ($op== 'Entrée Packing') {
                                                              $encours = $total - $QC; 
                                                        }
                                                        else {
                                                            $encours = 0;
                                                        }

                                                        echo $encours;
                                                    ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <!-- <tfoot>
                                        <tr>
                                            <th colspan="1">Total</th>
                                            <th></th>
                                            <th colspan="2"></th>
                                            
                                        </tr>
                                    </tfoot> -->
                                </table>
                                    <h4 class="bg-secondary ">Quantité Commande: <?php echo $qte ?? 0 ;?>   </h4>
                                <table class="table table-bordered  ">
                                    <thead>
                                        <tr> 
                                           <th>Situation</th>
                                           <?php foreach ($tailles as $taille => $values) { ?>
                                                <th><?php echo $taille;  ?></th> <!-- Les tailles sont dans une seule ligne, une par colonne -->
                                            <?php } ?>
                                            <th>TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Qte Commande</td>
                                           
                                              <?php  if(isset($quantitesParTaille)){?>
                                                <?php foreach ($quantitesParTaille as $taille => $qte) { ?>
                                                    <td><?php echo $qte ?? 0 ;?></td> 
                                                <?php } ?>
                                                 <td><?php echo $totalqte;?></td>
                                             <?php }else{?>
                                             
                                            <?php } ?>
                                        </tr>
                                        <tr>
                                              <td>OK PROD(<?php $couleur_split = explode(" ", $couleur);
                                                $couleur_principale = $couleur_split[0]; echo $couleur_principale;?>)
                                            </td>
                                            <?php if (isset($okprodParTaille)) { ?>
                                            <?php foreach ($idcomdetParTaille as $taille => $couleurs) { ?>
                                                <td>
                                                    <?php 
                                                        // Affiche la valeur et rend le champ modifiable
                                                        if (isset($okprodParTaille[$taille])) {
                                                            echo '<input type="number" value="' . $okprodParTaille[$taille] . '" ';

                                                            // Boucle à travers les couleurs pour récupérer les IDs
                                                            foreach ($couleurs as $couleur => $idcomdet) {
                                                                echo 'data-id="' . $idcomdet . '" ';
                                                            }

                                                            echo 'onchange="updateOkProd(this)">'; // Appel de la fonction AJAX lors de la modification
                                                        }
                                                    ?>
                                                </td>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <td>Aucune donnée</td>
                                        <?php } ?>
                                            <td><?php echo $totalokprod ;?></td>
                                        </tr>
                                        <tr>
                                            <td>Reste à envoyé</td>
                                             <?php if (isset($quantitesParTaille) && isset($okprodParTaille)) { ?>
                                                <?php foreach ($quantitesParTaille as $taille => $qte) { ?>
                                                    <td>
                                                        <?php 
                                                            // Calcule la différence entre $qte et $values (par taille)
                                                            $values = $okprodParTaille[$taille] ?? 0;
                                                            $difference = $qte - $values;
                                                            echo $difference;
                                                        ?>
                                                    </td>
                                                <?php } ?>
                                                <td><?php echo $totalqte - $totalokprod; ?></td> <!-- Différence totale -->
                                            <?php } ?>
                                        </tr>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    
                </div>
            </div>
    <?php } ?>
    <!-- ... reste du body -->
  
<footer class="bg-primary-gradient">
    <div class="container py-4 py-lg-5">
        <div class="row justify-content-center">
            <div class="col-sm-4 col-md-3 text-center text-lg-start d-flex flex-column">
                <h3 class="fs-6 fw-bold">A propos</h3>
                <ul class="list-unstyled">
                    <li><a href="#">Entreprise</a></li>
                    <li><a href="#">Equipes</a></li>
                    <li><a href="#"><span style="color: rgb(94, 87, 87); background-color: rgba(48, 49, 52, 0);">Héritage</span><br><br></a></li>
                </ul>
            </div>
            <div class="col-lg-3 text-center text-lg-start d-flex flex-column align-items-center order-first align-items-lg-start order-lg-last"><img src="../general/assets/img/Logo-Ultramaille-1.png" style="height: 53px;">
                <p class="text-muted"><span style="color: rgb(0, 0, 0);">Spécialistes de la maille depuis 20 ans, nous sommes des tricoteurs situé à Antananarivo à Madagascar.</span></p>
            </div>
        </div>
        <hr>
        <div class="text-muted d-flex justify-content-between align-items-center pt-3">
            <p class="mb-0">Copyright © 2023 Ultramaille</p>
            <ul class="list-inline mb-0">
                <li class="list-inline-item"></li>
                <li class="list-inline-item"></li>
                <li class="list-inline-item"></li>
            </ul>
        </div>
    </div>
</footer>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="../general/assets/js/aos.min.js"></script>
<script src="../general/assets/js/bs-init.js"></script>
<script src="../general/assets/js/bold-and-bright.js"></script>
<script src="../general/assets/js/Dark-Mode-Switch-darkmode.js"></script>
<script>

  function updateOkProd(inputElement) {
    var newValue = inputElement.value; // La nouvelle valeur modifiée
    var idcomdet = inputElement.getAttribute('data-id'); // Récupérer l'ID associé
    console.log(inputElement.value);
    console.log(inputElement.getAttribute('data-id'));
    

    // Création de la requête AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_okprod.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Envoyer les paramètres : l'ID et la nouvelle valeur
    var params = 'idcomdet=' + idcomdet + '&okprod=' + newValue;

    xhr.onload = function () {
        if (xhr.status === 200) {
            console.log('Réponse brute du serveur:', xhr.responseText);
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

    xhr.send(params); // Envoie les données au serveur
}


</script>
</body>
</html>

      <!-- Affichage des résultats avec plusieurs OF -->
        <!-- <?php foreach ($operation_values as $operation => $colors) { ?>
            <?php foreach ($colors as $couleur => $totals) { ?>
            <p>
                Nom opération: <?php echo $operation; ?><br>
                Couleur: <?php echo $couleur; ?><br>
                Finis1: <?php echo $totals['finis1']; ?><br>
                Finis2: <?php echo $totals['finis2']; ?><br>
                Retouches: <?php echo $totals['retouches']; ?><br>
                Tailles: <?php echo implode('/ ', $totals['tailles']); ?><br>
            </p>
            <?php } ?>
        <?php } ?> -->