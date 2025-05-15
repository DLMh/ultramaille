<?php

include("db.php");


//require
//require_once
$sql="SELECT * FROM `user` where `user`.`user_matric`=:mtrc and `user`.`pswd`=:mdp and `user`.`activ_profil`=:ap";
$afficher=$conn->prepare($sql);
$afficher->execute(array(
    "mtrc"=>$_POST["matr"],
    "mdp"=>md5($_POST["pw"]),
    "ap"=>1
));

$row=$afficher->rowCount(); //1 ou 0
// echo $row; 
$res=$afficher->fetch();
if($_POST["matr"]!="" and $_POST["pw"]!=""){
    if(md5($_POST["pw"])==$res["pswd"]){
        if($row>0){
            //activer session
            session_start();
            $_SESSION["vnom"]=$res["user_nom"];
            $_SESSION["vpren"]=$res["user_prenom"];
            $_SESSION["vmatr"]=$res["user_matric"];
            $_SESSION["iddus"]=$res["id_user"];
            $_SESSION["ymail"]=$res["user_mail"];
            $_SESSION["civ"]=$res["civil"];
            $_SESSION["typeU"]=$res["admin"];
            $_SESSION["iduser"]=$res["id_user"];
            include("mouchard.php");
            include("insert_user_connected.php");
                        // vérif si utilisateur est admin ou simple administrateur => admin 0 ou 1
            //menu administrateur
            if ($res["admin"]==1){
                header("location: ../front/general/menu_admin.php");
            }
            //menu utilisateur CRM et dir
            if ($res["admin"]==2){
                header("location: ../front/general/menu-general-user.php");
            }
            //les autres
            if ($res["admin"]==0){
                header("location: ../front/general/menu-general-user.php");
            }

        }else{
            header("location: ../front/general/Login/Login.php?error=true");
        
          
        }
    }else{
       
             //error_login();
             //echo "error";
             header("location: ../front/general/Login/Login.php?error=true");
    }

}
function error_login(){

    //header("location: ../index.php?error=true");
   
    header("location: ../front/general/login.php?error=true");
}
?>

