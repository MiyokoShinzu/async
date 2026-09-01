<?php

$servername = "localhost"; // Change server name
$username = "u659629058_evallo"; // Change username
$password = "c6ehxV57."; // Change password
$dbname = "u659629058_async"; // Change database name

$mysqli = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

?>
