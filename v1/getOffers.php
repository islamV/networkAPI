<?php
require './../includes/app.php';
header("Content-Type: application/json");
$user  = session('user_data');
$admin = $user['created_by'];

$offers = getDB(  'networks_profiles',
"WHERE network_admin = '$admin' 
AND enable_status !=1 AND total_qouta != 0 "
);
// var_dump($offers);



if($offers ){
echo json_encode(array('status' => 'success',
    'offers' => $offers ,
    'count' => $offers['count']
 ));
  http_response_code(200);
}else{
echo json_encode(["status" => "error", "message" => "data not found."]);
        http_response_code(404);
        exit;
}