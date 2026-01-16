<?php
$host = "ixnzh1cxch6rtdrx.cbetxkdyhwsb.us-east-1.rds.amazonaws.com";
$user = "jufrdyqihgrkgrfz";
$pass = "nav270mtf2ouerzs";
$db   = "x9luliyya0uk2dhv";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>