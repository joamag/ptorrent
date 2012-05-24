<?php
include "lib/bencode.php";
include "base.php";

// loads the structure for the current request
// and then loads it string value
$structure = load_structure();
$structure_print = print_r($structure, true);

// checks if the database file path exits in case
// it does not (assumes new database)
$is_new = !file_exists($DATABASE_PATH);

// operates the database updating all the required structure
// values according to the client loop
$db = new SQLite3($DATABASE_PATH);
$is_new && create_database($db);
$is_new && create_configuration($db);
$valid = validate_structure($structure);
$valid && ensure_structure($db, $structure);
$complete = $valid ? get_complete($db, $structure["info_hash_b64"]) : 0;
$incomplete = $valid ? get_incomplete($db, $structure["info_hash_b64"]) : 0;
$peers = $valid ? get_peers($db, $structure["info_hash_b64"]) : array();
$tracker_id = $valid ? get_configuration($db, $key = "tracker_id") : "default";
$db->close();

// creates the response map and then prints the
// response in a pretty way so that it's possible
// to log it then sets the dictionary value and
// encodes it using the bencoding strategy
$response = create_response(TIMEOUT, $tracker_id, $complete, $incomplete, $peers);
$response_print = print_r($response, true);
$response_encoded = Lightbenc::bencode($response);

// sets the appropriate content type for the request
// and then prints the encoded response
header("Content-Type: text/plain");
print($response_encoded);

$filePath = "\\tracker.log";
$file = fopen($filePath, "a+") or die("can't open file");
fwrite($file, $structure_print);
fwrite($file, $response_print);
fwrite($file, $response_encoded);
fclose($file);
?>