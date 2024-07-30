<?php
require('includes/app.php') ;
header("Content-Type: application/json");

if (isset($_POST["subscription_id"])) {
    $srv_id = $_POST['subscription_id'];


    $user  = session('user_data');
    $user = firstDB('users' ,'WHERE username='.$user['username']);   


    if (!$user) {
        echo json_encode(["status" => "error", "message" => "User not authenticated."]);
        http_response_code(401);
        exit;
    }

    $username = $user['username'];
    $password = isset($user['password']) ? $user['password'] : '';
    $user_crnt_balance = $user['balance'];

    $row_1_query = mysqli_query($connect, "SELECT * FROM networks_profiles WHERE id = '$srv_id'");
    $row_1 = mysqli_fetch_assoc($row_1_query);

    if (is_null($row_1)) {
        echo json_encode(["status" => "error", "message" => "Subscription not found."]);
        http_response_code(200);
        exit;
    }

    $admin = $row_1['network_admin'];
    $speed = $row_1['srv_upload'] . "/" . $row_1['srv_download'];
    $srv_d = $row_1['srv_download'];
    $srv_u = $row_1['srv_upload'];
    $req_srv_price = $row_1['srv_price'];
    $total_qt = $row_1['total_qouta'];

    // Calculate old quota
    $count_tx_query = mysqli_query($connect, "SELECT SUM(acctinputoctets) + SUM(acctoutputoctets) AS total_used_traffic FROM radacct WHERE username = '$username'");
    $count_tx = mysqli_fetch_assoc($count_tx_query);
    $old_qouta = $count_tx['total_used_traffic'] * 1;

    // Check if user is online
    $online_check_query = mysqli_query($connect, "SELECT COUNT(*) AS count FROM radacct WHERE username = '$username' AND acctstoptime IS NULL");
    $online_check = mysqli_fetch_assoc($online_check_query);

    // Delete old radcheck and radreply records
    mysqli_query($connect, "DELETE FROM radcheck WHERE username = '$username'");
    mysqli_query($connect, "DELETE FROM radreply WHERE username = '$username'");

    // Archive user data
    mysqli_query($connect, "INSERT INTO users_rad_acct (username, charge_time, total_tx) VALUES ('$username', CURRENT_TIMESTAMP, '$old_qouta')");
    mysqli_query($connect, "INSERT INTO network_fincial_inputs (value, time, network_admin) VALUES ('$req_srv_price', CURRENT_TIMESTAMP, '$admin')");
    mysqli_query($connect, "INSERT INTO network_fincial_inputs (value, time, network_admin, type) VALUES ('$req_srv_price', CURRENT_TIMESTAMP, '$admin', 5)");

    if ($online_check['count'] > 0) {
        dissconnect_user_byid($username, $connect);
    }

    sleep(3);
    mysqli_query($connect, "DELETE FROM radacct WHERE username = '$username'");

    // Check if user has enough balance
    if ($user_crnt_balance >= $req_srv_price) {
        $txt_04 = "اشعار : عملية شحن باقة شحن كامل ناجحة";
        $acct_balance = ($user_crnt_balance - $req_srv_price);

        // Calculate expiration time
        if ($row_1['time_index'] == 0) {
            $newtime = $row_1['srv_time'];
            $expire = date('Y-m-d', strtotime("+$newtime days"));
            $date_exp = sprintf("%s %2d:00", $expire, date('H'));
        } else if ($row_1['time_index'] == 1) {
            $srv_tm = $row_1['srv_time'];
            $prefix = "+$srv_tm hours";
            $date_exp = date("Y-m-d H:i", strtotime($prefix));
        }

        $epoch = (new DateTime($date_exp, new DateTimeZone("Africa/Cairo")))->format("U");
        // Update user data
        $sql = "UPDATE users SET balance = '$acct_balance', current_profile = '$srv_id', download = '$srv_d', upload = '$srv_u', recharge_status = '1', expire_date = '$date_exp', active_recharge = '1', total_qouta = '$total_qt', charged_time = CURRENT_TIMESTAMP WHERE username = '$username'";
        mysqli_query($connect, $sql);


        mysqli_query($connect, "UPDATE users_expire_acct SET expire_datetime = '$date_exp' WHERE username = '$username'");
        mysqli_query($connect, "INSERT INTO radcheck (username, attribute, op, value) VALUES ('$username', 'Expiration', ':=', '$epoch')");

        if ($user['srvtype'] == 'Login-User') {
            mysqli_query($connect, "INSERT INTO radcheck (username, attribute, op, value) VALUES ('$username', 'Max-All-Limit-Total', ':=', '$total_qt')");
        } else if ($user['srvtype'] == 'Framed-User') {
            mysqli_query($connect, "INSERT INTO radcheck (username, attribute, op, value) VALUES ('$username', 'Max-All-Limit-Xmit', ':=', '$total_qt')");
        }

        mysqli_query($connect, "INSERT INTO radreply (username, attribute, op, value) VALUES ('$username', 'Mikrotik-Rate-Limit', '=', '$speed')");
        mysqli_query($connect, "INSERT INTO user_profile_charge_history (username, charge_value, charged_profile, charge_by, charged_by_admin, charged_by_user) VALUES ('$username', '$req_srv_price', '$row_1[srv_name]', '', '0', '1')");

        if (($user['is_mac'] == 1) || empty($password)) {
            mysqli_query($connect, "INSERT INTO radcheck (username, attribute, op, value) VALUES ('$username', 'Auth-Type', ':=', 'Accept')");
        } else {
            $sql_08 = "INSERT INTO radcheck(username, attribute, op, value)
            SELECT t.username, 'Password-With-Header', ':=', CONCAT('{ssha512}', REPLACE(TO_BASE64(CONCAT(UNHEX(SHA2(CONCAT(t.password, t.salt), 512)), t.salt)), '\n', ''))
            FROM (SELECT '$username' AS username, '$password' AS password, UNHEX(MD5(UUID())) AS salt) t;";
            mysqli_query($connect, $sql_08);
        }

        $inst_04 = "INSERT INTO users_notifications (username, text, type, time) VALUES ('$username', '$txt_04', 0, CURRENT_TIMESTAMP)";
        mysqli_query($connect, $inst_04);

        echo json_encode(["status" => "success", "message" => "ت"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Insufficient balance."]);
        http_response_code(400);
    }
} else {
    echo json_encode(["status" => "error", "message" => "subscription_id not provided."]);
    http_response_code(400);
}

function dissconnect_user_byid($id, $connect) {
    $data = mysqli_query($connect, "SELECT * FROM radacct WHERE username = '$id' AND acctstoptime IS NULL");
    $row = mysqli_fetch_assoc($data);
    $session_id = $row['acctsessionid'];
    $nas_ip = $row['packetsrcaddress'];
    $framed_ip = $row['framedipaddress'];
    $srv_type = $row['servicetype'];

    if ($srv_type == 'Framed-User') {
        $cmd_0 = "echo Acct-Session-Id=$session_id | radclient -x $nas_ip:3799 disconnect radsec";
    } else {
        $cmd_0 = "echo Acct-Session-Id=$session_id, Framed-IP-Address=$framed_ip | radclient -x $nas_ip:3799 disconnect radsec";
    }

    $output = exec($cmd_0);
    return $cmd_0 . " || " . $output;
}
?>