<?php 
    include("../../admin/databases/db_to_mysql.php");
    if(isset($_GET['nomcli'])){
        $nomcli=$_GET['nomcli'];
        $ref_exp=$_GET['ref_exp'];
    }
$client = mysqli_real_escape_string($conn, $nomcli);
$exp = mysqli_real_escape_string($conn, $ref_exp);

$sql = "SELECT pl.*, p.* ,c.numcde,c.desc_type,c.desc_ref
        FROM `packing_list` AS pl 
        JOIN packing AS p ON p.id = pl.idpacking 
        JOIN commande_mvt c ON p.idcomdet = c.idcomdet
        WHERE pl.nomcli = '$client' AND pl.ref_exp = '$exp'";
    $result = mysqli_query($conn, $sql);

    $donnees = [];
    $idcom=0;
    $dateprevuexp=null;
   
    if (mysqli_num_rows($result) > 0) {

        while ($row = mysqli_fetch_assoc($result)) {
            $idcom=$row['idcom'];
            $dateprevuexp=$row['date_depot_packing'];
                $donnees[] = [
                    'id' => $row['id'],
                    'idpacking' => $row['idpacking'],
                    'nomcli' => $row['nomcli'],
                    'ref_exp' => $row['ref_exp'],
                    'numcde' => $row['numcde'],
                    'desc_ref' => $row['desc_ref'],
                    'desc_type' => $row['desc_type'],
                    'desc_coul' => $row['desc_coul'],
                    'desc_taille' => $row['desc_taille'],
                    'quantite' => $row['quantite'],
                    'idcom' => $row['idcom'],
                    'date_depot_packing'=> $row['date_depot_packing'],
                    'idcomdet' => $row['idcomdet']
                ];
        }
    } else {
        echo "0 information pour ce numéro d'expedition";
    }

    $sqlColis = "SELECT dc.*, dctn.dimension,dctn.poids as poidscarton
        FROM detail_colis dc join detail_carton dctn on dc.idcarton=dctn.id 
        WHERE nomcli = '$client' AND ref_exp = '$exp'";
    $resultColis = mysqli_query($conn, $sqlColis);

    $donneesColis = [];
    if (mysqli_num_rows($resultColis) > 0) {

        while ($row = mysqli_fetch_assoc($resultColis)) {
                $donneesColis[] = [
                    'id' => $row['id'],
                    'refcde' => $row['refcde'],
                    'poids' => $row['poids'],
                    'quantite' => $row['quantite'],
                    'desc_taille' => $row['desc_taille'],
                    'nomcli' => $row['nomcli'],
                    'ref_exp' => $row['ref_exp'],
                    'dimension' => $row['dimension'],
                    'poidscarton' => $row['poidscarton']
                ];
        }
    } else {
        echo "0 information pour le detail colis";
    }
    $sqldestinataire="SELECT * FROM `commande` as co  LEFT JOIN `client` as cl ON co.`idcli` = cl.`idclient`  left JOIN `client_livraison` as cliv ON co.`idcli` = cliv.`idclient` LEFT JOIN `prev_ccial` as pv ON pv.`idprev`= co.`idprev` WHERE co.`idcom`=".$idcom;
    $resultdestinataire = mysqli_query($conn, $sqldestinataire);

    $sqltypeCarton= "SELECT * FROM `detail_carton`";
    $restypeCarton=mysqli_query($conn, $sqltypeCarton);
   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Packing list</title>
    <link rel="stylesheet" href="../general/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&amp;display=swap">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../general/assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="../general/assets/fonts/material-icons.min.css">
    <link rel="stylesheet" href="../general/assets/css/aos.min.css">
    <link rel="stylesheet" href="../general/assets/css/Dark-Mode-Switch.css">
    <link rel="icon" href="../general/image/UTM_logo_sans_fond.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

      <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
    <div class="container mt-5 ">
        <h1 class="text-center">LISTE DE COLISAGE / Packing List </h1>
        <style>
            .custom-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(40%, 1fr));
                gap: 20%;
            }
        </style>
        <div class="row mt-3 custom-grid">
             <div  style="padding:0px 0px 0px 100px;flex-direction:column;">
                <label style="font-weight: bold;">EXPEDITEUR</label>
                <label> ULTRAMAILLE S.A. </label>
                <label><span style="font-weight: bold;">Tél</span> (261) 20 22 438 15 / (261) 20 22 438 16</label>
                <label><span style="font-weight: bold;">Fax</span> (261) 20 22 438 14 </label>
                <label>BP 3298 Antananarivo Madagascar</label>
            </div>
            <div class="d-flex flex-column" style="align-items: end; ">
                
                <label style="font-weight: bold;">DESTINATAIRE</label>
                <label> <?php echo $client ?> </label>
                <?php if (mysqli_num_rows($resultdestinataire) > 0) { ?>
                    <select class="form-select form-select-lg mb-3 w-75" name="" id="" style="white-space: normal;"> 
                        <?php while ($row = mysqli_fetch_assoc($resultdestinataire)) { ?>
                            <option value="<?php echo $row['adresse_livr'] ?? $row['adr_fact']; ?>">
                                <?php echo $row['adresse_livr'] ?? $row['adr_fact']; ?>
                            </option>
                        <?php } ?>
                    </select>
                <?php } ?>
              
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-3 d-flex" style="padding: 20px;border: solid 1px;  flex-direction:column;align-items:center;">
                <label ><span style="font-weight: bold;">Date:</span> <?php echo $dateprevuexp ;?></label>
                <label> <?php echo $exp ?></label>
            </div>
        </div>
        <div class="row mt-3">
        <?php if (!empty($donnees)) {
            
                $quantiteMap = [];
                foreach ($donneesColis as $colisRow) {
                    $quantiteMap[$colisRow['refcde']][$colisRow['desc_taille']] = [
                        'quantite' => $colisRow['quantite'], 
                        'poids' => $colisRow['poids'],
                        'dimension' => $colisRow['dimension'],
                        'poidscarton' => $colisRow['poidscarton']
                    ];
                }
                // Récupérer toutes les tailles uniques pour les en-têtes
                // Définir l'ordre personnalisé des tailles
                $tailleOrder = ['XS','S', 'M', 'L', 'XL', '2XL'];

                // Extraire les tailles uniques à partir des données
                $tailles = array_unique(array_column($donnees, 'desc_taille'));
                echo "<script> const tailles = " . json_encode($tailles) . "; </script>";

                // Trier les tailles en fonction de l'ordre défini, avec les tailles inconnues en dernier
                usort($tailles, function ($a, $b) use ($tailleOrder) {
                    $posA = array_search($a, $tailleOrder);
                    $posB = array_search($b, $tailleOrder);

                    // Si $a ou $b n'est pas dans $tailleOrder, ils obtiennent une position après les tailles définies
                    $posA = ($posA === false) ? count($tailleOrder) : $posA;
                    $posB = ($posB === false) ? count($tailleOrder) : $posB;

                    return $posA - $posB;
                });    
        ?>
            <div class="table-container overflow-auto" style="max-height: 700px;">
                <!-- <button id="regrouperCartonsBtn" class="btn btn-primary mb-2">Regrouper les cartons</button> -->
                <table  class="table table-bordered table-hover" style="width:100%" id="packingListTable">
                    <thead class="sticky-top bg-white">
                        <tr>
                            <!-- <th rowspan="2"></th> -->
                            <th rowspan="2">N° CTN</th>
                            <th rowspan="2">N° Commande</th>
                            <th rowspan="2">Reference</th>
                            <th rowspan="2">Designation</th>
                            <th rowspan="2">Couleur</th>
                            <th rowspan="2">Nbre Ctn</th>
                            <th colspan="<?php echo count($tailles); ?>">TAILLE</th> <!-- Utilisation de colspan pour englober toutes les tailles -->
                            <th rowspan="2">TOTAL</th>
                            <th rowspan="2">Poids Brut/CTN(kg)</th>
                            <th rowspan="2">Poids Brut total</th>
                            <th rowspan="2">Poids NET/CTN(kg)</th>
                            <th rowspan="2">Poids NET total</th>
                            <th rowspan="2">Carton</th>
                            <th colspan="4" rowspan="2">Action</th>
                        </tr>

                        <tr>
                            <!-- Afficher chaque taille dans un en-tête <th> -->
                            <?php foreach ($tailles as $taille) : ?>
                                <th class="tall"><?php echo $taille; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="sticky-body">
                        <?php 
                            $a = 1;
                            $b = 0;
                            $totalnbrcarton=0;
                            $TotalPB=0;
                            $TotalPN=0;
                            $TotalPiece=0;
                            $dimensionCartonSum = [];
                        foreach ($donnees as $row) {
                            $PBC = 0;
                            $PNC = 0;
                            $nbr_carton = 0;
                            $reste = 0;
                            $total = 0;
                            $dimension = isset($quantiteMap[$row['desc_ref']][$row['desc_taille']]['dimension']) 
                                ? $quantiteMap[$row['desc_ref']][$row['desc_taille']]['dimension'] 
                                : 'N/A';
                            $poidsctn = isset($quantiteMap[$row['desc_ref']][$row['desc_taille']]['poidscarton']) 
                                ? $quantiteMap[$row['desc_ref']][$row['desc_taille']]['poidscarton'] 
                                : 0;

                            if (isset($quantiteMap[$row['desc_ref']][$row['desc_taille']])) {
                                $quantiteParCarton = $quantiteMap[$row['desc_ref']][$row['desc_taille']]['quantite'];
                                $poidsParCarton = $quantiteMap[$row['desc_ref']][$row['desc_taille']]['poids'];
                                
                                if ($row['quantite'] < $quantiteParCarton) {
                                    // Ajouter directement une ligne pour le reste
                                    $reste = $row['quantite'];
                                    $b++; // Incrémenter B pour cette ligne
                                    $nbrctn = 1;
                                    $dimensionCartonSum[$dimension] = isset($dimensionCartonSum[$dimension]) ? $dimensionCartonSum[$dimension] + $nbrctn : $nbrctn;
                                    $totalnbrcarton += $nbrctn;
                                    $TotalPiece += $reste;

                                    ?>
                                    <tr 
                                        data-id="<?php echo $row['id']; ?>"    
                                        data-poidsParCarton="<?php echo $poidsParCarton; ?>" 
                                        data-poidsCtn="<?php echo $poidsctn; ?>"
                                        data-dimension="<?= $dimension; ?>" 
                                        data-numeroCarton="<?php echo $b; ?>"
                                        style="cursor:pointer;">
                                        <td data-numeroCarton><?php echo "$b"; ?></td>
                                        <td><?php echo $row['numcde']; ?> </td>
                                        <td><?php echo $row['desc_ref']; ?></td>
                                        <td><?php echo $row['desc_type']; ?></td>
                                        <td><?php echo $row['desc_coul']; ?></td>
                                        <td data-nbrctn class="bg-warning text-dark"><?php echo $nbrctn; ?></td>
                                        <!-- Afficher le reste pour la taille correspondante -->
                                        <?php foreach ($tailles as $taille) : ?>
                                            <td class="tall" data-taille="<?= $taille; ?>">
                                                <?php echo $row['desc_taille'] === $taille ? $reste : ''; ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <td data-total><?php echo $total = $reste * $nbrctn; ?></td>
                                        <td data-pbc><?php echo $PBC = ($reste * $poidsParCarton) + $poidsctn; ?></td>
                                        <td data-totalpbc><?php echo $TotalPBC = $PBC * $nbrctn; $TotalPB += $TotalPBC; ?></td>
                                        <td data-pnc><?php echo $PNC = $reste * $poidsParCarton; ?></td>
                                        <td data-totalpnc><?php echo $TotalPNC = $PNC * $nbrctn; $TotalPN += $TotalPNC; ?></td>
                                        <td data-dimension class="dimension-cell"><?php echo $dimension; ?></td>
                                        <td><i class="fa fa-edit" title="Modifier" style="color:green; cursor:pointer;" onclick="enableEditing(this)"></i></td>
                                        <td><i class="fa fa-trash" title="Supprimer" style="color:red;" onclick="removeRow(this)"></i></td>
                                        <td>
                                            <i class="fa fa-link" title="Joindre" style="color:blue; cursor:pointer;" 
                                                onclick="joinRows(this, <?php echo $b; ?>)">
                                            </i>
                                        </td>
                                        <td><i class="fa fa-plus" title="Ajouter" style="color:#17a2b8;" onclick="addRow(this)"></i></td>
                                    </tr>
                            <?php
                                continue; // Passer directement à la prochaine ligne du tableau
                                } else {
                                        $nbr_carton = floor($row['quantite'] / $quantiteParCarton);
                                        $reste = $row['quantite'] - ($nbr_carton * $quantiteParCarton);
                                        $totalnbrcarton += $nbr_carton;
                                    }
                                }
                            
                        
                                if (!isset($dimensionCartonSum[$dimension])) {
                                    $dimensionCartonSum[$dimension] = 0; 
                                }
                                $dimensionCartonSum[$dimension] += $nbr_carton;
                                // Calculer A et B pour cette ligne
                                $a = $b + 1;
                                $b = $b + $nbr_carton; 
                                // Calculer la quantité totale pour chaque référence
                            ?>
                            
                            <tr     
                                data-id=<?php echo $row['id']; ?> 
                                style="cursor:pointer;"
                            >
                                <?php if ($a !== $b ){?>
                                <td><?php echo "$a à $b"; ?></td>
                                <?php }else { ?>
                                <td><?php echo "$b"; ?></td>
                                <?php }?>
                                <td><?php echo $row['numcde'] ;?></td>
                                <td><?php echo $row['desc_ref'] ?></td>
                                <td><?php echo $row['desc_type'] ?></td>
                                <td><?php echo $row['desc_coul']?></td>
                                <td data-nbrctn class="carton-count text-dark">
                                    <?php
                                    
                                    if (isset($quantiteMap[$row['desc_ref']][$row['desc_taille']])) {
                                            echo $nbr_carton;
                                        } else {
                                            echo 'N/A';
                                        }
                                    ?>
                                </td>
                            
                                <!-- Afficher les quantités pour chaque taille -->
                                <?php foreach ($tailles as $taille) : ?>
                                    <td class="tall" data-taille="<?= $taille; ?>">
                                        <?php 
                                        if($row['quantite']<$quantiteParCarton){
                                            echo $row['desc_taille'] === $taille ? $row['quantite'] : '';
                                        }else{
                                            echo $row['desc_taille'] === $taille ? $quantiteParCarton : '';
                                        }
                                            
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                                <?php 
                                    if($row['quantite']<$quantiteParCarton){
                                        $total=$row['quantite']*$nbr_carton;
                                    }else{
                                        $total=$quantiteParCarton*$nbr_carton;
                                    }
                                    $PBC= ($total*$poidsParCarton)+$poidsctn;
                                    $PNC= $total*$poidsParCarton;
                                    $TotalPBC=$PBC*$nbr_carton;
                                    $TotalPNC=$PNC*$nbr_carton;   
                                    $TotalPB+=$TotalPBC;
                                    $TotalPN+=$TotalPNC;
                                    $TotalPiece+=$total;
                                ?>
                                <td data-total><?php  echo $total ; ?></td>
                                <td data-pbc><?php  echo $PBC ; ?></td>
                                <td data-totalpbc><?php  echo $TotalPBC ; ?></td>
                                <td data-pnc><?php  echo $PNC;  ?></td>
                                <td data-totalpnc><?php  echo $TotalPNC ; ?></td>
                                <td><?php echo $dimension; ?></td>
                                <style>
                                    td.tall {
                                        min-width: 50px; /* Largeur minimale pour les colonnes de tailles */
                                        text-align: center; /* Centre le contenu */
                                    }

                                    td.tall input {
                                        width: 100%; /* Ajuste la taille de l'input */
                                        text-align: center; /* Centre le texte dans l'input */
                                        border: 1px;
                                        padding: 1px;
                                        border-radius: 4px;
                                    }

                                    td.tall input:focus {
                                        outline: none;
                                        border-color: #007bff; /* Couleur de focus */
                                    }


                                </style>
                                <td colspan="4"></td>
                                
                            </tr>
                            <?php 
                            // Ajouter une ligne pour le reste s'il existe
                            if ($reste > 0) { 
                                $b++; // Incrémenter B pour la nouvelle ligne
                                $nbrctn=1;
                                $dimensionCartonSum[$dimension] += $nbrctn;
                                $totalnbrcarton+=$nbrctn;
                                $TotalPiece+=$reste*$nbrctn;
                            ?>
                                <tr 
                                    data-id=<?php echo $row['id']; ?>    
                                    data-poidsParCarton="<?php echo $poidsParCarton; ?>" 
                                    data-poidsCtn="<?php echo $poidsctn; ?>"
                                    data-dimension="<?= $dimension; ?>" 
                                    onclick="window.location.href='#';" style="cursor:pointer;">
                                    <td data-numeroCarton><?php echo "$b"; ?></td>
                                    <td><?php echo $row['numcde'] ;?></td>
                                    <td><?php echo $row['desc_ref'] ?></td>
                                    <td><?php echo $row['desc_type'] ?></td>
                                    <td><?php echo $row['desc_coul']?></td>
                                    <td data-nbrctn class="bg-warning text-dark"><?php echo $nbrctn ;?></td>
                                    
                                    <!-- Afficher le reste pour la taille correspondante -->
                                    <?php foreach ($tailles as $taille) : ?>
                                        <td class="tall" data-taille="<?= $taille; ?>">
                                            <?php
                                                echo $row['desc_taille'] === $taille ? $reste : '';
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td data-total><?php echo   $total= $reste*$nbrctn ;?></td>
                                    <td data-pbc><?php echo $PBC=($reste*$poidsParCarton)+$poidsctn ;?></td>
                                    <td data-totalpbc><?php echo $TotalPBC=(($reste*$poidsParCarton)+$poidsctn)*$nbrctn ;$TotalPB+=(($reste*$poidsParCarton)+$poidsctn)*$nbrctn;?></td>
                                    <td data-pnc><?php echo $PNC=$reste*$poidsParCarton ; ?></td>
                                    <td data-totalpnc ><?php echo $TotalPNC=($reste*$poidsParCarton)*$nbrctn ; $TotalPN+=($reste*$poidsParCarton)*$nbrctn;?></td>
                                    <td data-dimension  class="dimension-cell"><?php echo $dimension; ?></td>
                                    <td><i class="fa fa-edit" title="Modifier" style="color:green; cursor:pointer;" onclick="enableEditing(this)"></i></td>
                                    <td><i class="fa fa-trash" title="Supprimer" style="color:red;" onclick="removeRow(this)"></i></td>
                                    <td>
                                        <i class="fa fa-link" title="Joindre" style="color:blue; cursor:pointer;" 
                                            onclick="joinRows(this, <?php echo $b; ?>)">
                                        </i>
                                    </td>
                                    <td><i class="fa fa-plus" title="Ajouter" style="color:#17a2b8;" onclick="addRow(this)"></i></td>
                                </tr>
                            <?php } ?>
                        <?php }?>
                    </tbody>
                   <!-- Modal -->
                    <div class="modal fade" id="cartonModal" tabindex="-1" aria-labelledby="cartonModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="cartonModalLabel">Modifier les dimensions du carton</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Affichage de l'ID du carton -->
                                    <p><strong>ID du Carton :</strong> <span id="cartonId" class="text-primary"></span></p>

                                    <!-- Stockage de l'ID en input caché -->
                                    <input type="hidden" id="cartonIdHidden">

                                    <!-- Sélecteur de dimension -->
                                    <label for="cartonSelect" class="form-label">Choisissez une dimension :</label>
                                    <?php if (mysqli_num_rows($restypeCarton) > 0) { ?>
                                        <select id="cartonSelect" class="form-select">
                                            <?php while ($rowCarton = mysqli_fetch_assoc($restypeCarton)) { ?>
                                                <option value="<?php echo $rowCarton['dimension']; ?>">
                                                    <?php echo $rowCarton['dimension']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>

                                    <!-- Affichage de la dimension sélectionnée -->
                                    <p class="mt-3"><strong>Dimension :</strong> <span id="selectedDimension" class="text-success"></span></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                                        <i class="fas fa-times"></i> Annuler
                                    </button>
                                    <button type="button" class="btn btn-primary" id="validateButton">
                                        <i class="fas fa-check"></i> Valider
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>



                    <tfoot>
                        <tr id="totals-row">
                            <th colspan="6" style="text-align: right;">Totaux</th>
                            <!-- Ajouter une cellule pour chaque taille -->
                            <?php foreach ($tailles as $taille): ?>
                                <th data-taille-total="<?= $taille; ?>">0</th>
                            <?php endforeach; ?>
                            <th data-total-final="pieces" data-total-piece="<?= $TotalPiece; ?>">0</th>
                            <th data-total-final="pbc">0</th>
                            <th data-total-final="totalpbc">0</th>
                            <th data-total-final="pnc">0</th>
                            <th data-total-final="totalpnc">0</th>
                            <th colspan="4"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="row mt-3">
                <table class="table table-bordered" id="idtotauxrecap">
                    <tbody>
                        <tr>
                            <td>TOTAL NOMBRE DES PIECES</td>
                            <td><?php echo $TotalPiece ;?></td>
                        </tr>
                        <tr>
                            <td>
                                TOTAL NOMBRE DES CARTONS
                            </td>
                            <td id="totalNbrCarton"><?php echo $totalnbrcarton;?></td>
                        </tr>
                        <tr>
                            <td>
                                TOTAL POIDS BRUT/KG
                            </td>
                            <td id="totalPB" data-totalPB="<?php echo $TotalPB ;?>"> <?php echo $TotalPB ;?></td>
                        </tr>
                        <tr>
                            <td>
                                TOTAL POIDS NET/KG
                            </td>
                            <td id="totalPN" data-totalPN="<?php echo $TotalPN ;?>"> <?php echo $TotalPN ;?></td>
                        </tr>
                        
                        <?php 
                        $volume=0;
                        foreach ($dimensionCartonSum as $dimension => $totalCartons) { 
                           
                            $dimensionParts = explode('*', $dimension); 
                            $decimalDimension = 1; 
                            

                            foreach ($dimensionParts as $part) {
                                $decimalDimension *= ($part / 100); 
                             
                            }
                         
                        ?>
                            <tr data-dimensionrecap="<?= $dimension; ?>"
                                data-decimaldimension="<?= htmlspecialchars($decimalDimension, ENT_QUOTES, 'UTF-8'); ?>">
                                <td id="decimaldimension">
                                    REFERENCE DES CARTONS: <?php echo $dimension . " (". $decimalDimension . " m³)"; ?>
                                </td>                                
                                <td id="totalcartons" data-totalcartons="<?= $totalCartons; ?>"><?php echo $totalCartons; $volume+=$decimalDimension*$totalCartons; ?></td>
                            </tr>
                        <?php } ?> 
                        <tr>
                            <td>
                                VOLUME
                            </td>
                            <td id="volume"><?php echo $volume . " m³"; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
           <?php
            $tailleOrder = ['XS', 'S', 'M', 'L', 'XL', '2XL']; // Ordre des tailles défini

            function sortByCustomOrder(array $tailles, array $tailleOrder): array {
                usort($tailles, function ($a, $b) use ($tailleOrder) {
                    $posA = array_search($a, $tailleOrder);
                    $posB = array_search($b, $tailleOrder);

                    // Si une taille n'est pas trouvée, on la place à la fin
                    $posA = $posA === false ? PHP_INT_MAX : $posA;
                    $posB = $posB === false ? PHP_INT_MAX : $posB;

                    return $posA <=> $posB;
                });

                return $tailles;
            }

            // Regrouper les données par référence et couleur
            $groupedData = []; // Structure pour regrouper les données

            foreach ($donnees as $r) {
                $key = $r['desc_ref'] . '|' . $r['desc_coul']; // Clé unique pour chaque combinaison

                if (!isset($groupedData[$key])) {
                    $groupedData[$key] = [
                        'numcde' => $r['numcde'],
                        'desc_type' => $r['desc_type'],
                        'desc_ref' => $r['desc_ref'],
                        'desc_coul' => $r['desc_coul'],
                        'tailles' => [] // Associer tailles à quantités
                    ];
                }

                $groupedData[$key]['tailles'][$r['desc_taille']] = $r['quantite']; // Associer la taille à sa quantité
            }

            // Collecter toutes les tailles uniques
            $allTailles = [];
            foreach ($groupedData as $group) {
                $allTailles = array_merge($allTailles, array_keys($group['tailles']));
            }
            $allTailles = array_unique($allTailles); // Supprimer les doublons
            $allTailles = sortByCustomOrder($allTailles, $tailleOrder); // Trier les tailles selon l'ordre personnalisé
            ?>

            <div class="row d-none">
                <h3>RECAPITULATIF DE LA COMMANDE</h3>
                <table class="table table-bordered" id="recapitulatif">
                    <thead>
                        <tr>
                            <th>N° Commande</th>
                            <th>Référence</th>
                            <th>Désignation</th>
                            <th>Couleur</th>
                            <?php foreach ($allTailles as $taille): ?>
                                <th>Taille <?= $taille ?></th>
                            <?php endforeach; ?>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  $bigtotal=0;
                        foreach ($groupedData as $group): ?>
                        <tr>
                            <td><?= $group['numcde']; ?></td>
                            <td><?= $group['desc_ref']; ?></td>
                            <td><?= $group['desc_type']; ?></td>
                            <td><?= $group['desc_coul']; ?></td>
                            <?php
                            $total = 0;

                            // Parcourir toutes les tailles uniques pour générer les colonnes
                            foreach ($allTailles as $taille) {
                                $quantite = $group['tailles'][$taille] ?? 0; // Quantité ou 0 si non défini
                                echo "<td>" . ($quantite > 0 ? $quantite : '') . "</td>";
                                $total += $quantite; // Calculer le total pour cette ligne
                            }
                            ?>
                            <td><?= $total; $bigtotal+=$total ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <th colspan="9"></th>
                        <th><?php echo $bigtotal;?></th>
                    </tfoot>
                </table>
            </div>

            <div class="row" >
                <div class="col mb-4">
                    <div class="card">
                        <div class="card-body" style="display:flex;justify-content:center;">
                            <div>
                                <button 
                                    class="btn btn-outline-dark rounded-0 valider-btn" 
                                    data-ref-exp="<?php echo $exp;?>"  
                                    data-nomcli="<?php echo $client;?>" 
                                    onclick="handleValidation()">Valider
                                </button>
                                
                                <button  
                                    class="btn btn-outline-info rounded-0" 
                                    onclick="exportToPDF(); handlePDFExport();">
                                    Exporter en PDF
                                </button>
                                
                                <button 
                                    class="btn btn-outline-primary rounded-0" 
                                    onclick="exportToExcel()">
                                    Exporter en Excel
                                </button>
                                
                                <a 
                                    href="expedition_list.php" 
                                    class="btn btn-outline-secondary rounded-0  d-none" 
                                    id="goToExpeditionBtn"
                                   
                                     title="Retour à la liste d'expédition">
                                <i class="fa-solid fa-house" style="font-size: 1rem;"></i>

                                </a>
                            </div>
                        
                        </div>
                    </div>  
                </div>       
            </div>
        <?php } ?>
       
        </div>
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
   
    <script>
        let firstRow = null; // Variable pour stocker la première ligne cliquée

        function joinRows(button, numeroCarton) {
            const row = button.closest("tr"); // Trouver la ligne actuelle
            
            if (!firstRow) {
                // Premier clic
                firstRow = row;
                firstRow.style.setProperty("background-color", "red", "important");
                alert("Première ligne sélectionnée. Sélectionnez une deuxième ligne pour joindre.");
                console.log(firstRow);
            } else {
                // Deuxième clic
                if (row === firstRow) {
                    alert("Vous ne pouvez pas joindre la même ligne. Réessayez.");
                    firstRow.style.backgroundColor = ""; // Réinitialiser la couleur
                    firstRow = null;
                    return;
                }
                
                // Récupérer les données des deux lignes
                const pn1 = parseFloat(firstRow.querySelector("[data-pnc]").textContent) || 0;
                const pn2 = parseFloat(row.querySelector("[data-pnc]").textContent) || 0;
                const pb2 = parseFloat(row.querySelector("[data-pbc]").textContent) || 0;
                
                const nbCarton1 = parseInt(firstRow.querySelector("[data-nbrctn]").textContent) || 0;
                const numeroCarton1 = firstRow.querySelector("[data-numeroCarton]").textContent || "";
                const dimension1 = firstRow.dataset.dimension || "Inconnue";
                  // Recalcul dimension pour le premier clic
                let decimalDimension1 = recalculateDecimalDimension(dimension1);

                // Appeler la fonction updateTotalNbrCarton pour le premier clic
                updateTotalNbrCarton(-nbCarton1, decimalDimension1, dimension1);
                // Calculs
                const newPn = pn1 + pn2; // Nouveau Poids Net
                const newPb = pn1 + pb2; // Nouveau Poids Brut

                // Mettre à jour la deuxième ligne
                row.querySelector("[data-pnc]").textContent = newPn.toFixed(2);
                row.querySelector("[data-pbc]").textContent = newPb.toFixed(2);


                // Vider les valeurs de la première ligne
                firstRow.querySelectorAll("[data-pbc], [data-totalpbc], [data-pnc], [data-totalpnc],[data-dimension]").forEach(cell => {
                    cell.textContent = " ";
                });
                firstRow.querySelector("[data-nbrctn]").textContent = ""; // Nombre de cartons à 0
                firstRow.querySelector("[data-numeroCarton]").textContent = ""; // Numéro de carton à 0
                // Déplacer la première ligne sous la deuxième ligne
                row.parentNode.insertBefore(firstRow, row.nextSibling);

                // Réinitialiser la couleur et la variable
                firstRow.style.backgroundColor = ""; 
                firstRow = null; 
               
                updateTotals();
                alert("Les lignes ont été jointes et déplacées avec succès.");
            }
        }

        

        function updateTotals() {
            const table = document.querySelector('.sticky-body'); // Cible votre tableau
            const totalsRow = document.getElementById('totals-row'); // Ligne des totaux

            // Initialiser les totaux
            const totals = {
                sizes: {},
                pieces: 0,
                pbc: 0,
                totalpbc: 0,
                pnc: 0,
                totalpnc: 0,
            };

            // Parcourir chaque ligne visible pour accumuler les valeurs
            const rows = table.querySelectorAll('tr');
            rows.forEach((row) => {
                if (row.style.display === 'none' ) return; //ignore ligne //|| parseInt(row.querySelector("[data-nbrctn]").textContent) === 0 // ilaina ito 
                // Ajouter les tailles
                const sizeCells = row.querySelectorAll('td.tall');
                sizeCells.forEach((cell) => {
                    const taille = cell.dataset.taille;
                    const value = parseInt(cell.innerText.trim()) || 0;
                    totals.sizes[taille] = (totals.sizes[taille] || 0) + value;
                });

                // Ajouter les autres valeurs
                totals.pieces += parseInt(row.querySelector('[data-total]')?.innerText.trim() || 0);
                totals.pbc += parseFloat(row.querySelector('[data-pbc]')?.innerText.trim() || 0);
                totals.totalpbc += parseFloat(row.querySelector('[data-totalpbc]')?.innerText.trim() || 0);
                totals.pnc += parseFloat(row.querySelector('[data-pnc]')?.innerText.trim() || 0);
                totals.totalpnc += parseFloat(row.querySelector('[data-totalpnc]')?.innerText.trim() || 0);
            });

            // Mettre à jour la ligne des totaux
            Object.keys(totals.sizes).forEach((taille) => {
                const sizeCell = totalsRow.querySelector(`[data-taille-total="${taille}"]`);
                if (sizeCell) sizeCell.innerText = totals.sizes[taille];
            });

            // Mettre à jour les totaux principaux
            totalsRow.querySelector('[data-total-final="pieces"]').innerText = totals.pieces;
            totalsRow.querySelector('[data-total-final="pbc"]').innerText = totals.pbc.toFixed(2);
            totalsRow.querySelector('[data-total-final="totalpbc"]').innerText = totals.totalpbc.toFixed(2);
            totalsRow.querySelector('[data-total-final="pnc"]').innerText = totals.pnc.toFixed(2);
            totalsRow.querySelector('[data-total-final="totalpnc"]').innerText = totals.totalpnc.toFixed(2);

            // Vérification du total des pièces
            const piecesCell = totalsRow.querySelector('[data-total-final="pieces"]');
            const serverTotalPieces = parseInt(piecesCell.dataset.totalPiece, 10) || 0;

            // Appliquer la classe bg-danger si les totaux ne correspondent pas
            if (totals.pieces !== serverTotalPieces) {
                piecesCell.classList.add('bg-danger');
            } else {
                piecesCell.classList.remove('bg-danger');
            }
             // Mettre à jour le TOTAL POIDS BRUT/KG
            const totalPBElement = document.querySelector('td[data-totalPB]');
            if (totalPBElement) {
                totalPBElement.setAttribute('data-totalPB', totals.totalpbc);
                totalPBElement.innerText = totals.totalpbc.toFixed(2);
            }

            // Mettre à jour le TOTAL POIDS NET/KG
            const totalPNElement = document.querySelector('td[data-totalPN]');
            if (totalPNElement) {
                totalPNElement.setAttribute('data-totalPN', totals.totalpnc);
                totalPNElement.innerText = totals.totalpnc.toFixed(2);
            }
        }


        function enableEditing(editButton) {
            const row = editButton.closest('tr'); // Ligne actuelle
            const sizeColumns = row.querySelectorAll('td.tall'); // Colonnes des tailles

            sizeColumns.forEach((cell) => {
                if (!cell.querySelector('input')) {
                    const currentValue = cell.innerText.trim();
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.value = currentValue;
                    input.style.width = '100%';
                    cell.innerHTML = '';
                    cell.appendChild(input);
                }
            });

            // Modifier le bouton en "Enregistrer"
            editButton.className = 'fa fa-save';
            editButton.title = 'Enregistrer';
            editButton.style.color = 'orange';
            editButton.onclick = () => saveChanges(editButton);
        }

        function saveChanges(saveButton) {
            const row = saveButton.closest('tr');
            const rowData = {
                id: row.dataset.id,
                sizes: {},
                total: 0,
                poidsParCarton: parseFloat(row.dataset.poidsparcarton) || 0,
                poidsCtn: parseFloat(row.dataset.poidsctn) || 0,
                nbr_carton: parseInt(row.querySelector('.bg-warning.text-dark').innerText) || 1,
            };


            // Mettre à jour les tailles
            const sizeColumns = row.querySelectorAll('td.tall');
            sizeColumns.forEach((cell) => {
                const input = cell.querySelector('input');
                const taille = cell.dataset.taille;
                if (input) {
                    const value = parseInt(input.value.trim()) || 0;
                    cell.innerText = value > 0 ? value : '';
                    rowData.sizes[taille] = value;
                    rowData.total += value;
                }
            });
            console.log("nbrectn",row.querySelector("[data-nbrctn]").innerText);
            let totalPieces = 0;
            let PBC = 0;
            let PNC = 0;
            let TotalPBC = 0;
            let TotalPNC = 0;

            if(row.querySelector("[data-nbrctn]").innerText ===""){
                PBC = 0;
                totalPieces = rowData.total * rowData.nbr_carton;
            }else{
                totalPieces = rowData.total * rowData.nbr_carton;
                PBC = (totalPieces * rowData.poidsParCarton) + rowData.poidsCtn;
                PNC = totalPieces * rowData.poidsParCarton;
                TotalPBC = PBC * rowData.nbr_carton;
                TotalPNC = PNC * rowData.nbr_carton;
            }
            

            // Mettre à jour les colonnes correspondantes
            row.querySelector('[data-total]').innerText = totalPieces;
            row.querySelector('[data-pbc]').innerText = PBC.toFixed(2);
            row.querySelector('[data-pnc]').innerText = PNC.toFixed(2);
            row.querySelector('[data-totalpbc]').innerText = TotalPBC.toFixed(2);
            row.querySelector('[data-totalpnc]').innerText = TotalPNC.toFixed(2);

            // Changer le bouton en "Modifier"
            saveButton.className = 'fa fa-edit';
            saveButton.title = 'Modifier';
            saveButton.style.color = 'green';
            saveButton.onclick = () => enableEditing(saveButton);

            // Mettre à jour les totaux
            updateTotals();
        }

        // Appel initial
        document.addEventListener('DOMContentLoaded', () => updateTotals());
    </script>
    <script>

        function recalculateDecimalDimension(dimension) {
            if (!dimension) return 0;

            const parts = dimension.split('*');
            let decimalDimension = 1;

            parts.forEach(part => {
                const value = parseFloat(part.trim());
                if (!isNaN(value)) {
                    decimalDimension *= value / 100;
                }
            });

            return decimalDimension;
        }
        function removeRow(element) {
            if (confirm("Êtes-vous sûr de vouloir supprimer cette ligne ?")) {
                // Accéder à la ligne parent (tr)
                const row = element.closest('tr');
                
                // Extraire le nombre de cartons (nbrctn) depuis la cellule correspondante
                const nbrctn = parseInt(row.querySelector('.bg-warning').textContent, 10) || 0;

                const dimension = row.dataset.dimension || "Inconnue";
                let decimalDimension = recalculateDecimalDimension(dimension);

                // Afficher les informations dans la console pour vérification
                console.log("Dimension :", dimension);
                console.log("Decimal Dimension :", decimalDimension);
                console.log("Nombre de cartons :", nbrctn);
                updateTotalNbrCarton(-nbrctn, decimalDimension,dimension);

                // Supprimer la ligne
                row.remove();
                updateTotals();

                // Recalculer les valeurs de `a` et `b` pour les lignes restantes
                recalculateAB();

                alert("La ligne a été supprimée et le total mis à jour.");
            }
        }

        function updateTotalNbrCarton(deltaNbrCtn, decimalDimension, dimension) {
            // Récupérer les éléments HTML nécessaires
            const totalCartonsElement = document.getElementById('totalNbrCarton'); // Total global des cartons
            const volumeElement = document.getElementById('volume'); // Volume global

            // Rechercher la ligne avec l'attribut data-dimensionrecap correspondant
            const specificRow = document.querySelector(`tr[data-dimensionrecap="${dimension}"]`);

            if (specificRow) {
                // Vérifier si l'attribut data-dimensionrecap correspond
                const specificTotalCartonsElement = specificRow.querySelector('td[data-totalcartons]');

                if (specificTotalCartonsElement) {
                    let currentTotalRef = parseInt(specificTotalCartonsElement.getAttribute('data-totalcartons'), 10) || 0;
                    console.log('totalcarton avant',currentTotalRef);

                    // Mettre à jour le total des cartons pour cette dimension
                    currentTotalRef += deltaNbrCtn;

                    // Empêcher les valeurs négatives
                    if (currentTotalRef < 0) currentTotalRef = 0;

                    // Mettre à jour l'attribut et le texte
                    specificTotalCartonsElement.setAttribute('data-totalcartons', currentTotalRef);
                    specificTotalCartonsElement.textContent = currentTotalRef;
                    console.log('ity',currentTotalRef);
                }
            } else {
                console.warn(`Aucune ligne avec data-dimensionrecap="${dimension}" trouvée.`);
            }

            // Mettre à jour le total global des cartons
            let currentTotalCartons = parseInt(totalCartonsElement.textContent, 10) || 0;
            currentTotalCartons += deltaNbrCtn;

            // Empêcher les valeurs négatives
            if (currentTotalCartons < 0) currentTotalCartons = 0;
            totalCartonsElement.textContent = currentTotalCartons;

            // Calculer le changement de volume
            const volumeChange = decimalDimension * deltaNbrCtn;

            // Recalculer le volume global
            let currentVolume = parseFloat(volumeElement.textContent) || 0;
            currentVolume += volumeChange;

            // Empêcher les volumes négatifs
            if (currentVolume < 0) currentVolume = 0;

            // Mettre à jour l'affichage du volume
            if (volumeElement) {
                volumeElement.textContent = currentVolume.toFixed(3) + " m³";
            }

            // Log des informations pour le débogage
            console.log(
                `Mise à jour : Dimension = ${dimension}, Cartons Delta = ${deltaNbrCtn}, Volume Changement = ${volumeChange.toFixed(
                    3
                )} m³, Volume Total = ${currentVolume.toFixed(3)} m³.`
            );
        }
        function recalculateAB() {
        // Sélectionner toutes les lignes du tableau dans le <tbody> avec la classe "sticky-body"
            const rows = document.querySelectorAll('tbody.sticky-body tr');

            // console.log(`Recalcul des valeurs pour ${rows.length} lignes restantes.`);

            let currentA = 1; // Initialiser `a`
            let currentB = 0; // Initialiser `b`

            rows.forEach((row, index) => {
                // Récupérer la cellule contenant le nombre de cartons
                let nbrctn = 0;
                let reste = 0;
                const cartonCountCell = row.querySelector('.carton-count');
                // Récupérer la cellule contenant le reste
                const warningCell = row.querySelector('.bg-warning');
                

                
                if (cartonCountCell) {
                    // Si `.carton-count` existe, prendre sa valeur
                nbrctn = parseInt(cartonCountCell.textContent.trim(), 10) || 0;
                } else if (warningCell) {
                    // Si `.bg-warning` existe, prendre sa valeur
                    reste = parseInt(warningCell.textContent.trim(), 10) || 0;
                } else {
                    // Si aucune cellule n'est trouvée, ignorer cette ligne
                    // console.log(`Ligne ${index + 1}: ni carton-count ni bg-warning trouvés.`);
                    return;
                }

                // Calculer `a` et `b`
                currentA = currentB + 1;

                if (nbrctn > 0) {
                    currentB += nbrctn; // Ajouter le nombre de cartons
                } else if (reste > 0) {
                    currentB ++; // Ajouter le reste
                }
                const abCell = row.querySelector('td:first-child');
                if (abCell) {
                    abCell.textContent = currentA !== currentB ? `${currentA} à ${currentB}` : `${currentB}`;
                    // console.log(`Ligne ${index + 1}: Nouvelle valeur pour a et b : ${abCell.textContent}`);
                } else {
                    console.warn(`Cellule pour afficher a et b introuvable dans la ligne ${index + 1}.`);
                }

                // (Optionnel) Debug : afficher les valeurs actuelles
                // console.log(`Ligne ${index + 1}: Nombre de cartons = ${nbrctn}, Reste = ${reste}`);
            });

            console.log("Recalcul des valeurs `a` et `b` terminé.");
        }

        function saveToDatabase(data) {
            // Envoie les données au serveur via Fetch API
            fetch('save_packinglisttemp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data) // Envoie les données en format JSON
            })
                .then((response) => response.json())
                .then((result) => {
                    if (result.success) {
                        console.log('Données enregistrées avec succès!');
                    } else {
                        console.log('Erreur lors de l\'enregistrement : ' + result.error);
                    }
                })
                .catch((error) => {
                    console.error('Erreur:', error);
                    console.log('Une erreur est survenue. Veuillez réessayer.');
                });
        }

        // Charger les données au chargement de la page si nécessaire
        document.addEventListener('DOMContentLoaded', () => {
            // Implémentez ici une logique pour précharger des données si nécessaire
        });

        function generateSizeColumns() {
            // Utiliser le tableau 'tailles' passé depuis PHP
            return tailles.map(taille => `
                <td class="tall" data-taille="${taille}">
                    <input type="number" value="0">
                </td>
            `).join('');
        }

    </script>
    <script>
        async function exportToPDF() {
        const { jsPDF } = window.jspdf;

        // Créer un nouveau document PDF
         const doc = new jsPDF({ orientation: "landscape" });

        // Ajouter le titre principal
        doc.setFontSize(16);
        doc.text("LISTE DE COLISAGE / Packing List", 105, 20, { align: "center" });

        // Section Expéditeur (Aligné à gauche)
        const leftX = 20;
        let currentY = 40; // Position verticale de départ
        doc.setFontSize(12);
        doc.text("EXPÉDITEUR :", leftX, currentY);
        currentY += 10;
        doc.text("ULTRAMAILLE S.A.", leftX, currentY);
        currentY += 10;
        doc.text("Tél : (261) 20 22 438 15 / (261) 20 22 438 16", leftX, currentY);
        currentY += 10;
        doc.text("Fax : (261) 20 22 438 14", leftX, currentY);
        currentY += 10;
        doc.text("BP 3298 Antananarivo Madagascar", leftX, currentY);

        // Section Destinataire (Aligné à droite)
        const rightX = 140; // Position horizontale à droite
        currentY = 40; // Revenir au même niveau vertical
        const client = "<?php echo $client; ?>";
        const destinataireAdresse = document.querySelector('select').value;

        doc.text("DESTINATAIRE :", rightX, currentY, { align: "left" });
        currentY += 10;
        doc.text(`Client : ${client}`, rightX, currentY, { align: "left" });
        currentY += 10;
        doc.text(`Adresse : ${destinataireAdresse}`, rightX, currentY, { align: "left" });

        // Ajouter la section Date et Expéditeur
        currentY += 30;
        
        doc.text("DATE: " + "<?php echo $dateprevuexp; ?>", leftX, currentY);
        currentY += 10;
        doc.text("N°: " + "<?php echo $exp; ?>", leftX, currentY);

        // Ajouter un espace avant le tableau
        currentY += 30;

        // Inclure le tableau principal
        const table = document.getElementById("packingListTable");
        doc.autoTable({
            html: table,
            startY: currentY,
            theme: 'grid',
            headStyles: { fillColor: [41, 128, 185] },
            styles: { fontSize: 8 }
        });

        const recapTable = document.getElementById("idtotauxrecap");
        currentY = doc.lastAutoTable.finalY + 20; // Positionner après le premier tableau
        doc.autoTable({
            html: recapTable,
            startY: currentY,
            theme: 'grid',
            headStyles: { fillColor: [41, 128, 185] },
            styles: { fontSize: 10 }
        });

        doc.addPage();
         // Inclure le tableau récapitulatif
        const recapitulatif = document.getElementById("recapitulatif");

        
        let finalY = 15; // Position de départ pour le texte

        doc.setFontSize(12); // Taille de police pour les sections

        // Encadrer et afficher les informations "DATE" et "N°"
        const boxWidth = 180; // Largeur commune pour les cadres
        const boxHeight = 25; // Hauteur du cadre pour DATE et N°
        const padding = 5; // Espacement interne pour le texte

        // Dessiner le rectangle pour la section DATE et N°
        doc.rect(15, finalY, boxWidth, boxHeight); // Rectangle
        doc.text("DATE :", 20, finalY + 7); // Ajouter le texte DATE
        doc.text("<?php echo $dateprevuexp; ?>", 60, finalY + 7); // Valeur de la date
        doc.text("N° :", 20, finalY + 17); // Ajouter le texte N°
        doc.text("<?php echo $exp; ?>", 60, finalY + 17); // Valeur de N°

        finalY += boxHeight + 10; // Mise à jour de finalY pour éviter le chevauchement

        // Encadrer et afficher les informations du DESTINATAIRE
        const recipientBoxHeight = 40; // Hauteur pour la section DESTINATAIRE

        doc.rect(15, finalY, boxWidth, recipientBoxHeight); // Rectangle
        doc.text("DESTINATAIRE :", 20, finalY + 7); // Texte DESTINATAIRE
        doc.text(`Client : ${client}`, 20, finalY + 14); // Ajouter le client
        doc.text(`Adresse : ${destinataireAdresse}`, 20, finalY + 21); // Ajouter l'adresse

        finalY += recipientBoxHeight + 10; // Mise à jour de finalY pour éviter le chevauchement

        // Ajouter l'intitulé "RECAPITULATIF DE LA COMMANDE"
        doc.setFontSize(14); // Taille de police pour le titre
        doc.text("RECAPITULATIF DE LA COMMANDE", 105, finalY, { align: "center" });
        finalY += 10; // Ajouter un espace avant le tableau

        // Générer le tableau récapitulatif
        doc.autoTable({
            html: recapitulatif,
            startY: finalY, // Commence après le texte et le titre
            theme: 'grid',
            headStyles: { fillColor: [41, 128, 185] },
            styles: { fontSize: 10 }
        });



        const clientName = "<?php echo $client; ?>".replace(/[^a-zA-Z0-9]/g, '_'); // Nettoyer pour éviter les caractères spéciaux
        const expName = "<?php echo $exp; ?>".replace(/[^a-zA-Z0-9]/g, '_');  
        const fileName = `PackingList_${clientName}_${expName}.pdf`;

         // Générer le PDF en tant que base64
        const pdfBase64 = doc.output("datauristring").split(",")[1]; // Obtenez uniquement la partie base64
        // Sauvegarder le PDF
        doc.save(fileName);

        await fetch("save_pdf.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                pdfData: pdfBase64,
                client: "<?php echo $client; ?>",
                exp: "<?php echo $exp; ?>"
            }),
        })
            .then(async response => {
                const rawText = await response.text(); // Obtenez la réponse brute
                console.log("Réponse brute :", rawText); // Affichez pour voir ce qui est retourné
                try {
                    const data = JSON.parse(rawText); // Essayez d'analyser le JSON
                    if (data.success) {
                        alert("PDF et données sauvegardés avec succès !");
                    } else {
                        alert("Erreur lors de la sauvegarde : " + data.message);
                    }
                } catch (error) {
                    console.error("JSON invalide :", rawText); // Affichez si la réponse n'est pas valide
                }
            })
            .catch(error => {
                console.error("Erreur réseau :", error);
            });

        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Attacher un événement click à tous les boutons avec la classe .valider-btn
            document.querySelectorAll('.valider-btn').forEach(button => {
                button.addEventListener('click', () => {
                    // Récupérer les valeurs des attributs data-*
                    const refExp = button.getAttribute('data-ref-exp');
                    const nomCli = button.getAttribute('data-nomcli');

                    if (refExp && nomCli) {
                        // Envoyer les données au serveur via AJAX
                        fetch('updatePacking.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ refExp, nomCli }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Packing list terminé !');
                            } else {
                                alert(`${data.message}`);
                            }
                        })
                        .catch(error => {
                            console.error('Erreur réseau:', error);
                        });
                    } else {
                        alert('Les données nécessaires sont manquantes.');
                    }
                });
            });
        });

    </script>
    <script>
        async function exportToExcel() {
        const client = "<?php echo $client; ?>";
        const destinataireAdresse = document.querySelector('select').value;
        const dateExp = "<?php echo $dateprevuexp; ?>";
        const exp = "<?php echo $exp; ?>";

        // Récupérer les tailles dynamiques
        const tailles = <?php echo json_encode($tailles); ?>;

        // Récupérer les totaux dynamiques
        const totalCartonsDynamic = document.getElementById('totalNbrCarton').textContent || 0;
        const totalpbc=document.getElementById('totalPB').textContent || 0;
        const totalpnc=document.getElementById('totalPN').textContent || 0;

        // Préparer les données générales
        const data = [
            ["LISTE DE COLISAGE / Packing List"],
            [],
            ["EXPÉDITEUR"],
            ["ULTRAMAILLE S.A."],
            ["Tél : (261) 20 22 438 15 / (261) 20 22 438 16"],
            ["Fax : (261) 20 22 438 14"],
            ["BP 3298 Antananarivo Madagascar"],
            [],
            ["DESTINATAIRE"],
            [`Client : ${client}`],
            [`Adresse : ${destinataireAdresse}`],
            [],
            [`DATE : ${dateExp}`],
            [`N° : ${exp}`],
            [],
            ["TABLEAU PRINCIPAL"]
        ];

        // Ajouter une ligne unique d'en-têtes avec fusion pour les tailles
        const headerRow = [
            "N° CTN", "N° Commande", "Reference", "Designation", "Couleur", "Nbre Ctn",
            ...tailles, // Colonnes dynamiques pour les tailles
            "TOTAL", "Poids Brut/CTN(kg)", "Poids Brut total", "Poids NET/CTN(kg)", "Poids NET total", "Carton"
        ];
        data.push(headerRow);

        // Ajouter les lignes de données depuis la table HTML
        const table = document.getElementById("packingListTable");
        const rows = table.querySelectorAll("tr");
        rows.forEach((row) => {
            const rowData = [];
            row.querySelectorAll("th, td").forEach((cell) => {
                rowData.push(cell.textContent.trim());
            });
            data.push(rowData);
        });

         // Ajouter les totaux depuis la table HTML idtotauxrecap
        const recapTable = document.getElementById("idtotauxrecap");
        const recapRows = recapTable.querySelectorAll("tr");
        data.push([]);
        recapRows.forEach((row) => {
            const rowData = [];
            row.querySelectorAll("td").forEach((cell) => {
                rowData.push(cell.textContent.trim());
            });
            data.push(rowData);
        });

        const recapitulatif = document.getElementById("recapitulatif");
        const recRows = recapitulatif.querySelectorAll("tr");
        data.push([]);
        recRows.forEach((row) => {
            const rowData = [];
            row.querySelectorAll("td").forEach((cell) => {
                rowData.push(cell.textContent.trim());
            });
            data.push(rowData);
        });

        // Créer le fichier Excel
        const ws = XLSX.utils.aoa_to_sheet(data);

        // Fusionner les cellules pour l'en-tête "TAILLE"
        if (tailles.length > 0) {
            ws['!merges'] = [
                {
                    s: { r: data.length - rows.length - 2, c: 5 }, // Début de la fusion : ligne des en-têtes
                    e: { r: data.length - rows.length - 2, c: 5 + tailles.length - 1 } // Fin de la fusion
                }
            ];
        }

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Packing List");

        // Télécharger le fichier Excel
        const clientName = client.replace(/[^a-zA-Z0-9]/g, '_'); // Nettoyer les noms pour éviter les caractères spéciaux
        const expName = exp.replace(/[^a-zA-Z0-9]/g, '_');
        const fileName = `PackingList_${clientName}_${expName}.xlsx`;

        XLSX.writeFile(wb, fileName);
        }


    </script>
    <script>
        // Variables pour suivre l'état des actions
        let isValidated = false;
        let isPDFExported = false;

        // Fonction appelée lors du clic sur le bouton "Valider"
        function handleValidation() {
            isValidated = true;
            checkConditions();
        }

        // Fonction appelée lors du clic sur le bouton "Exporter en PDF"
        function handlePDFExport() {
            isPDFExported = true;
            checkConditions();
        }

        // Vérifie si les deux conditions sont remplies pour afficher le bouton
        function checkConditions() {
            if (isValidated && isPDFExported) {
                document.getElementById('goToExpeditionBtn').classList.remove('d-none');
            }
        }
    </script>
    <script>
        function addRow(button) {
            try {
                // Trouver la ligne actuelle
                const currentRow = button.closest('tr');
                const table = currentRow.parentNode;

                // Récupérer les données nécessaires de la ligne actuelle avec des valeurs par défaut
                const poidsParCarton = currentRow.dataset.poidsparcarton || ''; // Poids par carton
                const poidsCtn = currentRow.dataset.poidsctn || ''; // Poids carton
                const dimension = currentRow.dataset.dimension || ''; // Dimensions
                const numeroCarton = currentRow.querySelector('td[data-numeroCarton]')?.innerText || ''; // Numéro carton
                const numcde = currentRow.cells[1]?.innerText || ''; // Numéro de commande
                const descRef = currentRow.cells[2]?.innerText || ''; // Référence
                const descType = currentRow.cells[3]?.innerText || ''; // Désignation
                const descCoul = currentRow.cells[4]?.innerText || ''; // Couleur
                const nbrctn = parseInt(currentRow.querySelector('td[data-nbrctn]')?.innerText) || 0; // Nombre cartons
                const total = currentRow.querySelector('td[data-total]')?.innerText || ''; // Total
                const pbc = currentRow.querySelector('td[data-pbc]')?.innerText || ''; // PBC
                const totalPbc = currentRow.querySelector('td[data-totalpbc]')?.innerText || ''; // Total PBC
                const pnc = currentRow.querySelector('td[data-pnc]')?.innerText || ''; // PNC
                const totalPnc = currentRow.querySelector('td[data-totalpnc]')?.innerText || ''; // Total PNC

                // Fonction pour générer les colonnes dynamiques pour les tailles
                function createSizeColumns(row) {
                    return [...row.querySelectorAll('td[data-taille]')].map(td => `
                        <td class='tall' data-taille="${td.dataset.taille}">${td.innerText || ''}</td>`).join('');
                }

                // Créer une nouvelle ligne
                const newRow = document.createElement('tr');
                newRow.dataset.id = Date.now(); // Utilisation d'un timestamp unique pour l'identifiant
                newRow.dataset.poidsparcarton = poidsParCarton;
                newRow.dataset.poidsctn = poidsCtn;
                newRow.dataset.dimension = dimension;
                newRow.style.cursor = "pointer";


                // Construire la nouvelle ligne
                newRow.innerHTML = `
                    <td data-numeroCarton>${numeroCarton}</td>
                    <td>${numcde}</td>
                    <td>${descRef}</td>
                    <td>${descType}</td>
                    <td>${descCoul}</td>
                    <td data-nbrctn class="bg-warning text-dark">${nbrctn}</td>
                    ${createSizeColumns(currentRow)}
                    <td data-total>${total}</td>
                    <td data-pbc>${pbc}</td>
                    <td data-totalpbc>${totalPbc}</td>
                    <td data-pnc>${pnc}</td>
                    <td data-totalpnc>${totalPnc}</td>
                    <td data-dimension class="dimension-cell" >${dimension}</td>
                    <td>
                        <i class="fa fa-edit" title="Modifier" style="color:green; cursor:pointer;" onclick="enableEditing(this)"></i>
                    </td>
                    <td>
                        <i class="fa fa-trash" title="Supprimer" style="color:red; cursor:pointer;" onclick="removeRow(this)"></i>
                    </td>
                    <td>
                        <i class="fa fa-link" title="Joindre" style="color:blue; cursor:pointer;" 
                            onclick="joinRows(this, '${numeroCarton}')">
                        </i>
                    </td>
                    <td>
                        <i class="fa fa-plus" title="Ajouter" style="color:#17a2b8; cursor:pointer;" onclick="addRow(this)"></i>
                    </td>
                `;

                // Insérer la nouvelle ligne après la ligne actuelle
                currentRow.insertAdjacentElement('afterend', newRow);
                let decimalDimension = recalculateDecimalDimension(dimension);
                updateTotalNbrCarton(nbrctn, decimalDimension,dimension);
                updateTotals();

            } catch (error) {
                console.error("Erreur lors de l'ajout d'une nouvelle ligne :", error);
            }
        }
    </script>
    <script>
     document.addEventListener("DOMContentLoaded", function () {
    let oldDimension = ""; // Stockage de l'ancienne dimension avant modification

    // Gestion du clic sur une cellule contenant la dimension
    document.querySelectorAll(".dimension-cell").forEach(cell => {
        cell.addEventListener("click", function () {
            let row = this.closest("tr"); // Récupère la ligne parente
            let cartonId = row.getAttribute("data-id"); // ID du carton
            oldDimension = this.textContent.trim(); // Stocke l'ancienne dimension affichée avant modification
            // Mise à jour des valeurs dans le modal
            document.getElementById("cartonId").textContent = cartonId;
            document.getElementById("cartonIdHidden").value = cartonId;
            document.getElementById("selectedDimension").textContent = oldDimension;
            document.getElementById("cartonSelect").value = oldDimension; // Sélectionner l'ancienne valeur

            $("#cartonModal").modal("show");
        });
    });

    // Mettre à jour la dimension sélectionnée dans l'affichage du modal
    document.getElementById("cartonSelect").addEventListener("change", function () {
        document.getElementById("selectedDimension").textContent = this.options[this.selectedIndex].text;
    });

    // Validation et mise à jour
    document.getElementById("validateButton").addEventListener("click", function () {
        let cartonId = document.getElementById("cartonIdHidden").value;
        let newDimension = document.getElementById("cartonSelect").value; // Nouvelle dimension sélectionnée
        let newDimensionText = document.getElementById("cartonSelect").options[document.getElementById("cartonSelect").selectedIndex].text;

        // Trouver la ligne correspondante et mettre à jour la dimension
        document.querySelectorAll(".dimension-cell").forEach(cell => {
            let row = cell.closest("tr");
            if (row.getAttribute("data-id") === cartonId) {
                row.setAttribute("data-dimension", newDimension);
                cell.textContent = newDimensionText; // Mise à jour de l'affichage
            }
        });

        // Mise à jour du tableau récapitulatif
        updateRecapTable(oldDimension, newDimension);

        // Fermer le modal
        $("#cartonModal").modal("hide");
    });

    // Fonction pour convertir une dimension en valeur décimale
    function convertToDecimal(dimension) {
        let parts = dimension.split('*');
        let decimalValue = 1;
        parts.forEach(part => {
            decimalValue *= (parseFloat(part) / 100);
        });
        return decimalValue;
    }

    // Mettre à jour le tableau récapitulatif
    function updateRecapTable(oldDimension, newDimension) {
    let oldDecimal = convertToDecimal(oldDimension);
    let newDecimal = convertToDecimal(newDimension);
    let volume = 0;

    let oldRow = document.querySelector(`tr[data-dimensionrecap="${oldDimension}"]`);
    let newRow = document.querySelector(`tr[data-dimensionrecap="${newDimension}"]`);
    let volumeRow = Array.from(document.querySelectorAll("#idtotauxrecap tbody tr"))
        .find(row => row.querySelector("td")?.textContent.trim() === "VOLUME");

    // Mise à jour de l'ancienne ligne (Réduction du total ou suppression)
    if (oldRow) {
        let oldTotalCartons = parseInt(oldRow.querySelector("[data-totalcartons]").textContent, 10);
        if (oldTotalCartons > 1) {
            oldRow.querySelector("[data-totalcartons]").textContent = oldTotalCartons - 1;
        } else {
            oldRow.remove(); // Supprime la ligne si elle atteint 0
        }
    }

    // Mise à jour de la nouvelle ligne (Ajout ou création)
    if (newRow) {
        let newTotalCartons = parseInt(newRow.querySelector("[data-totalcartons]").textContent, 10);
        newRow.querySelector("[data-totalcartons]").textContent = newTotalCartons + 1;
    } else {
        // Création d'une nouvelle ligne si la dimension n'existe pas encore
        let tableBody = document.getElementById("idtotauxrecap").querySelector("tbody");
        let newTableRow = document.createElement("tr");
        newTableRow.setAttribute("data-dimensionrecap", newDimension);
        newTableRow.setAttribute("data-decimaldimension", newDecimal);
        newTableRow.innerHTML = `
            <td>REFERENCE DES CARTONS: ${newDimension} (${newDecimal.toFixed(3)} m³)</td>
            <td data-totalcartons="1">1</td>
        `;

        // Insérer la nouvelle ligne juste avant la ligne du volume total
        if (volumeRow) {
            tableBody.insertBefore(newTableRow, volumeRow);
        } else {
            tableBody.appendChild(newTableRow);
        }
    }

    // Recalcul du volume total
    document.querySelectorAll("tr[data-dimensionrecap]").forEach(row => {
        let decimalDimension = parseFloat(row.getAttribute("data-decimaldimension"));
        let totalCartons = parseInt(row.querySelector("[data-totalcartons]").textContent, 10);
        volume += decimalDimension * totalCartons;
    });

    // Mise à jour de l'affichage du volume total
    document.getElementById("volume").textContent = volume.toFixed(3) + " m³";
}

});



    </script>

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
                <p class="mb-0">Copyright © 2024 Ultramaille</p>
                <ul class="list-inline mb-0">
                    <li class="list-inline-item"></li>
                    <li class="list-inline-item"></li>
                    <li class="list-inline-item"></li>
                </ul>
            </div>
        </div>
    </footer>
    <script src="../general/assets/js/aos.min.js"></script>
    <script src="../general/assets/js/bs-init.js"></script>
    <script src="../general/assets/js/bold-and-bright.js"></script>
    <script src="../general/assets/js/Dark-Mode-Switch-darkmode.js"></script>
    
</body>
<?php mysqli_close($conn) ;?>
</html>