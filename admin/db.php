<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "admin_panel";
// change this

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
