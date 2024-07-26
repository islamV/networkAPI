<?php

require './../includes/app.php';
header("Content-Type: application/json");
$user  = session('user_data');
$user_data = firstDB('users' ,'WHERE username='.$user['username']);
  echo json_encode(["status" => "success"  , 'data'=>$user_data]);
  http_response_code(200);




  ?>