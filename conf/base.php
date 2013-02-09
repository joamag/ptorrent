<?php
/**
 * The default timeout value to be used in between
 * request to the tracker from the clients.
 */
define("TIMEOUT", 120);

/**
 * The tolerance value "given" to the client so that
 * it can provide an accurate update.
 */
define("TOLERANCE", TIMEOUT / 6);

/**
 * The path to the database for the tracked.
 * This database stores all the information
 * related with the tracker.
 */
$DATABASE_PATH = "db/ptorrent.db";

/**
 * The map associating the client
 * id preffix with the name of the
 * client it corresponds.
 */
$B_CLIENTS = array(
    "AZ" => "Azureus",
    "BC" => "BitComet",
    "UT" => "uTorrent",
);

/**
 * The timezone to be used for time
 * based operation in the system, should
 * be set to the local value of the system.
 */
$TIMEZONE = "UTC";

// defines the default time zone configuration,
// this is a system wide operation
date_default_timezone_set($TIMEZONE);
?>
