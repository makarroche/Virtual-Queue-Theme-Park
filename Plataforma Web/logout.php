<?php
session_start();

	unset($_SESSION['login']);
	unset($_SESSION['guestIdentification']);
	unset($_SESSION['autoPlay']);
	header("Location: /Estaciones/index.php");

?>