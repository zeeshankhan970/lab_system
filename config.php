<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
date_default_timezone_set("Asia/Karachi");
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli("localhost", "root", "", "lab_system");

if ($conn->connect_error) {          // ✅ check error FIRST
    die("DB connection failed");
}

$conn->set_charset("utf8mb4");       // ✅ then set charset
?>