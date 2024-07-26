<?php
require './../includes/app.php' ;
header("Content-Type: application/json");

// Check if the 'card' parameter is set
$price = 0;
if (isset( $_POST["card"])) {
    $card_id =  $_POST["card"];
    $card_id = mysqli_real_escape_string($connect, $card_id);
    $query = "SELECT * FROM recharge_cards WHERE card_num = '$card_id' AND card_status = 0";
    $result = mysqli_query($connect, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
        
            chargeCard($row , $connect);


        } else {
            echo json_encode(array('status' => 'error', 'message' => "كرت الشحن غير موجود او مشحون من قبل ($card_id)"));
            http_response_code(404);
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Database query failed.'));
        http_response_code(500);
    }
}


function chargeCard($row ,$connect ){
    $card_id =  $_POST["card"];
    $card_id = mysqli_real_escape_string($connect, $card_id);

        // Prepare SQL statement
        $user_data = session('user_data');

        $user_data = firstDB('users' ,'WHERE username='.$user_data['username']);
        $user_data =  $user_data['data'];
    // $user_data =  $user_data['data'];
    $card_value = $row['card_value'];
    $post_credit =  $user_data['credit'] * 1;
    $post_balance =  $user_data['balance'] * 1;
    $admin = $user_data['created_by'];
   $id  = $user_data['username'];
    $act_time = date("Y-m-d H:i:s");

    updateDB('recharge_cards', [
        'card_status' => '1',
        'active_time' => $act_time,
        'charged_user' => $id

    ], "WHERE card_num ='$card_id'");


    if ($post_credit == 0) {

        $charged_val = $post_balance + $card_value;
        updateDB('users', [
            'balance' => $charged_val,
        ], " WHERE username = '$id'");
    }

    if ($post_credit > 0) {

        if ($post_credit >= $card_value) {

            $prep_val = $post_credit - $card_value;


            updateDB('users', [
                'credit' => $prep_val
            ], " WHERE username = '$id'");
        }
    }

    if ($card_value > $post_credit) {

        $prep_val = $card_value - $post_credit;
        $charged_val = $post_balance + $prep_val;
   
        updateDB('users', [
            'balance' => $charged_val,
            'credit' => '0'
        ], " WHERE username = '$id'");
    }


    $txt_04 = $card_value .  "LE " . "عملية شحن رصيد بواسطة بطاقة شحن بقيمة";

    createDB('users_notifications', [
        'username' => $id,
        'text' => $txt_04,
        'time' => date("Y-m-d H:i:s"),
        'type' => 0

    ]);



    createDB('user_charg_history', [
        'username' => $id,
        'charge_value' => $card_value,
        'charge_time' => date("Y-m-d H:i:s"),
        'credit_charge_type' => 'Charged By Card',
        'charged_user' => $id,
        'charge_by' => $id,
        'charge_card' => $card_id



    ]);
    createDB('network_fincial_inputs', [
        'value' => $card_value,
        'time' => date("Y-m-d H:i:s"),
        'network_admin' => $admin,
        'type' => 3
    ]);
    
            
    echo json_encode(array('status' => 'success', 'message' => $card_value. "LE عملية شحن رصيد بواسطة بطاقة شحن بقيمة"));
    http_response_code(200);
}

?>