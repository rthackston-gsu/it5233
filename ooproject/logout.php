<?php
session_start();

$_SESSION["userid"] = "";
$_SESSION["isadmin"] = "";

session_destroy();

header("Location: login.php");
exit();
?>