<?php

session_start();

$helpers =  ["helper" , "db" ,"sessions_helper" ,'routing'];
foreach($helpers  as $helper){
    require  __DIR__ ."/helpers/".$helper.".php" ; 
}

$connect = mysqli_connect("localhost", "root", "islamroot1234", "radius");
 
$query= "" ;
if(!$connect){
    die("connected to database Failed" .mysqli_connect_error());
}

require_once base_path('routes/api');
require_once base_path('includes/exception_error');


route_init();


?>