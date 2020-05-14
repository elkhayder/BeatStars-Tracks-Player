<?php
#!/usr/bin/env php
/*
 ______ _ _    _                 _____            
|  ____| | |  | |               |  __ \           
| |__  | | | _| |__   __ _ _   _| |  | | ___ _ __ 
|  __| | | |/ / '_ \ / _` | | | | |  | |/ _ \ '__|
| |____| |   <| | | | (_| | |_| | |__| |  __/ |   
|______|_|_|\_\_| |_|\__,_|\__, |_____/ \___|_|   
                            __/ |                 
                           |___/   
* Beatstars tracks player BOT.
* Made with ♡ by elkhayder
* Check me on :
* => Instagram: @elkhayder.zakaria [https://www.instagram.com/elkhayder.zakaria]
* => Facebook: ElkhayDer Zakaria [https://www.facebook.com/ElkhayDerZakaria.II]
* => Github: elkhayder [https://github.com/elkhayder]
* => Email : zelkhayder@gmail.com
*/  
set_time_limit(0);
function iread($length) {
    return str_replace(PHP_EOL, '', fread(STDIN, $length)); // Read user input and remove the line jump
}
function TrackInfo($id) {
    $request = curl_init('https://main.v2.beatstars.com/beat?id='.$id.'&fields=details,stats'); // INIT cURL Request
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_HTTPHEADER, array(
        "Content-Type:application/json",
    ));
    $response = json_decode(curl_exec($request));
    if (curl_errno($request)) {
        die("cURL request Error : ". curl_error($request)); // Check For cURL errors
    }
    curl_close($request);
    return $response;
}          
/* Start of user communication */
fwrite(STDOUT, "
 ______ _ _    _                 _____            
|  ____| | |  | |               |  __ \           
| |__  | | | _| |__   __ _ _   _| |  | | ___ _ __ 
|  __| | | |/ / '_ \ / _` | | | | |  | |/ _ \ '__|
| |____| |   <| | | | (_| | |_| | |__| |  __/ |   
|______|_|_|\_\_| |_|\__,_|\__, |_____/ \___|_|   
                            __/ |                 
                           |___/   
"); 
sleep(1);
fwrite(STDOUT, chr(27).chr(91).'H'.chr(27).chr(91).'J');   //Clear Output by typing ^[H^[J 
fwrite(STDOUT, "Enter Track ID: "); // Request For track ID
$track_id = (int) intval(iread(10)); // Read Track ID
$track_info = TrackInfo($track_id); // Get Track Info
if($track_info->response->data->code !== 200) die("API request Error : " . $track_info->response->data->message); // Die if request did not go successfully
fwrite(STDOUT, "We Found : " . $track_info->response->data->details->title);
fwrite(STDOUT, PHP_EOL); // Jump Line
fwrite(STDOUT, "You want to continue ? [Y/n] ");
if(strtolower(iread(1)) !== "y") die("Exiting..."); // Exit if Used does not want to continue
fwrite(STDOUT, "Playing methods: ");
fwrite(STDOUT, PHP_EOL); // Jump Line
fwrite(STDOUT, "    [1]: Play track some spesific times");
fwrite(STDOUT, PHP_EOL); // Jump Line
fwrite(STDOUT, "    [2]: Keep playing the track untill reaching a spesific amount of plays");
fwrite(STDOUT, PHP_EOL); // Jump Line
fwrite(STDOUT, "Select your method ! [1/2] ");
$method_id = intval(iread(1));
while($method_id == "") { // Keep Checking untill User provides an answer
    $method_id = intval(iread(1));
}
switch($method_id) {
    case "1":
        $method = "fixed";
    break;
    case "2":
        $method = "flexible";
    break;
    default:
        die("Unknown method");
};
fwrite(STDOUT, "Plays : "); // Request Plays from user
$plays = (int) intval(iread(10)); // read Plays
if($plays <= $track_info->response->data->stats->plays && $method == "flexible") die("Plays should be greater than current track played times");
fwrite(STDOUT, "Count playing errors as a play ? [Y/n] "); // Request From user
$error_count = (strtolower(iread(1)) == "y") ? true : false;  // Read User data and save
fwrite(STDOUT, "Starting ..."); // Inform User at Starting
fwrite(STDOUT, PHP_EOL); // Jump Line
/* End of user communication */
///////////////////////////////////////////////////////////
$while = true;
$errors = (int) 0;
$success = (int) 0;
$count = (int) 1;
while($while) {
    fwrite(STDOUT, "[ " . str_pad($count, strlen($plays), '0', STR_PAD_LEFT) . " ] => ");
    $random_ip = long2ip(rand(0, "4294967295"));
    $request = curl_init('https://main.v2.beatstars.com/stats/track_play');
    curl_setopt($request, CURLOPT_POSTFIELDS, json_encode([
        'id' => $track_id,
        'sponsored' => false,
        "store_type" => "marketplace"
    ]));
    curl_setopt($request, CURLOPT_HTTPHEADER, [
        "Content-Type:application/json",
        "REMOTE_ADDR: $random_ip",
        "HTTP_X_FORWARDED_FOR: $random_ip"
    ]);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    $response = json_decode(curl_exec($request));
    if(curl_errno($request)) {
        fwrite(STDOUT, "cURL Error: " . curl_error($request));
        fwrite(STDOUT, PHP_EOL); // Jump Line
        $errors++;
    }
    curl_close($request);
    if($response->response->data->code !== 200) {
        fwrite(STDOUT, "API Error: " . $response->response->data->message);
        fwrite(STDOUT, PHP_EOL); // Jump Line
        $errors++;
    } else {
        fwrite(STDOUT, "Success");
        fwrite(STDOUT, PHP_EOL); // Jump Line
        $success++;
    }
    if($method == "fixed") {
        if($error_count) {
            if($success >= $plays) $while = false;
        } else {
            if($count > $plays) $while = false;
        }
    } else {
        if(intval($response->response->data->stats->plays) >= $plays) $while = false;
    }
    $count++;
}
fwrite(STDOUT, "Script Ended : " . $success . " success and " . $errors . " errors");
fwrite(STDOUT, PHP_EOL); // Jump Line
?>