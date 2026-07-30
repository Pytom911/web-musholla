<?php
$host = "localhost";
$username = "root";
$pass = "";
$db_name = "db_musholla";

$connect = mysqli_connect($host, $username, $pass, $db_name);

if (!$connect) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>