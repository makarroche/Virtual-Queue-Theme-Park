<?php
session_start();

$_SESSION['logged'] = false;
unset($_SESSION['firstName']);
unset($_SESSION['lastName']);
unset($_SESSION['user']);

header("Location: login.php");

?>
