<?php
require("base.php");

// retrieves the current set of get params
// as sequences instead of single values
$params = get_params();

// creates the list that will hold the various
// files to send the scrape information
$files = array();

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
    $files[$info_hahs]["isDct"] = true;
}
$db->close();

// converts the files map into a string (for latter logging)
// and then encodes the files map into bencode in order to
// be sent as the response
$files_string = print_r($files, true);
$files_encoded = Lightbenc::bencode($files);

// sets the appropriate content type for the request
// and then prints the encoded files
header("Content-Type: text/plain");
print($files_encoded);

// logs the various structures into the current log
// file for latter reference
log_message($files_string);
?>
