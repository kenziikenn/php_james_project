<?php
session_start();
session_destroy();
header("Location: ../judge-login.php");  // Updated to match the new login file name
exit();
?>