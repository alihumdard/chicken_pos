<?php
$con = mysqli_connect('localhost', 'root', '', 'rana_chicken');
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}
$today = date('Y-m-d'); // Current date: 2025-08-22 (as per prompt)
?>