<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "attendaance_system";
$port = 3307;
$conn = mysqli_connect($host, $username, $password, $database, $port);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

?>