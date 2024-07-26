<?php
require './../includes/app.php';
header("Content-Type: application/json");
$user  = session('user_data');


$username = $user['username'];

$user_profile_charge_history = getDB(  'user_profile_charge_history',
"WHERE username = '$username' 
   ORDER BY charge_time DESC "
);

$users_rad_acct =getDB(  'users_rad_acct',
"WHERE username = '$username' 
   ORDER BY charge_time DESC "
);



echo json_encode(array('status' => 'success','data'=> [
    'user_profile_charge_history' => $user_profile_charge_history,
    'users_rad_acct' => $users_rad_acct
    ]));

 ?>