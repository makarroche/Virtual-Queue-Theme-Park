<?php
	session_start();

	//Includes
	require_once('../../configure.php');

	$user = $_POST['userInput'];
	$clave = $_POST['passwordInput'];

	$database = "theme_park";
	$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
	if ($db_found) {
		$SQL = $db_found->prepare("SELECT * FROM usuarios WHERE UserName=? AND Password=md5(?)");
		if ($SQL) {
		   	$SQL->bind_param('ss', $user, $clave);
			$SQL->execute();
			$res = $SQL->get_result();
			if ($res->num_rows != NULL) {
			    $row = $res->fetch_assoc();

			    $_SESSION['firstName'] = $row['Nombre'];
		        $_SESSION['lastName'] = $row['Apellido'];
		        $_SESSION['logged'] = true;	
				setcookie("user", $user, time()+2592000);
				header("Location: dashboard.php");
			}
			else{
				$_SESSION['logged'] = false;
				$_SESSION['error'] = "Usuario o Contraseña incorrectos";
				header("Location: login.php");
			}
		}
	}
	$SQL->close();
	$db_found->close();
?>
