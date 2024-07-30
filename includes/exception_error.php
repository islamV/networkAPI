<?php


/**
 *
 * Exception Handling URL Pages
 */
header("Content-Type: application/json");

$GET_ROUTES = isset($routes['GET']) ? $routes['GET'] : [];

$POST_ROUTES = isset($routes['POST']) ? $routes['POST'] : [];


function isUserAuthenticated() {
    return null !== session('user_data');
}

if( $_SERVER["REQUEST_METHOD"] == "GET"  ) {

    if (!in_array(segment(), array_column($GET_ROUTES, 'segment'))  && isUserAuthenticated()) {
        echo json_encode(["status" => "error", "message" => "Route not found"]);
        http_response_code(404);
        exit();
    }elseif( !isUserAuthenticated()){
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        http_response_code(404);
        exit();
    }
  
}

if( $_SERVER["REQUEST_METHOD"] == "POST"  ) {

    if (!in_array(segment(), array_column($POST_ROUTES, 'segment'))  && isUserAuthenticated()) {
        echo json_encode(["status" => "error", "message" => "Route not found"]);
        http_response_code(404);
        exit();
    }
    elseif( !in_array(segment(), array_column($POST_ROUTES, 'segment'))  && !isUserAuthenticated()){
        echo json_encode(["status" => "error", "message" => "Unauthorizeds"]);
        http_response_code(404);
        exit();
    }
  
}

?>