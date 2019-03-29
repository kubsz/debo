<?php
$servername = "localhost";
$dbusername = "root";
include($_SERVER['DOCUMENT_ROOT']."/include/config.php");
$database = "debo";

$conn = mysqli_connect($servername, $dbusername, $dbpassword, $database, "3306");

if(!$conn) {
    die("Database connection failed: ".mysqli_connect_error());
}
?>