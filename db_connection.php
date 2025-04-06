<?php
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set character set to utf8mb4
mysqli_set_charset($conn, "utf8mb4");
?>