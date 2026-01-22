<?php
$host = "localhost";
$username = "root";
$password = "admin";
$db_name = "workshop_aslab";

$conn = new mysqli($host, $username, $password, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
