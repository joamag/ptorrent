<?php
function &get_database() {
    // refers the global reference to the database
    // path to be used in the current context
    global $DATABASE_PATH;

    // checks if the database file path exits in case
    // it does not (assumes new database)
    $is_new = !file_exists($DATABASE_PATH);

    // operates the database updating all the required structure
    // values according to the client loop
    $db = new SQLite3($DATABASE_PATH);
    $is_new && create_database($db);
    $is_new && create_configuration($db);

    // returns the just created dabase to be used by the
    // caller (ready for operations)
    return $db;
}
?>
