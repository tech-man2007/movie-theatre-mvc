<?php
$host = "127.0.0.1";
$user = "root"; // Termux MariaDB default usually accepts root with no password
$password = "";
$dbname = "movie_theatre";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
