<?php
// Start session
// session_start();

require './../includes/app.php' ;
header("Content-Type: application/json");

// Check if POST request contains 'username'
if (isset($_POST["username"])) {
    $user = $_POST["username"];

    // Validate the input
    if (empty($user)) {
        echo json_encode(["status" => "error", "message" => "Username cannot be empty."]);
        http_response_code(400);
        exit();
    }

    // Prepare SQL statement
    $stmt = $connect->prepare("SELECT * FROM users WHERE mobile = ?");
    $stmt->bind_param('s', $user);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        // Check for correct credentials
        if ($user == $row['pin'] || $user == $row['mobile']) {
            session('user_data'  , $row);
            echo json_encode(["status" => "success", "message" => "login succesfully."  ]);
            http_response_code(200);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid credentials."]);
            http_response_code(401);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not found."]);
        http_response_code(404);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Username not provided."]);
    http_response_code(400);
}
?>