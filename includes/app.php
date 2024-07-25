<?php

$helpers =  ["helper" , "db" ,"sessions_helper" ,'routing'];
foreach($helpers  as $helper){
    require  __DIR__ ."/helpers/".$helper.".php" ; 
}

$connect = mysqli_connect(
    config('database.servername'),
    config('database.username'),
    config('database.password'),
    config('database.database'),
    config('database.port'),
);
 
$query= "" ;
if(!$connect){
    die("connected to database Failed" .mysqli_connect_error());
}

require_once base_path('routes/api');
require_once base_path('includes/exception_error');


route_init();