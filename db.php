<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "attendaance_system"; // spelling kept from your original db.php
$port = 3307;                     // change to 3306 if your XAMPP MySQL uses the default

$conn = mysqli_connect($host, $username, $password, $database, $port);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");
