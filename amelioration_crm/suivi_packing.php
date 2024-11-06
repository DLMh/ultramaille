<?php
session_start();
if(!isset($_SESSION["vmatr"])){
    header ("location:../../index.php");
}
$_SESSION["last_act"]=time();
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Menu aministrateur</title>
    <link rel="stylesheet" href="../general/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&amp;display=swap">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../general/assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="../general/assets/fonts/material-icons.min.css">
    <link rel="stylesheet" href="../general/assets/css/aos.min.css">
    <link rel="stylesheet" href="../general/assets/css/Dark-Mode-Switch.css">
    <link rel="icon" href="../general/image/UTM_logo_sans_fond.png">
</head>
<style>
     i.fas.fa-address-book {
    font-size: 37px;
}
</style>
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
                    </svg></a></div>
        </div>
    </nav>
    <div class="toast" id="loadingToast" role="alert" aria-live="assertive" aria-atomic="true">
  <div class="toast-header">
    <strong class="mr-auto">Chargement en cours...</strong>
  </div>
  <div class="toast-body">
    <div class="spinner-border text-primary" role="status">
      <span class="sr-only">Chargement...</span>
    </div>
    Votre contenu est en cours de chargement.
  </div>
</div>
    <div style="background: url(&quot;../general/assets/img/fond-marche.jpg&quot;);height: 400px;">
        <h1 class="text-light justify-content-center align-items-center align-content-around" data-aos="fade-right" data-aos-once="true" style="text-align: center;font-size: 30px;margin-top: 0px;padding: 0px;padding-top: 165px;">Bienvenue <?php echo $_SESSION["civ"]." ".$_SESSION["vnom"]." ".$_SESSION["vpren"];?></h1>
    </div>
    <h2 style="text-align: center;margin-top: 36px;margin-bottom: 35px;border-style: none;">Menu Administrateur</h2>
    <div class="container">
        <div class="row">
        <?php
            include("../../admin/db.php");
            //comptage des messages non lus dans la table dde-flux
            $lu=$conn->prepare("SELECT * FROM `dde-flux` WHERE `lu_nonlu`=:nbl AND `mail`=:tmail AND `etat`=:teat");
            $lu->execute(array(
                "tmail"=>$_SESSION["ymail"],
                "teat"=>0,
                "nbl"=>1
            ));
            $rlu=$lu->rowCount();
                //comptage des messages non lus dans la table dde-detail-flux
            $lubis=$conn->prepare("SELECT * FROM `dde-detail-flux` WHERE `lu_nonlu`=:nblb AND `iduser`=:tmailb AND `etat`=:ttat");
            $lubis->execute(array(
                "tmailb"=>$_SESSION["ymail"],
                "ttat"=>0,
                "nblb"=>1
            ));
            $rlub=$lubis->rowCount();
            $nvmsg=$rlu+$rlub
        ?>
            <div class="col-md-3" style="text-align: center;">
                <a href="menu_suivi.php">
                   <button class="btn" data-aos="fade-up" data-aos-once="true" type="button" style="width: 205px; height: 129.7812px; text-align: center; box-shadow: 0px 0px, 4px 5px 20px rgba(0, 0, 0, 0.2); margin-bottom: 15px; position: relative; background-color: #29E6BD; color: #FFFFFF;">
                        <i class="fas fa-tasks" style="font-size: 44px;box-shadow: 0px 0px;"></i>
                    </button>
                </a><br><span class="fw-bold" style="margin-top: 0px;padding-top: 0px;">Suivi Packing</span>
            </div>
            <div class="col-md-3" style="text-align: center;">
                <a href="expedition_list.php">
                    <button class="btn" data-aos="fade-up" data-aos-once="true" type="button" style="width: 205px; height: 129.7812px; text-align: center; box-shadow: 0px 0px, 4px 5px 20px rgba(0, 0, 0, 0.2); margin-bottom: 15px; position: relative; background-color: #4682B4; color: white;">
                        <i class="fas fa-box-open" style="font-size: 44px;box-shadow: 0px 0px;"></i>
                    </button>
                </a>
                <br><span class="fw-bold">Packing List</span>

            </div>
            <div class="col-md-3" style="text-align: center;box-shadow: 0px 0px;">
            <a href="packing.php">
                <button class="btn btn-outline-dark" data-aos="fade-up" data-aos-delay="800" data-aos-once="true" type="button" style="width: 205px;height: 129.7812px;text-align: center;box-shadow: 4px 5px 20px rgb(0,0,0);margin-bottom: 15px;"><i class="fas fa-arrow-left" style="font-size: 44px;box-shadow: 0px 0px;"></i>

            </button></a><br><span class="fw-bold">Retour</span>
            </div>
        </div>
    </div>
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
    <script src="../general/js/jquery.min.js"></script>
    <script>
        // Afficher le toast de chargement
            function afficherLoading() {
            $('#loadingToast').toast('show');
            }

            // Masquer le toast de chargement
            function cacherLoading() {
            $('#loadingToast').toast('hide');
            }

            // Fonction de chargement fictive (à remplacer par votre chargement réel)
            function simulateLoading() {
            // Simule un délai de chargement de 3 secondes
            setTimeout(function() {
                // Une fois le chargement terminé, afficher le toast de chargement
                cacherLoading();
            }, 3000);
            }

            // Appel de la fonction de chargement fictive
            simulateLoading();

            // Appel de la fonction pour afficher le toast de chargement
            afficherLoading();

    </script>
    <script src="../general/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../general/assets/js/aos.min.js"></script>
    <script src="../general/assets/js/bs-init.js"></script>
    <script src="../general/assets/js/bold-and-bright.js"></script>
    <script src="../general/assets/js/Dark-Mode-Switch-darkmode.js"></script>
</body>

</html>