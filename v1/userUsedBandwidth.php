<?php
require './../includes/app.php';
header("Content-Type: application/json");
$user = session('user_data');
$username = $user['username'];


// echo json_encode(array('status' => 'success', 'data' => $data));

// if ($check['count'] > 0) {
//     echo json_encode(array('status' => 'success', 'data' => $data));
//     http_response_code(200);
// }
function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    $bytes /= pow(1024, $pow); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 

$current_seession  = getDB('radacct', "WHERE username = $username AND acctstoptime IS NULL GROUP BY `radacctid` LIMIT 1 ",);
$mac  = getDB('radacct', "WHERE username = $username GROUP BY callingstationid", "callingstationid , COUNT(*) AS count , SUM(acctsessiontime) AS tm");
$daily_consumption = getDB(
    'radacct',
    "WHERE username = '$username' 
    GROUP BY DATE_FORMAT(acctstarttime, '%Y-%m-%d') 
    ORDER BY DATE_FORMAT(acctstarttime, '%Y-%m-%d') DESC",

    "DATE_FORMAT(acctstarttime, '%Y-%m-%d') AS active_year, 
    COUNT(radacctid) AS count_id, 
    SUM(acctsessiontime) AS used_tm, 
    SUM(acctoutputoctets) + SUM(acctinputoctets) AS used_tx"
);

$monthly_consumption = getDB(
    'radacct',
    "
    WHERE username = '$username' 
    GROUP BY year, month 
    ORDER BY MAX(acctstarttime) DESC",

    " 
        DATE_FORMAT(acctstarttime, '%Y') AS year, 
        DATE_FORMAT(acctstarttime, '%m') AS month, 
        COUNT(radacctid) AS count_id, 
        SUM(acctsessiontime) AS used_tm, 
        SUM(acctoutputoctets) + SUM(acctinputoctets) AS used_tx "
);

foreach($monthly_consumption['data'] as $m){
    $m['used_tx'] =formatBytes($m['used_tx'], 0);

}

foreach($daily_consumption['data'] as $m){
    $m['used_tx'] =formatBytes($m['used_tx'], 0);

}


if ($current_seession['count'] > 0  || $mac['count'] > 0) {

    echo json_encode(array('status' => 'success', 'data' => [
        'current_session' => $current_seession,
        'mac' => $mac,
        'daily_consumption' => $daily_consumption,
        'monthly_consumption' => $monthly_consumption,
    ]));
    http_response_code(200);
} else {
    // Handle the case where there is no data
    echo json_encode(['status' => 'error', 'message' => 'No data found.']);
    http_response_code(404);
}


?>