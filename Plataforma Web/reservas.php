<?php
	session_start();
	if(!isset($_SESSION['advertencia'])){
	    $_SESSION['advertencia']=" ";
	}
	if(!isset($_SESSION['fastpass_alert'])){
	    $_SESSION['fastpass_alert']=" ";
	}
	if(!isset($_SESSION['fastpass_ride'])){
	    $_SESSION['fastpass_ride']=" ";
	}
	if(!isset($_SESSION['fastpass_time'])){
	    $_SESSION['fastpass_time']=" ";
	}
	if(!isset($_SESSION['fastpass_number'])){
	    $_SESSION['fastpass_number']=" ";
	}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Estación</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="css/mdb.min.css" rel="stylesheet">
    <!-- Your custom styles (optional) -->
    <link href="css/Mystyle.css" rel="stylesheet">
    <!-- SCRIPTS -->
    <!-- JQuery -->
    <script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="js/popper.min.js"></script>
    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="js/mdb.min.js"></script>
    <!--MyScript-->
    <script type="text/javascript" src="js/myJavaScript.js"></script>
    

    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.blue-indigo.min.css"/>
    <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>

    <?php
    	if(!$_SESSION['login']){
   			$_SESSION['mensaje'] = "Debes iniciar sesión para acceder al área de reservas";
    		header("Location: index.php");
		}
		
		$guestID = $_SESSION['guestIdentification']; 

		$station_ip_adress = $_SERVER['REMOTE_ADDR'];
		
		
		require '../configure.php';

		acceso_estacion($station_ip_adress);

		//Reservas de juegos
		function bookRide($guestID,$rideNumber){
			$fastpass_time = NULL;
			$fastpass_name = NULL;
			if (isset($_POST['Ride' . $rideNumber])) {
				$fastpass_time = search_for_fastpass($rideNumber);
				$fastpass_name = fastpass_name($rideNumber);
				if($fastpass_time != NULL){
					$fastpass_time = substr($fastpass_time, 0, -3);
					$_SESSION['fastpass_alert'] = "Fastpass disponible para ".$fastpass_name ." -> ". $fastpass_time;
					$_SESSION['fastpass_time']=$fastpass_time;
      				$_SESSION['fastpass_ride']=$fastpass_name;
      				$_SESSION['fastpass_number']=$rideNumber;  
				}
				else{
					if(!(check_if_waitTime_is_similar($guestID,$rideNumber))){ 
						update_guestQueue($guestID, $rideNumber);
					}
				}
			}
		}

		bookRide($guestID, 1);
		bookRide($guestID, 2);
		bookRide($guestID, 3);
		bookRide($guestID, 4);
		bookRide($guestID, 5);
		bookRide($guestID, 6);
		bookRide($guestID, 7);
		bookRide($guestID, 8);
		bookRide($guestID, 9);
		bookRide($guestID, 10);
		bookRide($guestID, 11);
		bookRide($guestID, 12);

		//Acepto el Fastpass disponible 
		if (isset($_POST['si_quiero'])) { 
				$fastpass_ride=$_SESSION['fastpass_number'];
      			$fastpass_time=$_SESSION['fastpass_time'];
				$go = book_fastpass($guestID, $fastpass_ride, $fastpass_time);
				if($go){
					delete_available_fastpass($fastpass_ride, $fastpass_time);
					add_one_fastpass_guest($guestID);
					fastpass_sales_by_attraction($fastpass_ride);
				}
		}
		//Rechazo el Fastpass disponible
		if (isset($_POST['no_gracias'])) { 
				$rideNumber=$_SESSION['fastpass_number'];
				if(!(check_if_waitTime_is_similar($guestID,$rideNumber))){
					update_guestQueue($guestID, $rideNumber);
      			}
		}
		
		

		//Bajas de juegos
		function unbookRide($guestID,$rideNumber){
			if (isset($_POST['offRide' . $rideNumber])) {
				delete_guestQueue($guestID, $rideNumber);
			}
		}

		unbookRide($guestID, 1);
		unbookRide($guestID, 2);
		unbookRide($guestID, 3);
		unbookRide($guestID, 4);
		unbookRide($guestID, 5);
		unbookRide($guestID, 6);
		unbookRide($guestID, 7);
		unbookRide($guestID, 8);
		unbookRide($guestID, 9);
		unbookRide($guestID, 10);
		unbookRide($guestID, 11);
		unbookRide($guestID, 12);

		//Muestra las filas del usuario
		$row=show_guestQueues($guestID);

		//Muestra los tiempos de espera de las rides
		$river_adventure=show_rideWaitTime(1);
		$revenge_mummy=show_rideWaitTime(2);
		$forbidden_journey=show_rideWaitTime(3);
		$splash_mountain=show_rideWaitTime(4);
		$pirates_caribbean=show_rideWaitTime(5);
		$et_adventure=show_rideWaitTime(6);
		$exp_everest=show_rideWaitTime(7);
		$tower_terror=show_rideWaitTime(8);
		$thunder_mountain=show_rideWaitTime(9);
		$jungle_cruise=show_rideWaitTime(10);
		$rockn_roller=show_rideWaitTime(11);
		$haunted_mansion=show_rideWaitTime(12);

		//Muestra los turnos donde el usuario esta en fila
		$turn=show_guestTurn($guestID);

		//Actualiza la tabla guests con las reservas del usuario
		function update_guestQueue($guestID, $rideNumber) {
			//require '../configure.php';
			$database = "theme_park";
			$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );

			if ($db_found) {

				$SQL = $db_found->prepare("SELECT Q1, Q2, Q3 FROM guests WHERE ID=?");

				if ($SQL) {
					$SQL->bind_param('i', $guestID);
					$SQL->execute();
					$res = $SQL->get_result();

					if ($res->num_rows > 0) {

						$row = $res->fetch_assoc();

						$Q1 = $row['Q1'];
						$Q2 = $row['Q2'];
						$Q3 = $row['Q3'];
						
						if($rideNumber!==$Q1 and $rideNumber!==$Q2 and $rideNumber!==$Q3){
							if(is_null($Q1)){
								$SQL = $db_found->prepare("UPDATE guests SET Q1=? WHERE ID=?");
								$SQL->bind_param('ii', $rideNumber, $guestID);
								$SQL->execute();
							}
							else{
								if (is_null($Q2)) {
									$SQL = $db_found->prepare("UPDATE guests SET Q2=? WHERE ID=?");
									$SQL->bind_param('ii', $rideNumber, $guestID);
									$SQL->execute();
								}
								else{
									if (is_null($Q3)) {
										$SQL = $db_found->prepare("UPDATE guests SET Q3=? WHERE ID=?");
										$SQL->bind_param('ii', $rideNumber, $guestID);
										$SQL->execute();
									}
									else{
										$_SESSION['advertencia'] = "Ya estás en el número máximo de filas posible!";
									}	
								}
							}
						}
						else{
							echo '<script language="javascript">';
							echo 'alert("Atracción ya reservada")';
							echo '</script>';
						}	
					}
					else {
						print "Error - No rows";
					}
					$SQL->close();
					$db_found->close();
				}
			}
		}

		//Actualiza la tabla guest si el usuario se da de baja a un juego y crea fastpass
		function delete_guestQueue($guestID, $rideNumber){
			$inicialValue =NULL;
			$time_for_fastpass = NULL;
			//require '../configure.php';
			$database = "theme_park";
			$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );

			if ($db_found) {

				$SQL = $db_found->prepare("SELECT Q1, Q2, Q3 FROM guests WHERE ID=?");

				if ($SQL) {
					$SQL->bind_param('i', $guestID);
					$SQL->execute();
					$res = $SQL->get_result();

					if ($res->num_rows > 0) {

						$row = $res->fetch_assoc();

						$Q1 = $row['Q1'];
						$Q2 = $row['Q2'];
						$Q3 = $row['Q3'];

						if($Q1=="$rideNumber"){
							//Encontrar la hora del turno a dar de baja
							$SQL = $db_found->prepare("SELECT T1 FROM guests WHERE ID=?");
							$SQL->bind_param('i', $guestID);
							$SQL->execute();
							$result_fastpass = $SQL->get_result();

							if ($result_fastpass->num_rows > 0) {
								$row = $result_fastpass->fetch_assoc();
								$time_for_fastpass = $row['T1'];
							}

							$SQL = $db_found->prepare("UPDATE guests SET Q1=?, T1=? WHERE ID=?");
							$SQL->bind_param('iii',$inicialValue, $inicialValue, $guestID);
							$SQL->execute();
						}
						if($Q2=="$rideNumber"){
							//Encontrar la hora del turno a dar de baja
							$SQL = $db_found->prepare("SELECT T2 FROM guests WHERE ID=?");
							$SQL->bind_param('i', $guestID);
							$SQL->execute();
							$result_fastpass = $SQL->get_result();

							if ($result_fastpass->num_rows > 0) {
								$row = $result_fastpass->fetch_assoc();
								$time_for_fastpass = $row['T2'];
							}

							$SQL = $db_found->prepare("UPDATE guests SET Q2=?, T2=? WHERE ID=?");
							$SQL->bind_param('iii',$inicialValue, $inicialValue, $guestID);
							$SQL->execute();
						}
						if($Q3=="$rideNumber"){
							//Encontrar la hora del turno a dar de baja
							$SQL = $db_found->prepare("SELECT T3 FROM guests WHERE ID=?");
							$SQL->bind_param('i', $guestID);
							$SQL->execute();
							$result_fastpass = $SQL->get_result();

							if ($result_fastpass->num_rows > 0) {
								$row = $result_fastpass->fetch_assoc();
								$time_for_fastpass = $row['T3'];
							}
							$SQL = $db_found->prepare("UPDATE guests SET Q3=?, T3=? WHERE ID=?");
							$SQL->bind_param('iii',$inicialValue, $inicialValue, $guestID);
							$SQL->execute();
						}
					}

					//Crear Fastpass para el turno que se dió de baja
					$SQL = $db_found->prepare("INSERT INTO fastpass (Time_fastpass, Ride_fastpass) VALUES (?, ?)");
					$SQL->bind_param('si',$time_for_fastpass, $rideNumber);
					$SQL->execute();

					//ACA TENGO QUE BORRAR AL USUARIO DE LA FILA VIRTUAL CORRESPONDIENTE

					$SQL->close();
					$db_found->close();

				}	
			}
		}

		//Muestra Tus Filas
		function show_guestQueues($guestID){
			$database = "theme_park";
			$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );

			if ($db_found) {

				$SQL = $db_found->prepare("SELECT Q1, Q2, Q3 FROM guests WHERE ID=?");

				if ($SQL) {
					$SQL->bind_param('i', $guestID);
					$SQL->execute();
					$res = $SQL->get_result();

					if ($res->num_rows > 0) {

						$row = $res->fetch_assoc();

						$Q1 = $row['Q1'];
						$Q2 = $row['Q2'];
						$Q3 = $row['Q3'];
					}
				}
			}
			$SQL->close();
			$db_found->close();
		return $row;		
		}

		//Muestra el tiempo de espera para cada ride
		function show_rideWaitTime($rideID){
			$database = "theme_park";
			$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );

			if ($db_found) {

				$SQL = $db_found->prepare("SELECT WaitTime FROM rides WHERE IDRide=?");

				if ($SQL) {
					$SQL->bind_param('i', $rideID);
					$SQL->execute();
					$res = $SQL->get_result();

					if ($res->num_rows > 0) {

						$row = $res->fetch_assoc();

						$waitTime = $row['WaitTime'];
						if($waitTime<10){
							$short = "0";
							$short .= $waitTime;
							$waitTime = $short;
						}	
					}
				}
			}
			$SQL->close();
			$db_found->close();
		return $waitTime;		
		}

		//Muestra los turnos para cada juego reservado del usuario
		function show_guestTurn($guestID){
			$database = "theme_park";
			$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );

			if ($db_found) {

				$SQL = $db_found->prepare("SELECT Q1, Q2, Q3, T1, T2, T3 FROM guests WHERE ID=?");

				if ($SQL) {
					$SQL->bind_param('i', $guestID);
					$SQL->execute();
					$res = $SQL->get_result();

					if ($res->num_rows > 0) {

						$row = $res->fetch_assoc();

						$Q1 = $row['Q1'];
						$Q2 = $row['Q2'];
						$Q3 = $row['Q3'];

						$T1 = $row['T1'];
						$T2 = $row['T2'];
						$T3 = $row['T3'];

						$parsed_T1 = substr($T1, 0, -3);
						$parsed_T2 = substr($T2, 0, -3);
						$parsed_T3= substr($T3, 0, -3);

						$turn = array($Q1=>$parsed_T1, $Q2=>$parsed_T2, $Q3=>$parsed_T3);
					}
				}
			}
			$SQL->close();
			$db_found->close();
		return $turn;		
		}

		//Busca fastpass para una atracción
		function search_for_fastpass($rideNumber){
			$fastpass_alert = NULL;
			$database = "theme_park";
			$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );

			if ($db_found) {

				$SQL = $db_found->prepare("SELECT Time_fastpass FROM fastpass WHERE Ride_fastpass=? ORDER BY time_fastpass LIMIT 1");
				if ($SQL) {
					$SQL->bind_param('i', $rideNumber);
					$SQL->execute();
					$res = $SQL->get_result();

					if ($res->num_rows > 0) {
						$row = $res->fetch_assoc();
						$fastpass_alert = $row['Time_fastpass'];
					}
				}
			}
			$SQL->close();
			$db_found->close();
		return $fastpass_alert;		
		}


		//Busca el nombre del juego que tiene fastpass disponible
		function fastpass_name($rideNumber){
			$fastpass_ride_name = NULL;
			$database = "theme_park";
			$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );

			if ($db_found) {

				$SQL = $db_found->prepare("SELECT Full_Name FROM rides_table_names WHERE IDRide=?");
				if ($SQL) {
					$SQL->bind_param('i', $rideNumber);
					$SQL->execute();
					$res = $SQL->get_result();

					if ($res->num_rows > 0) {
						$row = $res->fetch_assoc();
						$fastpass_ride_name = $row['Full_Name'];
					}
				}
			}
			$SQL->close();
			$db_found->close();
		return $fastpass_ride_name;	
		}

		//Reserva el fastpass para un usuario actualizando la tabla guests
		function book_fastpass($guestID, $fastpass_ride, $fastpass_time){
			$database = "theme_park";
			$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
			$go = true;

			if ($db_found) {

				$SQL = $db_found->prepare("SELECT Q1, Q2, Q3 FROM guests WHERE ID=?");

				if ($SQL) {
					$SQL->bind_param('i', $guestID);
					$SQL->execute();
					$res = $SQL->get_result();

					if ($res->num_rows > 0) {

						$row = $res->fetch_assoc();

						$Q1 = $row['Q1'];
						$Q2 = $row['Q2'];
						$Q3 = $row['Q3'];
						
						if($fastpass_ride!==$Q1 and $fastpass_ride!==$Q2 and $fastpass_ride!==$Q3){
							if(is_null($Q1)){
								$SQL = $db_found->prepare("UPDATE guests SET Q1=?, T1=? WHERE ID=?");
								$SQL->bind_param('isi', $fastpass_ride, $fastpass_time, $guestID);
								$SQL->execute();
							}
							else{
								if (is_null($Q2)) {
									$SQL = $db_found->prepare("UPDATE guests SET Q2=?, T2=? WHERE ID=?");
									$SQL->bind_param('isi', $fastpass_ride, $fastpass_time, $guestID);
									$SQL->execute();
								}
								else{
									if (is_null($Q3)) {
										$SQL = $db_found->prepare("UPDATE guests SET Q3=?, T3=? WHERE ID=?");
										$SQL->bind_param('isi', $fastpass_ride, $fastpass_time, $guestID);
										$SQL->execute();
									}
									else{
										$_SESSION['advertencia'] = "Ya estás en el número máximo de filas posible!";
										$go = false;

									}	
								}
							}
						}
						else{
							echo '<script language="javascript">';
							echo 'alert("Atracción ya reservada")';
							echo '</script>';
						}	
					}
					else {
						print "Error - No rows";
					}
					$SQL->close();
					$db_found->close();
				}
			}
			return $go;
		}

		//Borrar el fastpass si es aceptado por el usuario
		function delete_available_fastpass($fastpass_ride, $fastpass_time){
			$database = "theme_park";
			$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );

			if ($db_found) {

				$SQL = $db_found->prepare("DELETE FROM fastpass WHERE Ride_fastpass=? AND Time_fastpass=?");

				if ($SQL) {
					$SQL->bind_param('is', $fastpass_ride, $fastpass_time);
					$SQL->execute();
				}
			}
			$SQL->close();
			$db_found->close();
		}

		//Agregar un fastpass numericamente al usuario 
		function add_one_fastpass_guest($guestID){
			$database = "theme_park";
			$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );

			if ($db_found) {

				$SQL = $db_found->prepare("UPDATE guests SET FASTPASS=FASTPASS+1 WHERE ID=?");

				if ($SQL) {
					$SQL->bind_param('i', $guestID);
					$SQL->execute();
				}
			}
			$SQL->close();
			$db_found->close();
		}

		//Revisa que un usuario no se ponga en dos filas con similar tiempo de espera
		function check_if_waitTime_is_similar($guestID,$rideNumber){
			$database = "theme_park";
			$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
			$is_similar = false;

			if ($db_found) {

				$SQL = $db_found->prepare("SELECT WaitTime FROM rides WHERE IDRide=?");
				$SQL->bind_param('i', $rideNumber);
				$SQL->execute();
				$res = $SQL->get_result();

				if ($res->num_rows > 0) {
					$row = $res->fetch_assoc();
					$waitTime_to_book = $row['WaitTime'];
				}

				$SQL = $db_found->prepare("SELECT Q1, Q2, Q3 FROM guests WHERE ID=?");

				if ($SQL) {
					$SQL->bind_param('i', $guestID);
					$SQL->execute();
					$res = $SQL->get_result();

					if ($res->num_rows > 0) {

						$row = $res->fetch_assoc();

						$Q1 = $row['Q1'];
						$Q2 = $row['Q2'];
						$Q3 = $row['Q3'];

						if(!is_null($Q1)){
							$SQL = $db_found->prepare("SELECT WaitTime FROM rides WHERE IDRide=?");
							$SQL->bind_param('i', $Q1);
							$SQL->execute();
							$res = $SQL->get_result();

							if ($res->num_rows > 0) {
								$row = $res->fetch_assoc();
								$waitTime_1 = $row['WaitTime'];
							}
							if($waitTime_1 == $waitTime_to_book || abs($waitTime_1 - $waitTime_to_book)<10 ){
								if( is_null($Q1) || is_null($Q2) || is_null($Q3) ){
									$_SESSION['advertencia'] = "La atracción que quieres reservar tendrá un turno similar a una que ya tienes en Tus Filas, elige otra atracción";
									$is_similar = true;
								}
							}
						}
						if(!is_null($Q2)){
							$SQL = $db_found->prepare("SELECT WaitTime FROM rides WHERE IDRide=?");
							$SQL->bind_param('i', $Q2);
							$SQL->execute();
							$res = $SQL->get_result();

							if ($res->num_rows > 0) {

								$row = $res->fetch_assoc();
								$waitTime_2 = $row['WaitTime'];
							}
							if($waitTime_2 == $waitTime_to_book || abs($waitTime_2 - $waitTime_to_book)<10){
								if( is_null($Q1) || is_null($Q2) || is_null($Q3) ){
									$_SESSION['advertencia'] = "La atracción que quieres reservar tendrá un turno similar a una que ya tienes en Tus Filas, elige otra atracción";
									$is_similar = true;
								}
							}
						}
						if(!is_null($Q3)){
							$SQL = $db_found->prepare("SELECT WaitTime FROM rides WHERE IDRide=?");
							$SQL->bind_param('i', $Q3);
							$SQL->execute();
							$res = $SQL->get_result();

							if ($res->num_rows > 0) {
								$row = $res->fetch_assoc();
								$waitTime_3 = $row['WaitTime'];
							}
							if($waitTime_3 == $waitTime_to_book || abs($waitTime_3 - $waitTime_to_book)<10){
								if( is_null($Q1) || is_null($Q2) || is_null($Q3) ){
									$_SESSION['advertencia'] = "La atracción que quieres reservar tendrá un turno similar a una que ya tienes en Tus Filas, elige otra atracción";
									$is_similar = true;
								}
							}
						}
					}
				}
			}
		return $is_similar;	
		}

		//Agregar un acceso desde una estación con IP fija
		function acceso_estacion($station_ip_adress){
			$database = "theme_park";
			$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );

			if ($db_found) {

				$SQL = $db_found->prepare("UPDATE estaciones SET Booked=Booked+1 WHERE IP=?");

				if ($SQL) {
					$SQL->bind_param('s', $station_ip_adress);
					$SQL->execute();
				}
			}
			$SQL->close();
			$db_found->close();
		}

		//Fastpass que se venden por atracción
		function fastpass_sales_by_attraction($ride_Number){
			$database = "theme_park";
			$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );

			if ($db_found) {

				$SQL = $db_found->prepare("UPDATE rides SET FastpassSales=FastpassSales+1 WHERE IDRide=?");

				if ($SQL) {
					$SQL->bind_param('i', $ride_Number);
					$SQL->execute();
				}
			}
			$SQL->close();
			$db_found->close();
		}


	?>

	<script type="text/javascript">
		var guestQueue1 = "<?PHP echo $row['Q1'] ; ?>";
		var guestQueue2 = "<?PHP echo $row['Q2'] ; ?>";
  		var guestQueue3 = "<?PHP echo $row['Q3'] ; ?>";
	</script>

  </head>

  <body>
    <div id="paginaReservas" class="view">
    	<div class="mask rgba-black-strong"></div>
	    <!--Carousel Tus Filas Wrapper-->
		<div class="container pt-3">
		    <div class="row">
		        <div class="col-md-3">
		            <h1 class="carouselText white-text">Tus Filas</h1>
		        </div>
		        <div class="col-md-6 my-auto">
		        	<div class="carouselText white-text warningmsg text-center">
  						<?php echo $_SESSION['advertencia'];?>
  					</div>
  					<div class="carouselText white-text fastpass_alert_color text-center">
  						<?php 
	  						if($_SESSION['fastpass_alert'] != " "){
	  							echo'
	  								<!-- Modal -->
									<div class="modal fade show" id="Modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
									  	<div class="modal-dialog" role="document">
									    	<div class="modal-content">
									      		<div class="modal-header">
									        		<h5 class="modal-title black-text" id="ModalLabel">
									        		Oportunidad de Fastpass!
									        		</h5>									  
									      		</div>
									      		<div class="modal-body black-text">
									';
									echo $_SESSION["fastpass_alert"];
									echo '
									     		</div>
									      		<div class="modal-footer">
									      			<FORM name="normal" METHOD ="POST" ACTION ="reservas.php" class="m-0 p-0">
										        		<button name="no_gracias" type="submit" class="btn btn-secondary">
										        			No, gracias
										        		</button>
										        	</FORM>
										        	<FORM name="fastpass" METHOD ="POST" ACTION ="reservas.php" class="m-0 p-0">
										        		<button name="si_quiero" type="submit" class="btn btn-primary">
										        			Si, quiero
										        		</button>
									        		</FORM>
									      		</div>
									    	</div>
									  	</div>
									</div>
									<script language="javascript">
										$("#Modal").modal("show")
									</script>
								';
  							}
  						?>	
  					</div>
				</div>
		        <div class="col-md-3 text-md-right my-auto">
		        	<a id="btnCerrarSesion" class="btn btn-primary" href="logout.php">Cerrar Sesión</a>
		        </div>
		    </div>
		</div>
		<div class="carousel multi slide" id="tusFilasCarousel">		 

		    <!--Carousel-->
		    <div class="container pt-0 mt-2">
		        <div class="carousel-inner multi">

		        	<!--First Slide-->
		            <div class="carousel-item active multi">
		                <div class="card-deck">
		                	<!--Card1-->
		                	<div class="col-md-4 hide" id="R1">
				                <div class="card mb-2">
				                    <img class="card-img-top" src="img/rides/jurassic-park.jpg" alt="Card image cap">
				                    <div class="card-body">
				                        <h4 class="card-title">River Adventure</h4>
				                        <p class="card-text">Navega por las instalaciones de Jurassic Park en una aventura inolvidable.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="offRide1" METHOD ="POST" ACTION ="reservas.php" class="m-0 p-0">
					                        		<INPUT class="btn btn-primary" TYPE = "Submit" Name = "offRide1" VALUE = "Salir de fila">
					                        	</FORM>
					                    	</div>
					                    	<i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
			                        		<h4 class="secondary-color white-text card-title col-3 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $turn[1];?></h4>
				                    	</div>
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card1-->

		                	<!--Card2-->
		                	<div class="col-md-4 hide" id="R2">
				                <div class="card mb-2">
				                    <img class="card-img-top" src="img/rides/mummy2.jpg" alt="Card image cap">
				                    <div class="card-body">
				                        <h4 class="card-title">Revenge of the Mummy</h4>
				                        <p class="card-text">Sujétate mientras te mueves hacia delante y hacia atrás para escapar de la momia.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="offRide2" METHOD ="POST" ACTION ="reservas.php" class="m-0 p-0">
					                        		<INPUT class="btn btn-primary" TYPE = "Submit" Name = "offRide2" VALUE = "Salir de fila">
					                        	</FORM>
					                    	</div>
					                    	<i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
			                        		<h4 class="secondary-color white-text card-title col-3 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $turn[2];?></h4>
				                    	</div>
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card2-->

		                	<!--Card3-->
		                	<div class="col-md-4 hide" id="R3">
				                <div class="card mb-2">
				                    <img class="card-img-top" src="img/rides/Harry-Potter.jpg" alt="Card image cap">
				                    <div class="card-body">
				                        <h4 class="card-title">Forbidden Journey</h4>
				                        <p class="card-text">Pasa por las imponentes puertas del castillo, recorre los pasadizos y corredores de Hogwarts.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="offRide3" METHOD ="POST" ACTION ="reservas.php" class="m-0 p-0">
					                        		<INPUT class="btn btn-primary" TYPE = "Submit" Name = "offRide3" VALUE = "Salir de fila">
					                        	</FORM>
					                    	</div>
					                    	<i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
			                        		<h4 class="secondary-color white-text card-title col-3 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $turn[3];?></h4>
				                    	</div>
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card3-->

		                	<!--Card4-->
		                	<div class="col-md-4 hide" id="R4">
				                <div class="card mb-2">
				                    <img class="card-img-top" src="img/rides/splash-mountain.jpg" alt="Card image cap">
				                    <div class="card-body">
				                        <h4 class="card-title">Splash Mountain</h4>
				                        <p class="card-text">Navega por cuevas rocosas, pantanos con enredaderas y terrenos espinosos.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="offRide4" METHOD ="POST" ACTION ="reservas.php" class="m-0 p-0">
					                        		<INPUT class="btn btn-primary" TYPE = "Submit" Name = "offRide4" VALUE = "Salir de fila">
					                        	</FORM>
					                    	</div>
					                    	<i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
			                        		<h4 class="secondary-color white-text card-title col-3 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $turn[4];?></h4>
				                    	</div>				           
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card4-->

		                	<!--Card5-->
		                	<div class="col-md-4 hide" id="R5">
				                <div class="card mb-2">
				                    <img class="card-img-top" src="img/rides/pirates-of-the-caribbean.jpg" alt="Card image cap">
				                    <div class="card-body">
				                        <h4 class="card-title">Pirates of the Caribbean</h4>
				                        <p class="card-text">Pasea navegando junto a un barco con piratas y soldados españoles debatiéndose a duelo.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="offRide5" METHOD ="POST" ACTION ="reservas.php" class="m-0 p-0">
					                        		<INPUT class="btn btn-primary" TYPE = "Submit" Name = "offRide5" VALUE = "Salir de fila">
					                        	</FORM>
					                    	</div>
					                    	<i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
			                        		<h4 class="secondary-color white-text card-title col-3 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $turn[5];?></h4>
				                    	</div>				                      
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card5-->

		                	<!--Card6-->
		                	<div class="col-md-4 hide" id="R6">
				                <div class="card mb-2">
				                    <img class="card-img-top" src="img/rides/ET.jpg" alt="Card image cap">
				                    <div class="card-body">
				                        <h4 class="card-title">E.T. Adventure</h4>
				                        <p class="card-text">Súbete a la bici voladora con E.T. en la canasta delantera y parte en un alucinante viaje.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="offRide6" METHOD ="POST" ACTION ="reservas.php" class="m-0 p-0">
					                        		<INPUT class="btn btn-primary" TYPE = "Submit" Name = "offRide6" VALUE = "Salir de fila">
					                        	</FORM>
					                    	</div>
					                    	<i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
			                        		<h4 class="secondary-color white-text card-title col-3 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $turn[6];?></h4>
				                    	</div>				                        
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card6-->

		                	<!--Card7-->
		                	<div class="col-md-4 hide" id="R7">
				                <div class="card mb-2">
				                    <img class="card-img-top" src="img/rides/exp-everest.jpg" alt="Card image cap">
				                    <div class="card-body">
				                        <h4 class="card-title">Expedition Everest</h4>
				                        <p class="card-text">Explora las montañas del Himalaya y escapa de las garras del Yeti.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="offRide7" METHOD ="POST" ACTION ="reservas.php" class="m-0 p-0">
					                        		<INPUT class="btn btn-primary" TYPE = "Submit" Name = "offRide7" VALUE = "Salir de fila">
					                        	</FORM>
					                    	</div>
					                    	<i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
			                        		<h4 class="secondary-color white-text card-title col-3 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $turn[7];?></h4>
				                    	</div>				                      
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card7-->

		                	<!--Card8-->
		                	<div class="col-md-4 hide" id="R8">
				                <div class="card mb-2">
				                    <img class="card-img-top" src="img/rides/tower-of-terror.jpg" alt="Card image cap">
				                    <div class="card-body">
				                        <h4 class="card-title">Tower of Terror</h4>
				                        <p class="card-text">Sube y baja a bordo de un ascensor embrujado. Estás a punto de entrar en ¡The Twilight Zone! .</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="offRide8" METHOD ="POST" ACTION ="reservas.php" class="m-0 p-0">
					                        		<INPUT class="btn btn-primary" TYPE = "Submit" Name = "offRide8" VALUE = "Salir de fila">
					                        	</FORM>
					                    	</div>
					                    	<i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
			                        		<h4 class="secondary-color white-text card-title col-3 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $turn[8];?></h4>
				                    	</div>				                       
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card8-->

			                <!--Card9-->
		                	<div class="col-md-4 hide" id="R9">
				                <div class="card mb-2">
				                    <img class="card-img-top" src="img/rides/big-thunder-mountain-railroad.jpg" alt="Card image cap">
				                    <div class="card-body">
				                        <h4 class="card-title">Big Thunder Mountain</h4>
				                        <p class="card-text">Atraviesa a toda velocidad un pueblo minero a bordo de un desenfrenado tren minero fugitivo.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="offRide9" METHOD ="POST" ACTION ="reservas.php" class="m-0 p-0">
					                        		<INPUT class="btn btn-primary" TYPE = "Submit" Name = "offRide9" VALUE = "Salir de fila">
					                        	</FORM>
					                    	</div>
					                    	<i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
			                        		<h4 class="secondary-color white-text card-title col-3 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $turn[9];?></h4>
				                    	</div>				                        
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card9-->

		                	<!--Card10-->
		                	<div class="col-md-4 hide" id="R10">
				                <div class="card mb-2">
				                    <img class="card-img-top" src="img/rides/jungle-cruise.jpg" alt="Card image cap">
				                    <div class="card-body">
				                        <h4 class="card-title">Jungle Cruise</h4>
				                        <p class="card-text">Reza no correr la misma suerte que muchos exploradores que se aventuran en la jungla.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="offRide10" METHOD ="POST" ACTION ="reservas.php" class="m-0 p-0">
					                        		<INPUT class="btn btn-primary" TYPE = "Submit" Name = "offRide10" VALUE = "Salir de fila">
					                        	</FORM>
					                    	</div>
					                    	<i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
			                        		<h4 class="secondary-color white-text card-title col-3 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $turn[10];?></h4>
				                    	</div>				                        
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card10-->

		                	<!--Card11-->
		                	<div class="col-md-4 hide" id="R11">
				                <div class="card mb-2">
				                    <img class="card-img-top" src="img/rides/rock-and-roller-coaster.jpg" alt="Card image cap">
				                    <div class="card-body">
				                        <h4 class="card-title">Rock ’n’ Roller Coaster </h4>
				                        <p class="card-text">Viajarás a toda velocidad por la autopista de Los Angeles durante un recorrido lleno de acción.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="offRide11" METHOD ="POST" ACTION ="reservas.php" class="m-0 p-0">
					                        		<INPUT class="btn btn-primary" TYPE = "Submit" Name = "offRide11" VALUE = "Salir de fila">
					                        	</FORM>
					                    	</div>
					                    	<i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
			                        		<h4 class="secondary-color white-text card-title col-3 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $turn[11];?></h4>
				                    	</div>				                        
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card11-->

		                	<!--Card12-->
		                	<div class="col-md-4 hide" id="R12">
				                <div class="card mb-2">
				                    <img class="card-img-top" src="img/rides/haunted-mansion.jpg" alt="Card image cap">
				                    <div class="card-body">
				                        <h4 class="card-title">Haunted Mansion</h4>
				                        <p class="card-text">Recorre una casa embrujada en la que habitan fantasmas, espíritus y sorpresas sobrenaturales.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="offRide12" METHOD ="POST" ACTION ="reservas.php" class="m-0 p-0">
					                        		<INPUT class="btn btn-primary" TYPE = "Submit" Name = "offRide12" VALUE = "Salir de fila">
					                        	</FORM>
					                    	</div>
					                    	<i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
			                        		<h4 class="secondary-color white-text card-title col-3 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $turn[12];?></h4>
				                    	</div>				                        
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card12-->

		                	<!--Card13-->
		                	<div class="col-md-4 hide" id="R13" >
				                <div class="card mb-2">
				                    <img class="card-img-top" src="img/rides/mini-parkmap.jpg" alt="Card image cap">
				                    <div class="card-body">
				                        <h4 class="card-title">Sin Reservar</h4>
				                        <p class="card-text">¿Qué estás esperando? elige una atracción para ponerte en fila.</p>
				                        <INPUT class="btn btn-primary" TYPE = "button" Name = "sinR1" VALUE = "Hacer una reserva" onclick="location.href='#atraccionesCarousel'">
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card13-->

		                	<!--Card14-->
		                	<div class="col-md-4 hide" id="R14">
				                <div class="card mb-2">
				                    <img class="card-img-top" src="img/rides/mini-parkmap.jpg" alt="Card image cap">
				                    <div class="card-body">
				                        <h4 class="card-title">Sin Reservar</h4>
				                        <p class="card-text">¿Qué estás esperando? elige una atracción para ponerte en fila.</p>
				                        <INPUT class="btn btn-primary" TYPE = "button" Name = "sinR2" VALUE = "Hacer una reserva" onclick="location.href='#atraccionesCarousel'">
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card14-->

		                	<!--Card15-->
		                	<div class="col-md-4 hide" id="R15">
				                <div class="card mb-2">
				                    <img class="card-img-top" src="img/rides/mini-parkmap.jpg" alt="Card image cap">
				                    <div class="card-body">
				                        <h4 class="card-title">Sin Reservar</h4>
				                        <p class="card-text">¿Qué estás esperando? elige una atracción para ponerte en fila.</p>
				                        <INPUT class="btn btn-primary" TYPE = "button" Name = "sinR3" VALUE = "Hacer una reserva" onclick="location.href='#atraccionesCarousel'">
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card15-->

		            	</div>
		            </div>
		            <!--/.First Slide-->
		        </div>
		    </div>
		    <!--/.Carousel-->
		</div>
		<!--/.Carousel Tus Filas Wrapper-->


		<!--Carousel Atracciones Wrapper-->
		<div class="container pt-3">
		    <div class="row">
		        <div class="col-md-5">
		            <h1 class="carouselText white-text">Atracciones</h1>
		        </div>
	        	<!--Controls-->
			    <div class="col-md-2 text-md-center my-auto">
	                <a class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored" data-slide="prev" href="#atraccionesCarousel" title="go back">
	                	<i class="material-icons">arrow_back</i>
	                </a>
	                
	                <a class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored" data-slide="next" href="#atraccionesCarousel" title="more">
	                	<i class="material-icons">arrow_forward</i>
	                </a>
			    </div>
			    <!--/.Controls-->
		    </div>
		</div>
		<div class="carousel slide" data-ride="carousel" id="atraccionesCarousel">		 

		    <!--Carousel-->
		    <div class="container pt-0 mt-2">
		        <div class="carousel-inner multi">

		        	<!--First Slide-->
		            <div class="carousel-item active multi">
		                <div class="card-deck">
		                	<!--Card1-->
		                	<div class="col-md-4">
					                <div class="card mb-2" id="card1">
					                    <img class="card-img-top" src="img/rides/jurassic-park.jpg" alt="Card image cap" id="cardimg1">
					                    <div class="card-body">
					                        <h4 class="card-title">River Adventure</h4>
					                        <p class="card-text">Navega por las instalaciones de Jurassic Park en una aventura inolvidable.</p>
					                        <div class="row m-0 p-0">
					                        	<div class="col-7 m-0 p-0">
					                        		<FORM NAME ="Ride1" METHOD ="POST" ACTION ="reservas.php">
					                        			<INPUT class="btn btn-primary" TYPE = "Submit" Name = "Ride1" VALUE = "Entrar a fila" id="cardBtn1">
					                        		</FORM>
					                        	</div>
					                        <i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
					                        <h5 id = "wt1" class="secondary-color white-text card-title col-2 m-0 ml-1 p-1 my-auto shadow rounded text-center" ><?PHP print $river_adventure;?> min</h5>	
					                        </div>		                        					                        
					                    </div>
					                </div>
				            </div>
		                	<!--/.Card1-->

		                	<!--Card2-->
		                	<div class="col-md-4">
				                <div class="card mb-2" id="card2">
				                    <img class="card-img-top" src="img/rides/mummy2.jpg" alt="Card image cap" id="cardimg2">
				                    <div class="card-body">
				                        <h4 class="card-title">Revenge of the Mummy</h4>
				                        <p class="card-text">Sujétate mientras te mueves hacia delante y hacia atrás para escapar de la momia.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="Ride2" METHOD ="POST" ACTION ="reservas.php">
				                        			<INPUT class="btn btn-primary" TYPE = "Submit" Name = "Ride2" VALUE = "Entrar a fila" id="cardBtn2"> 
				                        		</FORM>
				                        	</div>	                       				              			                     
				                        <i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
				                        <h5 id = "wt2" class="secondary-color white-text card-title col-2 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $revenge_mummy;?>min</h5>
				                        </div>
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card2-->

			                <!--Card3-->
		                	<div class="col-md-4">
				                <div class="card mb-2" id="card3">
				                    <img class="card-img-top" src="img/rides/Harry-Potter.jpg" alt="Card image cap" id="cardimg3">
				                    <div class="card-body">
				                        <h4 class="card-title">Forbidden Journey</h4>
				                        <p class="card-text">Pasa por las imponentes puertas del castillo, recorre los pasadizos y corredores de Hogwarts.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">	
						                        <FORM NAME ="Ride3" METHOD ="POST" ACTION ="reservas.php">
						                        	<INPUT class="btn btn-primary" TYPE = "Submit" Name = "Ride3" VALUE = "Entrar a fila" id="cardBtn3">
						                        </FORM>
				                        	</div>
				                        <i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
				                        <h5 id = "wt3" class="secondary-color white-text card-title col-2 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $forbidden_journey;?>min</h5>
				                        </div>				                      
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card3-->
		            	</div>
		            </div>
		            <!--/.First Slide-->

		            <!--Second Slide-->
		            <div class="carousel-item multi">
		                <div class="card-deck">
		                	<!--Card4-->
		                	<div class="col-md-4">
				                <div class="card mb-2" id="card4">
				                    <img class="card-img-top" src="img/rides/splash-mountain.jpg" alt="Card image cap" id="cardimg4">
				                    <div class="card-body">
				                        <h4 class="card-title">Splash Mountain</h4>
				                        <p class="card-text">Navega por cuevas rocosas, pantanos con enredaderas y terrenos espinosos.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="Ride4" METHOD ="POST" ACTION ="reservas.php">
				                        			<INPUT class="btn btn-primary" TYPE = "Submit" Name = "Ride4" VALUE = "Entrar a fila" id="cardBtn4">
				                        		</FORM>
				                        	</div>
				                        	<i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
				                        	<h5 id = "wt4" class="secondary-color white-text card-title col-2 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $splash_mountain;?>min</h5>
				                        </div>			                        				                       
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card4-->

		                	<!--Card5-->
		                	<div class="col-md-4">
				                <div class="card mb-2" id="card5">
				                    <img class="card-img-top" src="img/rides/pirates-of-the-caribbean.jpg" alt="Card image cap" id="cardimg5">
				                    <div class="card-body">
				                        <h4 class="card-title">Pirates of the Caribbean</h4>
				                        <p class="card-text">Pasea navegando junto a un barco con piratas y soldados españoles debatiéndose a duelo.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="Ride5" METHOD ="POST" ACTION ="reservas.php">
				                        			<INPUT class="btn btn-primary" TYPE = "Submit" Name = "Ride5" VALUE = "Entrar a fila" id="cardBtn5">
				                        		</FORM>
				                        	</div>
				                        	<i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
				                        	<h5 id = "wt5" class="secondary-color white-text card-title col-2 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $pirates_caribbean;?>min</h5>
				                        </div>				                        				                       
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card5-->

			                <!--Card6-->
		                	<div class="col-md-4">
				                <div class="card mb-2" id="card6">
				                    <img class="card-img-top" src="img/rides/ET.jpg" alt="Card image cap" id="cardimg6">
				                    <div class="card-body">
				                        <h4 class="card-title">E.T. Adventure</h4>
				                        <p class="card-text">Súbete a la bici voladora con E.T. en la canasta delantera y parte en un alucinante viaje.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
					                        	<FORM NAME ="Ride6" METHOD ="POST" ACTION ="reservas.php">
					                        		<INPUT class="btn btn-primary" TYPE = "Submit" Name = "Ride6" VALUE = "Entrar a fila" id="cardBtn6">
					                        	</FORM>					                    
				                        	</div>
				                        <i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
				                        <h5 id = "wt6" class="secondary-color white-text card-title col-2 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $et_adventure;?>min</h5>
				                        </div>				                        				                       
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card6-->
		            	</div>
		            </div>
		            <!--/.Second Slide-->

		            <!--Therd Slide-->
		            <div class="carousel-item multi">
		                <div class="card-deck">
		                	<!--Card7-->
		                	<div class="col-md-4">
				                <div class="card mb-2" id="card7">
				                    <img class="card-img-top" src="img/rides/exp-everest.jpg" alt="Card image cap" id="cardimg7">
				                    <div class="card-body">
				                        <h4 class="card-title">Expedition Everest</h4>
				                        <p class="card-text">Explora las montañas del Himalaya y escapa de las garras del Yeti.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="Ride7" METHOD ="POST" ACTION ="reservas.php">
				                        			<INPUT class="btn btn-primary" TYPE = "Submit" Name = "Ride7" VALUE = "Entrar a fila" id="cardBtn7">
				                       			</FORM>
				                        	</div>
				                        <i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
				                        <h5 id = "wt7" class="secondary-color white-text card-title col-2 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $exp_everest;?>min</h5>
				                        </div>		                        				                        	                  
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card7-->

		                	<!--Card8-->
		                	<div class="col-md-4">
				                <div class="card mb-2" id="card8">
				                    <img class="card-img-top" src="img/rides/tower-of-terror.jpg" alt="Card image cap" id="cardimg8">
				                    <div class="card-body">
				                        <h4 class="card-title">Tower of Terror</h4>
				                        <p class="card-text">Sube y baja a bordo de un ascensor embrujado. Estás a punto de entrar en ¡The Twilight Zone! .</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="Ride8" METHOD ="POST" ACTION ="reservas.php">
				                        			<INPUT class="btn btn-primary" TYPE = "Submit" Name = "Ride8" VALUE = "Entrar a fila" id="cardBtn8">
				                        		</FORM>
				                        	</div>
				                         <i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
				                        <h5 id = "wt8" class="secondary-color white-text card-title col-2 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $tower_terror;?>min</h5>
				                        </div>   
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card8-->

			                <!--Card9-->
		                	<div class="col-md-4">
				                <div class="card mb-2" id="card9">
				                    <img class="card-img-top" src="img/rides/big-thunder-mountain-railroad.jpg" alt="Card image cap" id="cardimg9">
				                    <div class="card-body">
				                        <h4 class="card-title">Big Thunder Mountain</h4>
				                        <p class="card-text">Atraviesa a toda velocidad un pueblo minero a bordo de un desenfrenado tren minero fugitivo.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="Ride9" METHOD ="POST" ACTION ="reservas.php">
				                        			<INPUT class="btn btn-primary" TYPE = "Submit" Name = "Ride9" VALUE = "Entrar a fila" id="cardBtn9">
				                        		</FORM>
				                        	</div>
				                        <i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
				                        <h5 id = "wt9" class="secondary-color white-text card-title col-2 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $thunder_mountain;?>min</h5>
				                        </div> 
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card9-->
		            	</div>
		            </div>
		            <!--/.Therd Slide-->

		            <!--Fourth Slide-->
		            <div class="carousel-item multi">
		                <div class="card-deck">
		                	<!--Card10-->
		                	<div class="col-md-4">
				                <div class="card mb-2" id="card10">
				                    <img class="card-img-top" src="img/rides/jungle-cruise.jpg" alt="Card image cap" id="cardimg10">
				                    <div class="card-body">
				                        <h4 class="card-title">Jungle Cruise</h4>
				                        <p class="card-text">Reza no correr la misma suerte que muchos exploradores que se aventuran en la jungla.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="Ride10" METHOD ="POST" ACTION ="reservas.php">
				                        			<INPUT class="btn btn-primary" TYPE = "Submit" Name = "Ride10" VALUE = "Entrar a fila" id="cardBtn10">
				                        		</FORM>
				                        	</div>
				                        <i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
				                        <h5 id = "wt10" class="secondary-color white-text card-title col-2 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $jungle_cruise;?>min</h5>
				                        </div> 
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card10-->

		                	<!--Card11-->
		                	<div class="col-md-4">
				                <div class="card mb-2" id="card11">
				                    <img class="card-img-top" src="img/rides/rock-and-roller-coaster.jpg" alt="Card image cap" id="cardimg11">
				                    <div class="card-body">
				                        <h4 class="card-title">Rock ’n’ Roller Coaster</h4>
				                        <p class="card-text">Viajarás a toda velocidad por la autopista de Los Angeles durante un recorrido lleno de acción.</p>
				                        <div class="row m-0 p-0">
				                        	 <div class="col-7 m-0 p-0">
				                        	 	<FORM NAME ="Ride11" METHOD ="POST" ACTION ="reservas.php">
				                        			<INPUT class="btn btn-primary" TYPE = "Submit" Name = "Ride11" VALUE = "Entrar a fila" id="cardBtn11">
				                        		</FORM>
				                        	 </div>
				                        <i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
				                        <h5 id = "wt11" class="secondary-color white-text card-title col-2 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $rockn_roller;?>min</h5>
				                        </div>  
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card11-->

			                <!--Card12-->
		                	<div class="col-md-4" >
				                <div class="card mb-2" id="card12">
				                    <img class="card-img-top" src="img/rides/haunted-mansion.jpg" alt="Card image cap" id="cardimg12">
				                    <div class="card-body">
				                        <h4 class="card-title">Haunted Mansion</h4>
				                        <p class="card-text">Recorre una casa embrujada en la que habitan fantasmas, espíritus y sorpresas sobrenaturales.</p>
				                        <div class="row m-0 p-0">
				                        	<div class="col-7 m-0 p-0">
				                        		<FORM NAME ="Ride12" METHOD ="POST" ACTION ="reservas.php">
				                        			<INPUT class="btn btn-primary" TYPE = "Submit" Name = "Ride12" VALUE = "Entrar a fila" id="cardBtn12">
				                        		</FORM>
				                        	</div>
				                        <i class="fa fa-2x fa-clock-o my-auto p-0 m-0 ml-3 mr-1" aria-hidden="true"></i>
				                        <h5 id = "wt12" class="secondary-color white-text card-title col-2 m-0 ml-1 p-1 my-auto shadow rounded text-center"><?PHP print $haunted_mansion;?>min</h5>
				                        </div>
				                    </div>
				                </div>
				            </div>
		                	<!--/.Card12-->
		            	</div>
		            </div>
		            <!--/.Fourth Slide-->
		        </div>
		    </div>
		    <!--/.Carousel-->
		</div>
		<!--/.Carousel Atracciones Wrapper-->
	</div>
	<script language="javascript">
		var autoPlay="<?php echo $_SESSION['autoPlay'];?>";
		if(autoPlay){
			var audio = new Audio('sounds/magic_spell.ogg');
			audio.play();
		}
	</script>
	<?php 
      $_SESSION['advertencia']=" ";
      $_SESSION['fastpass_alert']=" ";
      //$_SESSION['fastpass_ride']=" ";
      //$_SESSION['fastpass_name']=" ";
      $_SESSION['autoPlay']=false;
    ?>
  </body>
</html>
