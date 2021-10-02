<?php 
	session_start();
	$guestID = $_GET['station'];
	$exists = checkExistingID($guestID);
	$_SESSION['autoPlay'] = true;

	if($exists){ // si el ID se encuentra en inventario, el login es correcto.
	    $_SESSION['login'] = true;
	    $_SESSION['guestIdentification'] = $guestID;
	    header("Location: /Estaciones/reservas.php");
	    exit;
	}
	else{
		$_SESSION['login'] = false;
		$_SESSION['mensaje'] = "Brazalete no identificado: Dirígete a un stand de ayuda";
		header("Location: /Estaciones/index.php");
		exit;
		}

	function checkExistingID($guestID){ 
		require '../configure.php';
		$database = "theme_park";
		$exist = false;
		$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );

		if ($db_found) {

			$SQL = $db_found->prepare("SELECT ID FROM guests WHERE ID=?");

			if ($SQL) {
				$SQL->bind_param('i', $guestID);
				$SQL->execute();
				$res = $SQL->get_result();

				if ($res->num_rows > 0) {

					$row = $res->fetch_assoc();

					$identification = $row['ID'];
					if($identification){
						$exist=true;
					}
				}
		    }
		}
		return $exist;
	}
?>