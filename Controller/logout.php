<?php
session_start();
session_unset();
session_destroy();
header("Location: ../View/html/login.html");
exit();
?>

