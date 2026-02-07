<?php
//Output Buffering Ta5zin mo2e9et dans un container 
ob_start(); 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set the default time zone to France
$timezone = date_default_timezone_set("Europe/Paris");

$con = mysqli_connect("localhost", "root", "", "vcs_db");

if (mysqli_connect_errno()) {
    echo "Failed to connect: " . mysqli_connect_errno();
}

//connecte to the base
?>
