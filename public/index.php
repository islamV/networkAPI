<?php
session_start();


// require './includes/app.php' ;
header("Content-Type: application/json");
require './../includes/app.php' ;


// var_dump($routes);
// Get the request URI and parse it
// $requestUri = $_SERVER['REQUEST_URI'];
// $scriptName = $_SERVER['SCRIPT_NAME'];
// $pathInfo = str_replace($scriptName, '', $requestUri);
// $pathInfo = trim($pathInfo, '/');




// $version = array_shift($requestSegments);
// $resource = array_shift($requestSegments);


// // Debugging output
// error_log("Resource: $resource");
// error_log("Request Method: {$_SERVER['REQUEST_METHOD']}");

// switch ($resource) {
//     case 'login':
//         require_once "v1/login.php";
//         break;
//     case 'charge' :
//         require_once "v1/charge_card.php";
//         break;
//         case 'bandwidth' :
//             require_once "v1/user_used_bandwidth.php";
//             break;
//     default:
//         http_response_code(404);
//         echo json_encode(["message" => "Resource not found."]);
//         break;
// }