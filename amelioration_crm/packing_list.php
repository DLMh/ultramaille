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
            $dateprevuexp=$row['date_prevu_exp'];
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
                    'date_prevu_exp'=> $row['date_prevu_exp'],
                    'idcomdet' => $row['idcomdet']
                ];
        }
    } else {
        echo "0 information pour ce numéro d'expedition";
    }

    $sqlColis = "SELECT *
        FROM detail_colis
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
                    'ref_exp' => $row['ref_exp']
                ];
        }
    } else {
        echo "0 information pour le detail colis";
    }
    $sqldestinataire="SELECT * FROM `commande` as co  LEFT JOIN `client` as cl ON co.`idcli` = cl.`idclient`  left JOIN `client_livraison` as cliv ON co.`idcli` = cliv.`idclient` LEFT JOIN `prev_ccial` as pv ON pv.`idprev`= co.`idprev` WHERE co.`idcom`=".$idcom;
    $resultdestinataire = mysqli_query($conn, $sqldestinataire);
   
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
    <div class="container mt-5">
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
                <hr>
                
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
        <div class="row mt-5">
            <div class="col-3 d-flex" style="padding: 20px;border: solid 1px;  flex-direction:column;align-items:center;">
                <label ><span style="font-weight: bold;">Date:</span> <?php echo $dateprevuexp ;?></label>
                <label> <?php echo $exp ?></label>
            </div>
        </div>
        <div class="row mt-3">
        <?php if (!empty($donnees)) {
            
                $quantiteMap = [];
                foreach ($donneesColis as $colisRow) {
                    $quantiteMap[$colisRow['refcde']][$colisRow['desc_taille']] = $colisRow['quantite'];
                }
                // Récupérer toutes les tailles uniques pour les en-têtes
                // Définir l'ordre personnalisé des tailles
                $tailleOrder = ['S', 'M', 'L', 'XL', '2XL'];

                // Extraire les tailles uniques à partir des données
                $tailles = array_unique(array_column($donnees, 'desc_taille'));

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
            
            <table  class="table table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th rowspan="2">N° CTN</th>
                        <th rowspan="2">N° Commande</th>
                        <th rowspan="2">Reference</th>
                        <th rowspan="2">Designation</th>
                        <th rowspan="2">Couleur</th>
                        <th rowspan="2">NBR CTNS (qte/1 colis)</th>
                        <th colspan="<?php echo count($tailles); ?>">TAILLE</th> <!-- Utilisation de colspan pour englober toutes les tailles -->
                        <th rowspan="2">TOTAL</th>
                        <th rowspan="2">Poids Brut/CTN</th>
                        <th rowspan="2">Poids Brut total</th>
                        <th rowspan="2">Poids NET/CTN</th>
                        <th rowspan="2">Poids NET total</th>
                        <th rowspan="2">Carton</th>
                    </tr>

                    <tr>
                        <!-- Afficher chaque taille dans un en-tête <th> -->
                        <?php foreach ($tailles as $taille) : ?>
                            <th><?php echo $taille; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $a = 1;
                        $b = 0;
                    foreach ($donnees as $row) {
                        $nbr_carton = 0;
                        if (isset($quantiteMap[$row['desc_ref']][$row['desc_taille']])) {
                            $nbr_carton = round($row['quantite'] / $quantiteMap[$row['desc_ref']][$row['desc_taille']]);
                        }

                        // Calculer A et B pour cette ligne
                        $a = $b + 1;
                        $b = $b + $nbr_carton; 
                        // Calculer la quantité totale pour chaque référence
                        ?>
                        
                        <tr onclick="window.location.href='#';" style="cursor:pointer;">
                            <?php if ($a !== $b ){?>
                            <td><?php echo "$a à $b"; ?></td>
                            <?php }else { ?>
                            <td><?php echo "$b"; ?></td>
                            <?php }?>
                            <td><?php echo $row['numcde'] ;?></td>
                            <td><?php echo $row['desc_ref'] ?></td>
                            <td><?php echo $row['desc_type'] ?></td>
                            <td><?php echo $row['desc_coul']?></td>
                            <td>
                                <?php
                                
                                 if (isset($quantiteMap[$row['desc_ref']][$row['desc_taille']])) {
                                        echo $nbr_carton ." (".$quantiteMap[$row['desc_ref']][$row['desc_taille']].")";
                                    } else {
                                        echo 'N/A';
                                    }
                                ?>
                            </td>
                           
                            <!-- Afficher les quantités pour chaque taille -->
                            <?php foreach ($tailles as $taille) : ?>
                                <td>
                                    <?php 
                                        echo $row['desc_taille'] === $taille ? $row['quantite'] : '';
                                    ?>
                                </td>
                            <?php endforeach; ?>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php }?>
                </tbody>
            </table>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td>TOTAL NOMBRE DES PIECES</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            TOTAL NOMBRE DES CARTONS
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            TOTAL POIDS BRUT/KG
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            TOTAL POIDS NET/KG
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            VOLUME /M3
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            REFERENCE DES CARTONS 
                        </td>
                        <td></td>
                    </tr>

                </tbody>
            </table>
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

    <!-- Script d'initialisation de DataTables -->
    <!-- <script>

        $(document).ready(function() {
        $('#data').DataTable({
            paging: true, // Activer la pagination
            searching: true, // Activer le filtrage
            ordering: true, // Activer le tri
            pageLength: 10, // Nombre de lignes par page
            language: {
                "sProcessing": "Traitement en cours...",
                "sLengthMenu": "Afficher _MENU_ éléments",
                "sZeroRecords": "Aucun résultat trouvé",
                "sInfo": "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
                "sInfoEmpty": "Affichage de 0 à 0 sur 0 élément",
                "sInfoFiltered": "(filtré à partir de _MAX_ éléments au total)",
                "sInfoPostFix": "",
                "sSearch": "Rechercher:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Chargement en cours...",
                "oPaginate": {
                    "sFirst": "Premier",
                    "sLast": "Dernier",
                    "sNext": "Suivant",
                    "sPrevious": "Précédent"
                },
                "oAria": {
                    "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                    "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
                }
            }
        });
    });

    </script> -->
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