<?php
require("base.php");

// retrieves the current set of get params
// as sequences instead of single values
$params = get_params();

// creates the list that will hold the various
// files to send the scrape information
$files = array(
    "isDct" => true,
);

// operates the database updating all the required structure
// values according to the client loop
$db = new SQLite3($DATABASE_PATH);
$is_new && create_database($db);
$is_new && create_configuration($db);
if(!$params["info_hash"]) { $params["info_hash"] = get_files_info_hash($db); }
foreach($params["info_hash"] as $info_hash) {
    $info_hash_b64 = base64_encode($info_hash);
    $file = get_scrap($db, $info_hash_b64);
    $files[$info_hash] = $file;
    $files[$info_hash]["isDct"] = true;
}
$db->close();

// creates the array that will hold the response and then 
// converts it into a string (for latter logging), then encodes
// it into bencode in order to be sent as the response
$response = array(
    "files" => $files,
    "isDct" => true,
);
$response_string = print_r($response, true);
$response_encoded = Lightbenc::bencode($response);

// sets the appropriate content type for the request
// and then prints the encoded response
header("Content-Type: text/plain");
print($response_encoded);

// logs the various structures into the current log
// file for latter reference
log_message($response_string);
?>
