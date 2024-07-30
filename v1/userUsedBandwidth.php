<?php
require('includes/app.php');

header("Content-Type: application/json");


$user = session('user_data');
$username = $user['username'];

function formatBytes($bytes, $precision = 2)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}



// Fetch data from database
$current_session = getDB('radacct', "WHERE username = '$username' AND acctstoptime IS NULL GROUP BY `radacctid` LIMIT 1");


   if($user['daily_qouta'] > 0){
      $total_used_traffic = getDB('radacct', "WHERE username = '$username' AND DATE(acctstarttime) = DATE(CURRENT_TIMESTAMP)", "SUM(acctinputoctets) + SUM(acctoutputoctets) AS total_used_traffic");

}

if($user['total_qouta'] > 0){
   $total_used_traffic = getDB('radacct', "WHERE username = '$username'", "SUM(acctinputoctets) + SUM(acctoutputoctets) AS total_used_traffic");
}
$gb  = $user['data']['networks_profiles']['gb_real_value'] ;
$total_used_traffic['total_used_traffic'] =   $total_used_traffic['total_used_traffic'] * $gb;

  $mac = getDB('radacct', "WHERE username = '$username' GROUP BY callingstationid", "callingstationid, COUNT(*) AS count, SUM(acctsessiontime) AS tm");

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
    "WHERE username = '$username' 
    GROUP BY year, month 
    ORDER BY MAX(acctstarttime) DESC",
    "DATE_FORMAT(acctstarttime, '%Y') AS year, 
    DATE_FORMAT(acctstarttime, '%m') AS month, 
    COUNT(radacctid) AS count_id, 
    SUM(acctsessiontime) AS used_tm, 
    SUM(acctoutputoctets) + SUM(acctinputoctets) AS used_tx"
);

foreach($monthly_consumption['data'] as &$m) {
    $m['used_tx'] = formatBytes($m['used_tx'], 2);
}

foreach($daily_consumption['data'] as &$m) {
    $m['used_tx'] = formatBytes($m['used_tx'], 2);
}


// Output response

    echo json_encode(array('status' => 'success', 'data' => [
        'current_session' => $current_session,
        'mac' => $mac,
        'daily_consumption' => $daily_consumption,
        'monthly_consumption' => $monthly_consumption,

         'total_used_traffic'=> $total_used_traffic 
    ]));
    http_response_code(200);

?>