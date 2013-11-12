<?php
require_once("conf/base.php");
require_once("lib/bencode.php");
require_once("log.php");
require_once("database.php");
require_once("template.php");

function get_params() {
    $query = explode("&", $_SERVER["QUERY_STRING"]);
    $params = array();

    foreach($query as &$param) {
        list($name, $value) = explode("=", $param);
        $params[urldecode($name)][] = urldecode($value);
    }

    return $params;
}

function random_string($length) {
    $random = "";
    for($index = 0; $index < $length; $index++) {
        $random .= chr(mt_rand(33, 126));
    }
    return $random;
}

function flush_files(&$db, &$path, $delete = true) {
    $handle = opendir($path);

    while(true) {
        $file_name = readdir($handle);

        if($file_name === false) { break; }

        $extension = strtolower(substr(strrchr($file_name, "."), 1));
        if($extension != "torrent") { continue; }

        $file_path = $path."/".$file_name;

        $torrent = Lightbenc::bdecode_file($file_path);
        $info = $torrent["info"];
        $info_encoded = Lightbenc::bencode($info);

        $info_hash = sha1($info_encoded, true);
        $info_hash_b64 = base64_encode($info_hash);

        $files = $info["files"];

        if($files) {
        } else {
            $file = array(
                "info_hash" => $info_hash,
                "info_hash_b64" => $info_hash_b64,
                "comment" => $torrent["comment"],
                "name" => $info["name"],
                "size" => $info["length"],
                "md5" => $info["md5sum"],
                "mode" => 1,
            );
        }

        ensure_file($db, $file);

        $delete && unlink($file_path);
    }

    closedir($handle);

    return $random;
}

function ensure_file(&$db, &$file) {
    $query = sprintf("select count(1) from file where info_hash = '%s'", $file["info_hash_b64"]);
    $exists = $db->querySingle($query);

    if($exists == 0) {
        $query = sprintf("insert into file(info_hash, comment, name, size, md5, mode) values('%s', '%s', '%s', '%d', '%s', '%d')", $file["info_hash_b64"], $file["comment"], $file["name"], $file["size"], $file["md5"], $file["mode"]);
        $db->exec($query);
    } else {
        $query = sprintf("update file set comment = '%s', name = '%s', size = %d, md5 = '%s', mode = %d where info_hash = '%s'", $file["comment"], $file["name"], $file["size"], $file["md5"], $file["mode"], $file["info_hash_b64"]);
        $db->exec($query);
    }
}

function create_database(&$db) {
    $db->exec("create table configuration(key string, value string)");
    $db->exec("create table file(info_hash string, comment string, name string, size integer, md5 string, mode integer)");
    $db->exec("create table peer(peer_id string, ip string, port string, client string, version string)");
    $db->exec("create table peer_file(peer_id string, info_hash string, client string, version string, downloaded integer, uploaded integer, left integer, status integer, timestamp double precision)");

    $db->exec("create index configuration_key on configuration(key)");
    $db->exec("create index file_info_hash on file(info_hash)");
    $db->exec("create index file_name on file(name)");
    $db->exec("create index peer_peer_id on peer(peer_id)");
    $db->exec("create index peer_file_peer_id on peer_file(peer_id)");
    $db->exec("create index peer_file_info_hash on peer_file(info_hash)");
    $db->exec("create index peer_file_status on peer_file(status)");
}

function create_configuration(&$db) {
    $tracker_id = random_string(20);
    $query = sprintf("insert into configuration(key, value) values('%s', '%s')", "tracker_id", $tracker_id);
    $db->exec($query);
}

function delete_database() {
    global $DATABASE_PATH;
    unlink($DATABASE_PATH);
}

function touch_peers(&$db) {
    $query = sprintf("delete from peer_file where timestamp < %f", time());
    $db->exec($query);
}

function &get_peers(&$db, &$info_hash_b64, $compact = 0, $extended = 0, $touch = 1) {
    // in case the touch flag is set the peer file
    // relations must be house kept to the current time
    if($touch == 1) { touch_peers($db); }

    // creates the query to select the complete set of
    // peers for the file with the provided info hash
    // and executes it retrieving the results
    $query = sprintf("select * from peer_file left join peer on peer_file.peer_id = peer.peer_id where peer_file.info_hash = '%s'", $info_hash_b64);
    $results = $db->query($query);

    // creates the array that will contain the complete
    // set of peers and iterates over the results to
    // contruct the set from the results
    $peers = array();
    while($row = $results->fetchArray()) {
        // in case the retrieval type is compact only
        // the ip information and port should be returned
        if($compact == 1) {
            $port = intval($row["port"]);
            $ip_split = explode(".", $row["ip"]);
            $ip_integer = intval($ip_split[0]) << 24 | intval($ip_split[1]) << 16 | intval($ip_split[2]) << 8 | intval($ip_split[3]);
            $peer = pack("Nn", $ip_integer, $port);
        }
        // otherwise the normal (full) mode should be used
        // and the peer should include much more information
        else {
            if($extended == 1) {
                $peer = array(
                    "peer id" => base64_decode($row["peer_id"]),
                    "ip" => $row["ip"],
                    "port" => intval($row["port"]),
                    "isDct" => true,
                );
            } else {
                $peer = array(
                    "peer id" => base64_decode($row["peer_id"]),
                    "peer_id" => base64_decode($row["peer_id"]),
                    "ip" => $row["ip"],
                    "port" => intval($row["port"]),
                    "client" => $row["client"],
                    "version" => $row["version"],
                    "uploaded" => $row["uploaded"],
                    "downloaded" => $row["downloaded"],
                    "left" => $row["left"],
                    "status" => $row["status"],
                    "isDct" => true,
                );
            }
        }

        // adds the created peer structure to the list
        // of peers for the requested file
        $peers[] = $peer;
    }

    // in case the compact mode is set, must join
    // all the peer string into on solo string
    if($compact == 1) { $peers = implode($peers); }

    // returns the list of peers that was just contructed
    // to the caller method
    return $peers;
}

function &get_files(&$db) {
    $query = sprintf("select * from file");
    $results = $db->query($query);

    $files = array();

    while($row = $results->fetchArray()) {
        $file = array(
            "info_hash" => base64_decode($row["info_hash"]),
            "info_hash_b64" => $row["info_hash"],
            "name" => $row["name"],
            "size" => $row["size"],
            "md5" => $row["md5"],
            "mode" => $row["mode"],
        );
        $files[] = $file;
    }

    return $files;
}

function &get_files_info_hash(&$db) {
    $query = sprintf("select info_hash from file");
    $results = $db->query($query);

    $files = array();

    while($row = $results->fetchArray()) {
        $files[] = base64_decode($row["info_hash"]);
    }

    return $files;
}

function &get_file(&$db, &$info_hash_b64) {
    $query = sprintf("select * from file where info_hash = '%s'", $info_hash_b64);
    $row = $db->querySingle($query, true);

    $file = array(
        "info_hash" => base64_decode($row["info_hash"]),
        "info_hash_b64" => $row["info_hash"],
        "comment" => $row["comment"],
        "name" => $row["name"],
        "size" => $row["size"],
        "md5" => $row["md5"],
        "mode" => $row["mode"],
    );

    $file["peers"] = get_peers($db, $info_hash_b64);
    $file["complete"] = get_complete($db, $info_hash_b64);
    $file["incomplete"] = get_incomplete($db, $info_hash_b64);

    return $file;
}

function &get_scrap(&$db, &$info_hash_b64) {
    $query = sprintf("select * from file where info_hash = '%s'", $info_hash_b64);
    $row = $db->querySingle($query, true);

    $file = array(
        "info_hash" => base64_decode($row["info_hash"]),
        "name" => $row["name"],
    );

    $file["complete"] = get_complete($db, $info_hash_b64);
    $file["incomplete"] = get_incomplete($db, $info_hash_b64);
    $file["downloaded"] = get_complete($db, $info_hash_b64);

    return $file;
}

function &get_complete(&$db, &$info_hash_b64) {
    $query = sprintf("select count(1) from peer_file where peer_file.info_hash = '%s' and peer_file.status = 2", $info_hash_b64);
    $complete = $db->querySingle($query);
    return $complete;
}

function &get_incomplete(&$db, &$info_hash_b64) {
    $query = sprintf("select count(1) from peer_file where peer_file.info_hash = '%s' and peer_file.status = 1", $info_hash_b64);
    $incomplete = $db->querySingle($query);
    return $incomplete;
}

function &get_configuration(&$db, &$key) {
    $query = sprintf("select value from configuration where configuration.key = '%s'", $key);
    $value = $db->querySingle($query);
    return $value;
}

function &load_structure() {
    // references the global variables
    global $B_CLIENTS;

    // creates a new array to load the structure
    $structure = array();

    // retrieves the various tracker related variables
    // they are going to be used during processing
    $info_hash = $_GET["info_hash"];
    $peer_id = $_GET["peer_id"];
    $port = $_GET["port"];
    $uploaded = $_GET["uploaded"];
    $downloaded = $_GET["downloaded"];
    $left = $_GET["left"];
    $compact = $_GET["compact"];
    $no_peer_id = $_GET["no_peer_id"];
    $event = $_GET["event"];
    $ip = $_GET["ip"];
    $numwant = $_GET["numwant"];
    $key = $_GET["key"];

    // sets the various tracker related variables in
    // the tracker structrure
    $structure["info_hash"] = $info_hash;
    $structure["peer_id"] = $peer_id;
    $structure["port"] = $port;
    $structure["uploaded"] = $uploaded;
    $structure["downloaded"] = $downloaded;
    $structure["left"] = $left;
    $structure["compact"] = $compact;
    $structure["no_peer_id"] = $no_peer_id;
    $structure["event"] = $event;
    $structure["ip"] = $ip ? $ip : $_SERVER["REMOTE_ADDR"];
    $structure["numwant"] = $numwant;
    $structure["key"] = $key;
    $structure["trackerid"] = $trackerid;

    // sets the status variable (download status accordingly)
    $structure["status"] = $left == 0 ? 2 : 1;

    // calculates the next timestamp value according to the
    // timeout and tolerance values
    $structure["timestamp"] = time() + TIMEOUT + TOLERANCE;

    // retreives the various client information from the
    // peer identfier and the parses it correctly
    $client_id = substr($peer_id, 1, 2);
    $version_major = substr($peer_id, 3, 1);
    $version_middle = substr($peer_id, 4, 1);
    $version_minor = substr($peer_id, 5, 1);
    $version_nano = substr($peer_id, 6, 1);
    $version = sprintf("%d.%d.%d", intval($version_major), intval($version_middle), intval($version_minor));
    $client = $B_CLIENTS[$client_id] ? $B_CLIENTS[$client_id] : "Unknown";
    $structure["client_id"] = $client_id;
    $structure["version_major"] = $version_major;
    $structure["version_middle"] = $version_middle;
    $structure["version_minor"] = $version_minor;
    $structure["version_nano"] = $version_nano;
    $structure["client"] = $client;
    $structure["version"] = $version;

    // calculates the base64 values for the binary values
    // they will be very usefull from now onwards
    $structure["info_hash_b64"] = base64_encode($info_hash);
    $structure["peer_id_b64"] = base64_encode($peer_id);

    // returns the "just" created structure
    return $structure;
}

function validate_structure(&$structure) {
    if(!$structure[info_hash]) { return false; }
    if(!$structure[peer_id]) { return false; }
    if(!$structure[ip]) { return false; }
    return true;
}

function ensure_structure(&$db, &$structure) {
    $query = sprintf("select count(1) from peer where peer_id = '%s'", $structure["peer_id_b64"]);
    $exists = $db->querySingle($query);

    if($exists == 0) {
        $query = sprintf("insert into peer(peer_id, ip, port, client, version) values('%s', '%s', '%s', '%s', '%s')", $structure["peer_id_b64"], $structure["ip"], $structure["port"], $structure["client"], $structure["version"]);
        $db->exec($query);
    }

    $query = sprintf("select count(1) from file where info_hash = '%s'", $structure["info_hash_b64"]);
    $exists = $db->querySingle($query);

    if($exists == 0) {
        $query = sprintf("insert into file(info_hash) values('%s')", $structure["info_hash_b64"]);
        $db->exec($query);
    }

    $query = sprintf("select count(1) from peer_file where peer_id = '%s' and info_hash = '%s'", $structure["peer_id_b64"], $structure["info_hash_b64"]);
    $exists = $db->querySingle($query);

    if($exists == 0) {
        $query = sprintf("insert into peer_file(peer_id, info_hash, client, version) values('%s', '%s', '%s', '%s')", $structure["peer_id_b64"], $structure["info_hash_b64"], $structure["client"], $structure["version"]);
        $db->exec($query);
    }

    if($structure["event"] == "stopped") {
        $query = sprintf("delete from peer_file where peer_id = '%s' and info_hash = '%s'", $structure["peer_id_b64"], $structure["info_hash_b64"]);
        $db->exec($query);
    } else {
        $query = sprintf("update peer_file set downloaded = %d, uploaded = %d, left = %d, status = %d, timestamp = %f where peer_id = '%s' and info_hash = '%s'", $structure["downloaded"], $structure["uploaded"], $structure["left"], $structure["status"], $structure["timestamp"], $structure["peer_id_b64"], $structure["info_hash_b64"]);
        $db->exec($query);
    }
}

function &create_response($interval = TIMEOUT, &$tracker_id = "default", $complete = 0, $incomplete = 0, &$peers = array()) {
    // creates a new array to load the response
    $response = array();

    // populates the response map with the various
    // required response values
    $response["interval"] = $interval;
    $response["tracker id"] = $tracker_id;
    $response["complete"] = $complete;
    $response["incomplete"] = $incomplete;
    $response["peers"] = $peers;

    // sets the current array as a dictionary
    // value for serialization reference
    $response["isDct"] = true;

    // returns the reponse map, that includes all
    // the required information to be returned to
    // the requesting client
    return $response;
}
?>
