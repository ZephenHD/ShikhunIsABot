<?php
//start a new connection to the db server
$db = new mysqli("localhost", "root", "", "webapp");
//if error connecting
if ($db->connect_error) {
    die('Connect Error: ' . $db->connect_error);
}