<?php

$servername="LAPTOP-ADJVF762";
$databases="ProdStockMailleDB";
$userid="sa";
$password="12345678";

$connexion=[
    "Database" => $databases,
    "Uid" => $userid,
    "PWD" => $password,
];

$con= sqlsrv_connect($servername,$connexion);
if(!$con)
    die(print_r(sqlsrv_errors(),true));
// else
// echo 'connexion réussi';

?>
