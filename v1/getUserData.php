<?php
require('includes/app.php');
header("Content-Type: application/json");
$user  = session('user_data');
$user_data = firstDB('users', 'WHERE username=' . $user['username']);

$srv_id = $user_data['current_profile'];

// Fetch network profiles
$stmt = $connect->prepare("SELECT * FROM networks_profiles WHERE id = ?");
$stmt->bind_param("s", $srv_id);
$stmt->execute();
$row_1 = $stmt->get_result()->fetch_assoc();
$stmt->close();
$user_data['networks_profiles'] = $row_1 ;
$user  = session('user_data' ,$user_data) ;
echo json_encode(["status" => "success", 'data' => $user_data]);
http_response_code(200);
?>