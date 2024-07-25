<?php
if (session('user_data')) {
    session_flush('user_data');
    echo json_encode(["status" => "success", "message" => "logout succesfully."  ]);
    http_response_code(200);
}