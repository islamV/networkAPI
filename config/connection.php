<?php

$database =  include __DIR__."/database.php";
try {
    $connect = mysqli_connect($database['servername'] , $database['username'] , $database['password'] , $database['database']);
 echo "good " ;
} catch (\Throwable $th) {
    throw $th;
}