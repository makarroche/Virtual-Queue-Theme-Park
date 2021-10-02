<?php
    session_start(); 
    require '../../configure.php';

    if (isset($_POST['Logout'])) {
        header('Location: logout.php');
    }

    $active_bands=brazaletes_activos();
    
    //Cuenta la cantidad de brazaletes activos
	function brazaletes_activos(){
		$database = "theme_park";
		$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
		if ($db_found) {
			$SQL = $db_found->prepare("SELECT COUNT(*) FROM guests");
			if ($SQL) {
				$SQL->execute();
				$res = $SQL->get_result();
				if ($res->num_rows > 0) {
					$row = $res->fetch_assoc();
          			$active_bands = $row['COUNT(*)'];
				}
			}
		}
		$SQL->close();
		$db_found->close();
	return $active_bands;		
	}

	//Muestra el ID de cada usuario
    function mostrar_guest_ids(){
      $database = "theme_park";
      $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
      if ($db_found) {
        $SQL = $db_found->prepare("SELECT ID FROM guests");
        if ($SQL) {
          $SQL->execute();
          $res = $SQL->get_result();
          if ($res->num_rows > 0) {
            while($row = $res->fetch_assoc()){
                echo '<a class="dropdown-item" onclick= "changeBandsInfo('. $row['ID'] . "," . best_ride_name(check_data(band_info($row['ID'])['Q1'])) . "," . best_ride_name(check_data(band_info($row['ID'])['Q2'])) . "," .  best_ride_name(check_data(band_info($row['ID'])['Q3'])) . "," .  check_data(band_info($row['ID'])['T1']) . "," .  check_data(band_info($row['ID'])['T2']) . "," .  check_data(band_info($row['ID'])['T3']) . ')" >';
                echo $row['ID'];
                echo '</a>';
                echo '</script>';
            }      
          }
        }
      }
      $SQL->close();
      $db_found->close();
    }

    //Datos de cada brazalete
	function band_info($guestID){
		$database = "theme_park";
		$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
		if ($db_found) {
			$SQL = $db_found->prepare("SELECT Q1, Q2 ,Q3 ,T1 ,T2 ,T3 FROM guests WHERE ID=?");
			if ($SQL) {
				$SQL->bind_param('i', $guestID);
				$SQL->execute();
				$res = $SQL->get_result();
				if ($res->num_rows > 0) {
					$row = $res->fetch_assoc();
          			$bands_info = $row;
				}
			}
		}
		$SQL->close();
		$db_found->close();

	return $bands_info;		
	}

	function check_data($data){
		if(is_null($data)){
			return "'N/A'";
		}
		else{
			if(is_numeric($data)){
			return $data;
			}
			else {
				$time=substr($data, 0, -3);
				return "'$time'";
			}
		}
	}

	//Busca el nombre de la atracción
      function best_ride_name($rideNumber){
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

        if(is_null($fastpass_ride_name)){
        	return "'Sin reservar'";
        }
        else{
      		return "'$fastpass_ride_name'";
      	} 
      }
?>

<!DOCTYPE html>
<html lang="en">
	<head>
	  	<meta charset="utf-8" />
	  	<link rel="apple-touch-icon" sizes="76x76" href="../assets/img/roller-coaster.png">
	  	<link rel="icon" type="image/png" href="../assets/img/roller-coaster.png">
	  	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	  	<title>
	    	Brazaletes
	  	</title>
	  	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
	  	<!--     Fonts and icons     -->
	  	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
	  	<!--link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css"-->
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
	  	<!-- CSS Files -->
	  	<link href="../assets/css/material-dashboard.css?v=2.1.0" rel="stylesheet" />

	    <script type="text/javascript">
	      	   	var active_bands=<?PHP echo $active_bands; ?>; 
	  	</script>
	</head>

	<body class="">
	  	<div class="wrapper ">
	    	<div class="sidebar" data-color="azure" data-background-color="black" data-image="../assets/img/960x480-universal-orlando-672x372.png">
	      		<div class="sidebar-wrapper">
	        		<ul class="nav">
	         			<li class="nav-item">
	            			<a class="nav-link" href="./dashboard.php">
	              				<i class="material-icons">dashboard</i>
	              				<p>Dashboard</p>
	            			</a>
	          			</li>
	          			<li class="nav-item">
	            			<a class="nav-link" href="./atracciones.php">
	              				<i class="material-icons">local_play</i>
	              				<p>Atracciones</p>
	            			</a>
	          			</li>
	          			<li class="nav-item">
	            			<a class="nav-link" href="./fastpass.php">
	             				<i class="material-icons">fast_forward</i>
	              				<p>Fastpass</p>
	            			</a>
	          			</li>
	          			<li class="nav-item">
	            			<a class="nav-link" href="./estaciones.php">
	              				<i class="material-icons">touch_app</i>
	              				<p>Estaciones</p>
	            			</a>
	          			</li>
	          			<li class="nav-item active">
	            			<a class="nav-link" href="./brazaletes.php">
	              				<i class="material-icons">watch</i>
	              				<p>Brazaletes</p>
	            			</a>
	          			</li>
	        		</ul>
	      		</div>
	    	</div>
	    	<div class="main-panel">
	      		<!-- Navbar -->
	      		<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
	        		<div class="container-fluid">
		          		<div class="navbar-wrapper">
		            		<a class="navbar-brand">
		            			<strong>
		            				Brazaletes - Datos Estadísticos 
		            			</strong>
		            			<i class="material-icons">
		            				poll
		            			</i>
		            		</a>
		          		</div>
			          	<button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
			            	<span class="sr-only">Toggle navigation</span>
			            	<span class="navbar-toggler-icon icon-bar"></span>
			            	<span class="navbar-toggler-icon icon-bar"></span>
			            	<span class="navbar-toggler-icon icon-bar"></span>
			          	</button>
		          		<div class="collapse navbar-collapse justify-content-end">
		            	<ul class="navbar-nav">
		              		<li class="nav-item-info">
			                  <strong><span class="text-secondary"><?php echo $_SESSION['firstName']. ' ' . $_SESSION['lastName']. ' ' ?>
			                  </span></strong>
			                </li>
			                <li class="nav-item-info">
			                	<i class="material-icons">person</i>
			                	<p class="d-lg-none d-md-block">
			                  	Account
			                	</p>
			              	</li>
			              <li class="nav-item-info">
			                    <FORM name="normal" METHOD ="POST" ACTION ="dashboard.php" class="m-0 p-0">
			                      <button type="submit" class="btn btn-link text-info" name = "Logout">Cerrar Sesión</button>
			                    </FORM>
			              </li>
		            	</ul>
		          		</div>
	        		</div>
	      		</nav>
	      		<!-- End Navbar -->

	    		<div class="content black bg-transparent">
		        	<div class="container-fluid">
		          		<div class="row ml-3">
		          			<div class="col-md-5">
				              	<div class="card card-chart">
				                	<div class="card-header card-header">
				                  		<div class="ct-chart" id="estaciones_pie"></div>
				                	</div>
				                	<div class="card-body">
				                  		<h4 class="card-title">Brazaletes Activos</h4>
				                  		<p class="card-category">
				                    	<span class="text"></span>
				                	</div>
				                	<div class="card-footer">
				                  		<div class="stats">
				                    		<i class="material-icons">notifications_active</i>
				                    		Un total de : &nbsp<span class="text-info" id="informacion"><strong>
				                    		<?php echo $active_bands ?></span></strong>&nbspbrazaletes activos en 10
				                  		</div>
				                	</div>
				              	</div>
		            		</div>
		            		<div class="col-md-5">
		          				<div class="card card-chart">
				                	<div class="card-header card-header">
				                  		<!-- Split button -->
				                    	<div class="btn-group">
				                      		<button type="button" class="btn btn-warning">Información Brazaletes</button>
				                      		<button type="button" class="btn btn-warning dropdown-toggle px-3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				                        		<span class="sr-only">Toggle Dropdown</span>
				                      		</button>
				                      		<div class="dropdown-menu">
				                        		<h6 class="dropdown-header" ><strong>Tags RFID</strong>                   <i class="material-icons">watch</i></h6>
				                        		<div class="dropdown-divider"></div>
				                        		<?PHP mostrar_guest_ids(); ?> 
				                      		</div>
				                    	</div>
				                	</div>
				                	<div class="card-body">
				                  		<h4 class="card-title">Atracciones donde está en Fila</h4>
				                  		<p class="card-category" id="amp">
				                    		<span class="text-warning"></span>
				                    		<i class="fas fa-arrow-circle-up"></i>
				                    		Seleccionar un TAG para ver los juegos donde el brazalete tiene turno asignado
				                    	</p>
				                	</div>
				                	<div class="card-footer">
				                  		<div class="stats">
				                    		<i class="material-icons">clear_all</i> En fila
				                  		</div>
				                	</div>
				              	</div>
		          			</div>
		          		</div>
		        	</div>	    		
		    	</div>

		    	<footer class="footer">
		        	<div class="container-fluid">
		           		<nav class="float-left">
		            		<ul>
		              			<li>
		                			<a href="">
		                  				<script>document.write('Last Update: '+new Date());</script>
		                			</a>
		              			</li>
		            		</ul>
		          		</nav>
		          		<div class="copyright float-right">
		            		<script>
		              			document.write(new Date().getFullYear())
		            		</script>,  Sistema Gestor de Filas Virtuales <i class="material-icons">poll</i>
		          		</div>
		        	</div>
	    		</footer>
			</div>
		</div>
	  	<!--   Core JS Files   -->
	  	<script src="../assets/js/core/jquery.min.js" type="text/javascript"></script>
	  	<script src="../assets/js/core/popper.min.js" type="text/javascript"></script>
	  	<script src="../assets/js/core/bootstrap-material-design.min.js" type="text/javascript"></script>
	  	<script src="../assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
	  	<!--  Google Maps Plugin    -->
	  	<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
	  	<!-- Chartist JS -->
	  	<script src="../assets/js/plugins/chartist.min.js"></script>
	  	<!--  Notifications Plugin    -->
	  	<script src="../assets/js/plugins/bootstrap-notify.js"></script>
	 	<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
	  	<script src="../assets/js/material-brazaletes.js" type="text/javascript"></script>
	  	<script>
	    	$(document).ready(function() {
	      		// Javascript method's body can be found in assets/js/demos.js
	      		md.initDashboardPageCharts();
	      });
      </script>
  </body>
</html>