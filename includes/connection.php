<?php 
session_start();

// Database credentials
if ($_SERVER['HTTP_HOST'] == "localhost") {
    // Local database credentials
    $serverIp = "localhost";
    $userName = "root";
    $password = "";
    $dbname = "viaviw56_hdwallpaperclient";
} else {
    // Live database credentials
    $serverIp = "HOSTNAME";
    $userName = "USERNAME";
    $password = "PASSWORD";
    $dbname = "DATABASE";
}

// Establishing connection to the database
$cn = mysqli_connect($serverIp, $userName, $password, $dbname);

if (!$cn) {
    die("Couldn't Connect: " . mysqli_connect_error());
}

// Selecting the database
$link = mysqli_select_db($cn, $dbname);

if (!$link) {
    die("Couldn't SELECT database: " . mysqli_error($cn));
}
?>
