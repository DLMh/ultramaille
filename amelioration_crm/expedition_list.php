<?php 
//SELECT p.*, c.idcom FROM packing p JOIN commande_mvt c ON p.idcomdet = c.idcomdet WHERE p.idcom = 176; requete ilaina iaffichena requete liste commande par exp

// SELECT idcom, ref_exp, MIN(date_depot_packing) AS date_depot_packing, MIN(date_prevu_exp) AS date_prevu_exp, MIN(date_depart_usine) AS date_depart_usine, transitaire, desc_coul, GROUP_CONCAT(desc_taille SEPARATOR '-') AS tailles, SUM(quantite) AS total_quantite, nomcli FROM packing GROUP BY idcom, ref_exp, transitaire, desc_coul, nomcli;


    include("../../admin/databases/db_to_mysql.php");
    $sql = " SELECT idcom,etat, ref_exp, MIN(date_depot_packing) AS date_depot_packing, MIN(date_prevu_exp) AS date_prevu_exp, MIN(date_depart_usine) AS date_depart_usine,MIN(transitaire) AS transitaire, nomcli FROM packing GROUP BY idcom, ref_exp,nomcli,etat";
    $result = mysqli_query($conn, $sql);

    $donnees = [];
    if (mysqli_num_rows($result) > 0) {
        // Parcourir les résultats et stocker dans le tableau
        while($row = mysqli_fetch_assoc($result)) {
            $donnees[] = [
                'ref_exp'    => $row["ref_exp"],
                'date_depot_packing'  => $row["date_depot_packing"],
                'date_prevu_exp' => $row["date_prevu_exp"],
                'date_depart_usine' => $row["date_depart_usine"],
                'transitaire'=> $row["transitaire"],              
                'nomcli'       => $row["nomcli"],
                'etat' => $row["etat"],
                'idcom'       => $row["idcom"]

            ];
        }
    } else {
        echo "no rows";
    }
   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Client </title>
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        <a href="suivi_packing.php">
            <button class="btn btn-primary" type="button" style="border-radius: 50%;padding: 8.6px 32px;padding-right: 10px;padding-left: 10px;padding-bottom: 10px;padding-top: 10px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-arrow-left-circle-fill" style="font-size: 41px;">
                        <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"></path>
                </svg>
            </button>
        </a>
    </div>
    <div class="container mt-5">
        <h1 class="text-center">Listes des éxpeditions par client</h1>
        <div style="text-align: left; margin-bottom: 10px;">
            <select id="yearFilter" class="form-select" style="width: 250px; display: inline-block;">
                <option value="">Toutes les années</option>
                <?php 
                    $currentYear = 2022;//date("Y")
                    $endYear = 2050;
                    for ($year = $currentYear; $year <= $endYear; $year++) { ?>
                        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                <?php } ?>
            </select>
        </div>
        
        <!-- Table HTML -->
        <table  id="data" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>REF EXP</th>
                    <th>Date dépôt (Packing)</th>
                    <th>Date prévue</th>
                    <th>Date départ usine</th>
                    <th>Transitaire</th>    
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>     
            <tbody>
                  <?php if (!empty($donnees)) { ?>
                    <?php foreach ($donnees as $row) { ?>
                        <tr onclick="window.location.href='#';" style="cursor:pointer;">
                            <td><?php  echo $row['nomcli']; ?></td>
                            <?php  if($row['etat']==1) { ?>
                            <td><a href="packingpdf.php?file=PackingList_<?php echo $row['nomcli']; ?>_<?php  echo $row['ref_exp']; ?>.pdf" style="color: green;" ><?php  echo $row['ref_exp']; ?></a></td>
                            <?php }else{?>
                                <td><a href="commandeEXP_lists?idcom=<?php echo $row['idcom']; ?>&&exp=<?php  echo $row['ref_exp']; ?>" style="color: blue;"><?php  echo $row['ref_exp']; ?></a></td>
                            <?php }?>
                            <td>
                                <input 
                                    class="form-control" type="date" 
                                    value="<?php echo $row['date_depot_packing']; ?>" 
                                    style="width: 100%; box-sizing: border-box; padding: 8px; margin: 0; border: none;"
                                    onchange="updateField(<?php echo $row['idcom']; ?>,'<?php  echo $row['ref_exp']; ?>', 'date_depot_packing', this.value)">
                            </td>

                            <!-- Date Prevu Exp Field -->
                            <td>
                                <input 
                                    class="form-control" type="date" 
                                    value="<?php echo $row['date_prevu_exp']; ?>" 
                                    style="width: 100%; box-sizing: border-box; padding: 8px; margin: 0; border: none;"
                                    onchange="updateField(<?php echo $row['idcom']; ?>,'<?php  echo $row['ref_exp']; ?>', 'date_prevu_exp', this.value)">
                            </td>

                            <!-- Date Depart Usine Field -->
                            <td>
                                <input 
                                    class="form-control" type="date" 
                                    value="<?php echo $row['date_depart_usine']; ?>" 
                                    style="width: 100%; box-sizing: border-box; padding: 8px; margin: 0; border: none;"
                                    onchange="updateField(<?php echo $row['idcom']; ?>,'<?php  echo $row['ref_exp']; ?>', 'date_depart_usine', this.value)">
                            </td>

                            <!-- Transitaire Field -->
                            <td>
                                <input 
                                    class="form-control" type="text" 
                                    value="<?php echo $row['transitaire']; ?>" 
                                    style="width: 100%; box-sizing: border-box; padding: 8px; margin: 0; border: none;"
                                    onchange="updateField(<?php echo $row['idcom']; ?>,'<?php  echo $row['ref_exp']; ?>', 'transitaire', this.value)">
                            </td>
                            <?php  if($row['etat']==1) { ?>
                            <td>Terminé</td>
                            <td><a href="packing_list?nomcli=<?php echo $row['nomcli']; ?>&ref_exp=<?php echo $row['ref_exp']; ?>"><i class="fa fa-edit text-warning"></i></a></td>
                            <?php }else{?>
                            <td>Non terminé</td>
                            <td><a></a></td>
                            <?php }?>
                        </tr>
                    <?php }?>
                <?php } ?>
            </tbody>
        
        </table>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

  
    <script>
        
        function updateField(idcom,ref_exp, field, value) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_field.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log(xhr.responseText); // Optional: Handle response
                }
            };
            xhr.send(`idcom=${idcom}&ref_exp=${ref_exp}&field=${field}&value=${value}`);
        }
    </script>
    
   <script>
    $(document).ready(function () {
        // Initialisation de DataTables
        var table = $('#data').DataTable({
            paging: true, // Activer la pagination
            searching: true, // Activer la recherche
            ordering: true, // Activer le tri
            pageLength: 10, // Nombre de lignes par page
            language: {
                "sProcessing": "Traitement en cours...",
                "sLengthMenu": "Afficher _MENU_ éléments",
                "sZeroRecords": "Aucun résultat trouvé",
                "sInfo": "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
                "sInfoEmpty": "Affichage de 0 à 0 sur 0 élément",
                "sInfoFiltered": "(filtré à partir de _MAX_ éléments au total)",
                "sSearch": "Rechercher:",
                "oPaginate": {
                    "sFirst": "Premier",
                    "sLast": "Dernier",
                    "sNext": "Suivant",
                    "sPrevious": "Précédent"
                }
            }
        });

        // Gestion du filtre par année
        $('#yearFilter').on('change', function () {
            var selectedYear = $(this).val(); // Année sélectionnée
            if (selectedYear) {
                table.rows().every(function () {
                    var dateInput = $(this.node()).find('input[type="date"]').val(); // Récupérer la valeur de l'input date
                    var rowYear = dateInput ? new Date(dateInput).getFullYear().toString() : ''; // Extraire l'année
                    if (rowYear === selectedYear) {
                        $(this.node()).show(); // Afficher la ligne
                    } else {
                        $(this.node()).hide(); // Masquer la ligne
                    }
                });
            } else {
                table.rows().every(function () {
                    $(this.node()).show(); // Afficher toutes les lignes si aucune année n'est sélectionnée
                });
            }
        });
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
</html>