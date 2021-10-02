<?php
session_start();

//Include library
require_once('includes/smartyAndNavbar.php');
require_once('includes/connectDB.php');

//Consulto por publicaciones de tipo receta
if ($conn) {
	$sql = "SELECT publicaciones.*, categorias.nombreCat, usuarios.nombreUsr, usuarios.apellido
			FROM publicaciones, categorias, usuarios
			WHERE publicaciones.eliminado = 0 AND publicaciones.usuario_id = usuarios.usuario_id AND publicaciones.tipo_id = 1 AND publicaciones.categoria_id = categorias.categoria_id
			ORDER BY fecha DESC LIMIT 0, 4";
	$parametros = array();
	$result = $conn->consulta($sql, $parametros);
	if ($result) {
		$recipies = $conn->restantesRegistros();
		$smarty->assign("recipies", $recipies);
	}
	else{
		echo "Error de consulta";
	}
}
else{
	echo "Error de conexión: " . $conn->ultimoError();
}

//Consulto por publicaciones de tipo nota
if ($conn) {
	$sql = "SELECT publicaciones.*, categorias.nombreCat, usuarios.nombreUsr, usuarios.apellido
			FROM publicaciones, categorias, usuarios
			WHERE publicaciones.eliminado = 0 AND publicaciones.usuario_id = usuarios.usuario_id AND publicaciones.tipo_id = 2 AND publicaciones.categoria_id = categorias.categoria_id
			ORDER BY fecha DESC LIMIT 0, 4";
	$parametros = array();
	$result = $conn->consulta($sql, $parametros);
	if ($result) {
		$notes = $conn->restantesRegistros();
		$smarty->assign("notes", $notes);
	}
	else{
		echo "Error de consulta";
	}
}
else{
	echo "Error de conexión: " . $conn->ultimoError();
}

//Send result to client
$smarty->display('index.tpl');

?>
