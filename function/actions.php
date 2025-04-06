<?php
include_once 'config.php';

$data = new Databases;

if (isset($_GET["login"])) {
    $un = $_POST["un"];
    $pw = $_POST["pw"];

    if ($getAccountCodes = $data->selectlogin('account', 'uname', '=', $un, 'pword', '=', $pw)) { 
        foreach ($getAccountCodes as $row) {
            if ($row["status"] == 1) {
                session_start();
                $_SESSION['account_id'] = $row["account_id"];
                $_SESSION['type_id'] = $row["type_id"];
                
                if ($row["type_id"] == 2) {
                    echo "tabulator";
                } elseif ($row["type_id"] == 1) {
                    echo "admin";
                }
            } else {
                echo "error";
            }
        }
    } else {
        echo "error";
    }
}

if (isset($_GET["submit"])) {
    $fn = mysqli_real_escape_string($data->con, $_POST["fn"]);
    $ln = mysqli_real_escape_string($data->con, $_POST["ln"]);
    $un = mysqli_real_escape_string($data->con, $_POST["un"]);
    $pw = mysqli_real_escape_string($data->con, $_POST["pw"]);

    // Check if username exists
    $checkQuery = "SELECT * FROM `account` WHERE `uname` = '$un'";
    $result = mysqli_query($data->con, $checkQuery);

    if (mysqli_num_rows($result) > 0) {
        echo "exists=yes";
    } else {
        // Insert new account (only as tabulator) with status = 0
        $insertQuery = "INSERT INTO `account` (`type_id`, `fn`, `ln`, `uname`, `pword`, `status`) 
                        VALUES (2, '$fn', '$ln', '$un', md5('$pw'), 0)";

        if (mysqli_query($data->con, $insertQuery)) {
            echo "saved=ok";
        } else {
            echo "error";
        }
    }
}
?>