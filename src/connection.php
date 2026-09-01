<?php

$servername = "localhost";
$username = "u659629058_evallo";
$password = "c6ehxV57.";
$dbname = "u659629058_async";

$mysqli = new mysqli(
    $servername,
    $username,
    $password,
    $dbname
);

/* Check connection first */
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

/* Set MySQL timezone to Philippine Time */
if (!$mysqli->query("SET time_zone = '+08:00'")) {
    die("Timezone configuration failed: " . $mysqli->error);
}
