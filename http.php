<?php
// API URL
set_time_limit(0);
ignore_user_abort(true);
$i=0;
while((!isset($_GET["plays"])) ? true : $i < $_GET["plays"]) {
    $random_ip = long2ip(rand(0, "4294967295"));
    $url = 'https://main.v2.beatstars.com/stats/track_play';
    $ch = curl_init($url);
    $data = array(
        'id' => $_GET["track"],
        'sponsored' => false,
        "store_type" => "marketplace"
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $headers = array('Content-Type:application/json', "REMOTE_ADDR: $random_ip", "HTTP_X_FORWARDED_FOR: $random_ip");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (curl_errno($ch)) die($i . "=> cURL request Error : ". curl_error($ch)); // Check For cURL errors
    curl_close($ch);
    $response = json_decode($response);
    if($response->response->data->stats->plays) 
        echo $i . " => Success | Total Plays :" . $response->response->data->stats->plays . " Requested via: " . $random_ip; 
    else 
        echo $i . " => Error";
    $i++;
    //sleep(5);
    echo "<br>";
    if(isset($_GET["delay"])) delay($_GET["delay"]);
}
?>