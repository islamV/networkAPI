<?php


/**
 *
 * Exception Handling URL Pages
 */
header("Content-Type: application/json");

$GET_ROUTES = isset($routes['GET']) ? $routes['GET'] : [];
$POST_ROUTES = isset($routes['POST']) ? $routes['POST'] : [];


if( $_SERVER["REQUEST_METHOD"] == "GET" &&!in_array(segment(), array_column($GET_ROUTES, 'segment'))  ) {
    echo json_encode(["status" => "error", "message" => "Route not found"]);
    http_response_code(404);
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST" && !in_array(segment(), array_column($POST_ROUTES , 'segment')) ) {
    echo json_encode(["status" => "error", "message" => "Route not found"]);
    http_response_code(404);
    exit();
}