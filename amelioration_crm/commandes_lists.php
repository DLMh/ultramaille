<?php 
    
    include("../../admin/databases/db_sql_server.php");
    
    if (isset($_GET['clientID'])) {
    $id = $_GET['clientID'];
    $client=$_GET['nomcli'];
    
    } else {
        
        echo "Paramètres manquants dans l'URL.";
    }
    $sql = "SELECT RefCRM, RefCde,CASE WHEN RefCRM IS NOT NULL AND RefCde IS NOT NULL THEN STRING_AGG(NumOF, '-') ELSE MAX(NumOF) END AS NumOF, CASE WHEN RefCRM IS NOT NULL AND RefCde IS NOT NULL THEN STRING_AGG(NomCollect, '/') ELSE MAX(NomCollect) END AS NomCollect FROM OF_Liste WHERE Clos=0 and ClientID=".$id."GROUP BY RefCRM, RefCde";
    
    $res=sqlsrv_query($con, $sql);
    

    if ($res === false) {
        die(print_r(sqlsrv_errors(), true));
    }
   header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Commandes</title>
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
        <a href="client_lists.php">
            <button class="btn btn-primary" type="button" style="border-radius: 50%;padding: 8.6px 32px;padding-right: 10px;padding-left: 10px;padding-bottom: 10px;padding-top: 10px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-arrow-left-circle-fill" style="font-size: 41px;">
                        <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"></path>
                </svg>
            </button>
        </a>
    </div>
    <div class="container mt-4">
        <h1 class="text-center">Listes des réferences commandes par client</h1>
        <!-- Table HTML -->
        <table id="data" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>OF</th>
                    <th>Collection</th>
                    <th>Réference Commandes</th>
                    <th>Action</th>
                </tr>
            </thead>
         
            <tbody>
                <?php  while ($row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php  echo $row['NumOF']; ?></td>
                        <td><?php echo mb_convert_encoding($row['NomCollect'], 'UTF-8', 'auto'); ?></td>
                        <td><?php   echo mb_convert_encoding($row['RefCde'], 'UTF-8') ; ?></td>
                      
                        <td>
                            <a href="traitement_OF.php?refcde=<?php echo $row['RefCde']?>&&refcrm=<?php  echo $row['RefCRM']; ?>&&clientID=<?php echo $id ?>&&OF=<?php echo $row['NumOF'] ?>&&collection=<?php echo $row['NomCollect'] ?>&&client=<?php echo $client ?>" >
                                <button class="btn btn-info">Suivi</button>
                            </a>
                        </td>
                    </tr>
                
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

  

    
    <!-- Script d'initialisation de DataTables -->
    <script>

        $(document).ready(function() {
        $('#data').DataTable({
            paging: true, // Activer la pagination
            searching: true, // Activer le filtrage
            ordering: true, // Activer le tri
            pageLength: 10, // Nombre de lignes par page
            deferRender: true,
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