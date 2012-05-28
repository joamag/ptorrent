<?php
require("classes/base.php");

// loads the structure for the current request
// and then loads it string value
$structure = load_structure();
$structure_print = print_r($structure, true);

// operates the database updating all the required structure
// values according to the client loop
$db = get_database();
$valid = validate_structure($structure);
$valid && ensure_structure($db, $structure);
$complete = $valid ? get_complete($db, $structure["info_hash_b64"]) : 0;
$incomplete = $valid ? get_incomplete($db, $structure["info_hash_b64"]) : 0;
$peers = $valid ? get_peers($db, $structure["info_hash_b64"], $structure["compact"]) : array();
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

// logs the various structures into the current log
// file for latter reference
log_message($structure_print);
log_message($response_print);
log_message($response_encoded);
?>
