<?php 
//SELECT p.*, c.numcde,c.desc_type,c.desc_ref FROM packing p JOIN commande_mvt c ON p.idcomdet = c.idcomdet WHERE p.idcom = 176; requete ilaina iaffichena requete liste commande par exp

// SELECT idcom, ref_exp, MIN(date_depot_packing) AS date_depot_packing, MIN(date_prevu_exp) AS date_prevu_exp, MIN(date_depart_usine) AS date_depart_usine, transitaire, desc_coul, GROUP_CONCAT(desc_taille SEPARATOR '-') AS tailles, SUM(quantite) AS total_quantite, nomcli FROM packing GROUP BY idcom, ref_exp, transitaire, desc_coul, nomcli;

    if(isset($_GET['idcom'])){
        $idcom=$_GET['idcom'];
        $exp=$_GET['exp'];
    }

    include("../../admin/databases/db_to_mysql.php");
    $sql = "SELECT p.*, c.numcde,c.desc_type,c.desc_ref FROM packing p JOIN commande_mvt c ON p.idcomdet = c.idcomdet WHERE p.idcom =".$idcom." and ref_exp='".$exp."'";
    $result = mysqli_query($conn, $sql);

    $donnees = [];
    $ids = [];
    if (mysqli_num_rows($result) > 0) {

        while ($row = mysqli_fetch_assoc($result)) {
            $desc_ref = $row['desc_ref'];
            $desc_coul = $row['desc_coul'];
            $desc_taille = $row['desc_taille'];
            $quantite = $row['quantite'];
            $ids[] = $row['id'];

            // Vérifie si la combinaison de `desc_ref` et `desc_coul` existe déjà.
            if (!isset($donnees[$desc_ref][$desc_coul])) {
                // Initialise les détails si c'est la première fois que cette combinaison est rencontrée.
                $donnees[$desc_ref][$desc_coul] = [
                    'id' => $row['id'],
                    'ref_exp' => $row['ref_exp'],
                    'date_depot_packing' => $row['date_depot_packing'],
                    'date_prevu_exp' => $row['date_prevu_exp'],
                    'date_depart_usine' => $row['date_depart_usine'],
                    'transitaire' => $row['transitaire'],
                    'nomcli' => $row['nomcli'],
                    'idcom' => $row['idcom'],
                    'numcde' => $row['numcde'],
                    'desc_type' => $row['desc_type'],
                    'sizes' => [] // Initialise un tableau pour les tailles.
                ];
            }

            // Ajoute la taille et la quantité au tableau `sizes` pour cette combinaison de `desc_ref` et `desc_coul`.
            $donnees[$desc_ref][$desc_coul]['sizes'][] = [
                'desc_taille' => $desc_taille,
                'quantite' => $quantite
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
    <title>Ultramaille </title>
    <link rel="stylesheet" href="../general/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&amp;display=swap">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../general/assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="../general/assets/fonts/material-icons.min.css">
    <link rel="stylesheet" href="../general/assets/css/aos.min.css">
    <link rel="stylesheet" href="../general/css/style_commandeEXP.css">
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
        <a href="expedition_list.php">
            <button class="btn btn-primary" type="button" style="border-radius: 50%;padding: 8.6px 32px;padding-right: 10px;padding-left: 10px;padding-bottom: 10px;padding-top: 10px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-arrow-left-circle-fill" style="font-size: 41px;">
                        <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"></path>
                </svg>
            </button>
        </a>
    </div>
    <div class="container mt-5">
        <h1 class="text-center">Paramètrage du packing list </h1>
       <?php
            // Récupère la première entrée pour afficher le nom du client et la référence d'expédition.
            $first_entry = reset($donnees); // Obtient la première entrée dans $donnees
            $first_detail = reset($first_entry); // Accède aux détails de la première couleur

        ?>
        <h4 class="text-center">
            <?php echo $first_detail['nomcli']; ?> -- <?php echo $first_detail['ref_exp']; ?>
        </h4>
        <div class="row">
            <?php if (!empty($donnees)) { ?>
                <table class="table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Couleur</th>
                            <th>Désignation</th>
                            <?php
                            // Collecte toutes les tailles uniques pour créer les colonnes dans l'en-tête.
                            $all_sizes = [];
                            foreach ($donnees as $desc_ref => $couleurs) {
                                foreach ($couleurs as $desc_coul => $details) {
                                    foreach ($details['sizes'] as $size) {
                                        $all_sizes[$size['desc_taille']] = true;
                                    }
                                }
                            }
                            // Affiche chaque taille unique comme en-tête de colonne.
                            foreach (array_keys($all_sizes) as $taille) {
                                echo "<th>$taille</th>";
                            }
                            ?>
                            <th>Total</th>
                        </tr>
                    </thead>
                        <tbody>
                            <?php 
                            $previous_ref = null; // Initialise une variable pour stocker la référence précédente

                            foreach ($donnees as $desc_ref => $couleurs) { ?>
                                <?php foreach ($couleurs as $desc_coul => $details) { ?>
                                    <?php $total = 0; ?>
                                    <tr onclick="window.location.href='#';" style="cursor:pointer;">
                                        <td><?php echo $desc_ref; ?></td>
                                        <td><?php echo $desc_coul; ?></td>
                                        <td><?php echo $details['desc_type']; ?></td>
                                        <?php
                                        // Initialise un tableau temporaire pour les quantités par taille.
                                        $quantities_by_size = array_fill_keys(array_keys($all_sizes), 0);
                                        foreach ($details['sizes'] as $size) {
                                            $quantities_by_size[$size['desc_taille']] = $size['quantite'];
                                        }
                                        // Affiche la quantité pour chaque taille et calcule le total.
                                        foreach ($quantities_by_size as $quantite) {
                                            echo "<td>$quantite</td>";
                                            $total += $quantite;
                                        }
                                        ?>
                                        <td><?php echo $total; ?></td>
                                    </tr>
                                <?php } ?>

                                <?php
                                // Si la référence actuelle est différente de la précédente, affiche le formulaire
                                if ($desc_ref !== $previous_ref) {
                                    $previous_ref = $desc_ref; // Met à jour la référence précédente
                                ?>
                                <tr>
                                    <td colspan="<?php echo count($all_sizes) + 4; ?>" style="text-align: center;">
                                        <form  id="form-detail-colis-<?php echo htmlspecialchars($desc_ref); ?>" method="post" onsubmit="submitForm(event, '<?php echo htmlspecialchars($desc_ref); ?>')" class="p-3 border rounded">
                                            <input type="hidden" name="reference" value="<?php echo htmlspecialchars($desc_ref); ?>">
                                            <input type="hidden" name="nomcli" value="<?php echo $first_detail['nomcli']; ?>">
                                            <input type="hidden" name="ref_exp" value="<?php echo $first_detail['ref_exp']; ?>">

                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <?php foreach (array_keys($all_sizes) as $taille) { ?>
                                                            <th><?php echo $taille; ?></th>
                                                        <?php } ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Ligne pour les champs de poids -->
                                                    <tr>
                                                        <?php foreach ($quantities_by_size as $taille => $quantite) { ?>
                                                            <?php if ($quantite > 0) { ?>
                                                                <td>
                                                                    <label for="poids_<?php echo $taille; ?>">Poids du colis</label>
                                                                    
                                                                    <input type="number" step="0.001" min="0" id="poids_<?php echo $taille; ?>" name="poids[<?php echo $taille; ?>]" class="form-control" placeholder="Poids en kg" required>
                                                                </td>
                                                            <?php } else { ?>
                                                                <!-- Cellule vide pour garder l'alignement -->
                                                                <td></td>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </tr>

                                                    <!-- Ligne pour les champs de quantité -->
                                                    <tr>
                                                        <?php foreach ($quantities_by_size as $taille => $quantite) { ?>
                                                            <?php if ($quantite > 0) { ?>
                                                                <td>
                                                                    <label for="quantite_<?php echo $taille; ?>">Capacité du colis:</label>
                                                                    <input type="number" id="quantite_<?php echo $taille; ?>" name="quantite_colis[<?php echo $taille; ?>]" class="form-control" placeholder="Quantité" required>
                                                                </td>
                                                            <?php } else { ?>
                                                                <!-- Cellule vide pour garder l'alignement -->
                                                                <td></td>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5">
                                                               <select id="cartons_param_<?php echo htmlspecialchars($desc_ref); ?>" name="cartons_param" class="form-control">
                                                                    <option value="">Sélectionner un carton</option>
                                                                </select>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-end mt-2">
                                                <button type="submit" class="btn btn-primary" >Enregistrer</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                </table>
            <?php } ?>
        </div>
        <div class="row">
            <div class="select-button-group">
                <button id="openModalBtn" type="button">
                    Ajouter un nouveau dimension de carton
                </button>
            </div>          
        </div>
       
    </div>
    <div class="container mt-5 d-flex">
         <button type="button" class="btn btn-dark" id="validerButton" style="margin-left: auto; margin-bottom:20px;">
            Valider
        </button>

    </div>
    <div class="modal fade" id="dataEntryModal" tabindex="-1" aria-labelledby="dataEntryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dataEntryModalLabel">Ajouter des Paramètres</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="dataEntryForm" action="save_carton_param.php" method="POST">
                        <div class="mb-3">
                            <label for="dimensions" class="form-label">Dimensions</label>
                            <input type="text" class="form-control" id="dimensions" name="dimensions" placeholder="Entrez les dimensions (ex: 20*40*34)" required>
                        </div>
                        <div class="mb-3">
                            <label for="poids" class="form-label">Poids</label>
                            <input type="number" step="0.001" class="form-control" id="poids" name="poids" placeholder="Entrez le poids en kg" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Ajouter</button>
                    </form>
                </div>
            </div>
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
    <?php echo "<script>let idArray = " . json_encode($ids) . ";</script>";?>
    <script>

        $(document).ready(function() {
            $('#validerButton').on('click', function(event) {
                event.preventDefault(); // Empêche l'envoi du formulaire sans validation

                let formIsValid = true;
                let nomcli = "<?php echo $first_detail['nomcli']; ?>";
                let ref_exp = "<?php echo $first_detail['ref_exp']; ?>";

                // Vérification des champs de poids et de quantité
                $('input[name^="poids["], input[name^="quantite_colis["]').each(function() {
                    if ($(this).val().trim() === "") {
                        formIsValid = false; // Si un champ est vide, le formulaire est invalide
                        $(this).addClass('is-invalid'); // Ajoute une classe pour indiquer l'erreur
                    } else {
                        $(this).removeClass('is-invalid'); // Enlève la classe d'erreur si le champ est rempli
                    }
                });

                // Vérification du champ carton sélectionné
                const cartonParam = $('#cartons_param_<?php echo htmlspecialchars($desc_ref); ?>').val().trim();
                if (cartonParam === "") {
                    formIsValid = false;
                    $('#cartons_param_<?php echo htmlspecialchars($desc_ref); ?>').addClass('is-invalid');
                } else {
                    $('#cartons_param_<?php echo htmlspecialchars($desc_ref); ?>').removeClass('is-invalid');
                }

                // Affiche un message d'alerte si un champ est vide
                if (!formIsValid) {
                    alert("Veuillez remplir tous les champs du formulaire.");
                    return;
                }

                // Envoi AJAX si le formulaire est valide
                $.ajax({
                    url: 'insert_packing_list.php',
                    type: 'POST',
                    data: { ids: idArray, nomcli: nomcli, ref_exp: ref_exp, carton_param: cartonParam },
                    success: function(response) {
                        console.log(response);
                        window.location.href = `packing_list.php?nomcli=${encodeURIComponent(nomcli)}&ref_exp=${encodeURIComponent(ref_exp)}`;
                    },
                    error: function(xhr, status, error) {
                        console.error("Erreur : " + error);
                    }
                });
            });
        });
    </script>

    <script>
        function submitForm(event, formRef) {
            event.preventDefault(); // Empêche la soumission du formulaire normale
            // Sélectionne le formulaire en fonction de l'ID dynamique
            var formData = $('#form-detail-colis-' + formRef).serialize();
            $.ajax({
                url: 'add_detail_colis.php',
                type: 'POST',
                data: formData,
                dataType: 'json', // Attente d'une réponse JSON
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message); // Message de succès
                        // Ajouter ici une action supplémentaire, comme rafraîchir une partie de la page
                    } else {
                        alert(response.message); // Message d'erreur reçu
                    }
                },
                error: function(xhr, status, error) {
                console.log("Erreur AJAX : ", error);
                console.log("Statut : ", status);
                console.log("Réponse : ", xhr.responseText); // Affiche les erreurs de réponse
                alert('Une erreur est survenue lors de l\'enregistrement des données.');
            }
            });
        }
    </script>
     <script>
            // Script pour ouvrir la modale
            document.getElementById('openModalBtn').addEventListener('click', function () {
                var myModal = new bootstrap.Modal(document.getElementById('dataEntryModal'));
                myModal.show();
            });

            // Script pour gérer le formulaire de saisie de données
            document.getElementById('dataEntryForm').addEventListener('submit', function (event) {
                event.preventDefault();

                // Récupérer les valeurs saisies
                const dimensions = document.getElementById('dimensions').value;
                const poids = document.getElementById('poids').value;

                // Envoyer les données au serveur via une requête AJAX
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "save_carton_param.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var myModal = bootstrap.Modal.getInstance(document.getElementById('dataEntryModal'));
                        myModal.hide();
                        document.getElementById('dataEntryForm').reset();
                        // Actualiser la liste des cartons pour chaque formulaire
                        refreshAllCartonsParams();
                    }
                };
                xhr.send(`dimensions=${dimensions}&poids=${poids}`);
            });

            // Recharger les données des paramètres de carton
            function loadCartonsParams(refId) {
                const xhr = new XMLHttpRequest();
                xhr.open("GET", "get_cartons_params.php", true);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        const cartons = JSON.parse(xhr.responseText);
                        const cartonsParamSelect = document.getElementById(`cartons_param_${refId}`);
                        cartonsParamSelect.innerHTML = '<option value="">Sélectionner un carton</option>';

                        cartons.forEach(carton => {
                            const option = document.createElement("option");
                            option.value = carton.id;
                            option.text = `${carton.dimension} - ${carton.poids} kg`;
                            cartonsParamSelect.add(option);
                        });
                    }
                };
                xhr.send();
            }

            // Fonction pour actualiser la liste des cartons pour chaque formulaire de la page
            function refreshAllCartonsParams() {
                <?php foreach ($donnees as $desc_ref => $couleurs) { ?>
                    loadCartonsParams('<?php echo $desc_ref; ?>');
                <?php } ?>
            }

            // Appeler refreshAllCartonsParams() lors du chargement de la page
            window.onload = function () {
                refreshAllCartonsParams();
};



    </script>
    <!-- Script d'initialisation de DataTables
    <script>

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
</html>