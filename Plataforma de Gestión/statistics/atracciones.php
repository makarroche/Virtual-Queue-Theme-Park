<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/roller-coaster.png">
  <link rel="icon" type="image/png" href="../assets/img/roller-coaster.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    Atracciones
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->
  <link href="../assets/css/material-dashboard.css?v=2.1.0" rel="stylesheet" />
  <?php
    session_start(); 
    require '../../configure.php';

    if (isset($_POST['Logout'])) {
        header('Location: logout.php');
    }

    $best_ride=star_ride();
    $best_ride_name=best_ride_name($best_ride);
    $max_queue=longest_queue();
    $max_queue_name=best_ride_name($max_queue);
    $max_wait=longest_waitTime();
    $max_wait_time=best_ride_name($max_wait);

    
    $river_adventure=reservas_atracciones(1);
    $revenge_mummy=reservas_atracciones(2);
    $forbidden_journey=reservas_atracciones(3);
    $splash_mountain=reservas_atracciones(4);
    $pirates_caribbean=reservas_atracciones(5);
    $et_adventure=reservas_atracciones(6);
    $exp_everest=reservas_atracciones(7);
    $tower_terror=reservas_atracciones(8);
    $thunder_mountain=reservas_atracciones(9);
    $jungle_cruise=reservas_atracciones(10);
    $rockn_roller=reservas_atracciones(11);
    $haunted_mansion=reservas_atracciones(12);
    
    $river_adventure_queue = personas_en_cada_fila_virtual(1);
    $revenge_mummy_queue = personas_en_cada_fila_virtual(2);
    $forbidden_journey_queue = personas_en_cada_fila_virtual(3);
    $splash_mountain_queue = personas_en_cada_fila_virtual(4);
    $pirates_caribbean_queue = personas_en_cada_fila_virtual(5);
    $et_adventure_queue = personas_en_cada_fila_virtual(6);
    $exp_everest_queue = personas_en_cada_fila_virtual(7);
    $tower_terror_queue = personas_en_cada_fila_virtual(8);
    $thunder_mountain_queue = personas_en_cada_fila_virtual(9);
    $jungle_cruise_queue = personas_en_cada_fila_virtual(10);
    $rockn_roller_queue = personas_en_cada_fila_virtual(11);
    $haunted_mansion_queue = personas_en_cada_fila_virtual(12);

    
    $river_adventure_waitTime = tiempo_de_espera_de_cada_atraccion(1);
    $revenge_mummy_waitTime = tiempo_de_espera_de_cada_atraccion(2);
    $forbidden_journey_waitTime = tiempo_de_espera_de_cada_atraccion(3);
    $splash_mountain_waitTime = tiempo_de_espera_de_cada_atraccion(4);
    $pirates_caribbean_waitTime = tiempo_de_espera_de_cada_atraccion(5);
    $et_adventure_waitTime = tiempo_de_espera_de_cada_atraccion(6);
    $exp_everest_waitTime = tiempo_de_espera_de_cada_atraccion(7);
    $tower_terror_waitTime = tiempo_de_espera_de_cada_atraccion(8);
    $thunder_mountain_waitTime = tiempo_de_espera_de_cada_atraccion(9);
    $jungle_cruise_waitTime = tiempo_de_espera_de_cada_atraccion(10);
    $rockn_roller_waitTime = tiempo_de_espera_de_cada_atraccion(11);
    $haunted_mansion_waitTime = tiempo_de_espera_de_cada_atraccion(12); 

    $max_1 = max_ride();
    $max_2 = max_queue();
    $max_3 = max_waitTime();



    //Busca la atracción más popular
      function star_ride(){
        $database = "theme_park";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
        if ($db_found) {
          $SQL = $db_found->prepare("SELECT IDRide FROM rides WHERE booked in(select max(booked) from rides)");
          if ($SQL) {
            $SQL->execute();
            $res = $SQL->get_result();
            if ($res->num_rows > 0) {
              $row = $res->fetch_assoc();
                    $best_ride = $row['IDRide'];
            }
          }
        }
        $SQL->close();
        $db_found->close();
      return $best_ride;    
      }

      //Resolución para gráfica Reservas
      function max_ride(){
        $database = "theme_park";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
        if ($db_found) {
          $SQL = $db_found->prepare("SELECT Booked FROM rides WHERE booked in(select max(booked) from rides)");
          if ($SQL) {
            $SQL->execute();
            $res = $SQL->get_result();
            if ($res->num_rows > 0) {
              $row = $res->fetch_assoc();
                    $max_1 = $row['Booked'];
            }
          }
        }
        $SQL->close();
        $db_found->close();
      return $max_1;    
      }

    //Busca el nombre de la atracción más popular
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
      return $fastpass_ride_name; 
      }

    //Busca la cantidad de reservas de todas las atracciones
      function reservas_atracciones($ride_number){
        $database = "theme_park";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
        if ($db_found) {
          $SQL = $db_found->prepare("SELECT Booked FROM rides WHERE IDRide = ?");
          if ($SQL) {
            $SQL->bind_param('i', $ride_number);
            $SQL->execute();
            $res = $SQL->get_result();
            if ($res->num_rows > 0) {
              $row = $res->fetch_assoc();
                    $booked = $row['Booked'];
            }
          }
        }
        $SQL->close();
        $db_found->close();
      return $booked;    
      }

    //Busca la cantidad de personas en cada fila virtual
    function personas_en_cada_fila_virtual($ride_number){
      $database = "theme_park";
      $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
      if ($db_found) {
        $SQL = $db_found->prepare("SELECT Queuing FROM rides WHERE IDRide=?");
        if ($SQL) {
          $SQL->bind_param('i', $ride_number);
          $SQL->execute();
          $res = $SQL->get_result();
          if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
                  $fila_virtual_y_personas = $row['Queuing'];
          }
        }
      }
      $SQL->close();
      $db_found->close();
    return $fila_virtual_y_personas;    
    }

    //Busca el tiempo de espera virtual para cada fila 
    function tiempo_de_espera_de_cada_atraccion($ride_number){
      $database = "theme_park";
      $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
      if ($db_found) {
        $SQL = $db_found->prepare("SELECT WaitTime FROM rides WHERE IDRide=?");
        if ($SQL) {
          $SQL->bind_param('i', $ride_number);
          $SQL->execute();
          $res = $SQL->get_result();
          if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
                  $tiempoDeEspera_atracciones = $row['WaitTime'];
          }
        }
      }
      $SQL->close();
      $db_found->close();
    return $tiempoDeEspera_atracciones;   
    }

    //Capacidad de cada atracción 
    function capacidad_de_cada_atraccion($ride_number){
      $database = "theme_park";
      $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
      if ($db_found) {
        $SQL = $db_found->prepare("SELECT Capacity FROM rides WHERE IDRide=?");
        if ($SQL) {
          $SQL->bind_param('i', $ride_number);
          $SQL->execute();
          $res = $SQL->get_result();
          if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
                  $capacidad_atracciones = $row['Capacity'];
          }
        }
      }
      $SQL->close();
      $db_found->close();
    return $capacidad_atracciones;    
    }

    //Busca el tiempo de salida del proximo carrito
    function next_cart_in($ride_number){
      $database = "theme_park";
      $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
      if ($db_found) {
        $SQL = $db_found->prepare("SELECT NextCartIn FROM rides WHERE IDRide=?");
        if ($SQL) {
          $SQL->bind_param('i', $ride_number);
          $SQL->execute();
          $res = $SQL->get_result();
          if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
                  $next = $row['NextCartIn'];
          }
        }
      }
      $SQL->close();
      $db_found->close();
    return $next;    
    }

    //Busca la fila virtual mas larga
      function longest_queue(){
        $database = "theme_park";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
        if ($db_found) {
          $SQL = $db_found->prepare("SELECT IDRide FROM rides WHERE Queuing in(select max(Queuing) from rides)");
          if ($SQL) {
            $SQL->execute();
            $res = $SQL->get_result();
            if ($res->num_rows > 0) {
              $row = $res->fetch_assoc();
                    $max_queue = $row['IDRide'];
            }
          }
        }
        $SQL->close();
        $db_found->close();
      return $max_queue;    
      }

       //Resolución para gráfica Filas Virtuales
      function max_queue(){
        $database = "theme_park";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
        if ($db_found) {
          $SQL = $db_found->prepare("SELECT Queuing FROM rides WHERE Queuing in(select max(Queuing) from rides)");
          if ($SQL) {
            $SQL->execute();
            $res = $SQL->get_result();
            if ($res->num_rows > 0) {
              $row = $res->fetch_assoc();
                    $max_2 = $row['Queuing'];
            }
          }
        }
        $SQL->close();
        $db_found->close();
      return $max_2;    
      }

       //Tiempo de espera mas alto
      function longest_waitTime(){
        $database = "theme_park";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
        if ($db_found) {
          $SQL = $db_found->prepare("SELECT IDRide FROM rides WHERE WaitTime in(select max(WaitTime) from rides)");
          if ($SQL) {
            $SQL->execute();
            $res = $SQL->get_result();
            if ($res->num_rows > 0) {
              $row = $res->fetch_assoc();
                    $max_wait = $row['IDRide'];
            }
          }
        }
        $SQL->close();
        $db_found->close();
      return $max_wait;    
      }

      //Resolucion para gráfica tiempo de espera
      function max_waitTime(){
        $database = "theme_park";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
        if ($db_found) {
          $SQL = $db_found->prepare("SELECT WaitTime FROM rides WHERE WaitTime in(select max(WaitTime) from rides)");
          if ($SQL) {
            $SQL->execute();
            $res = $SQL->get_result();
            if ($res->num_rows > 0) {
              $row = $res->fetch_assoc();
                    $max_3 = $row['WaitTime'];
            }
          }
        }
        $SQL->close();
        $db_found->close();
      return $max_3;    
      }
    ?>

    <script type="text/javascript">
      var river_adventure = "<?PHP echo $river_adventure ; ?>";
      var revenge_mummy = "<?PHP echo $revenge_mummy ; ?>";
      var forbidden_journey = "<?PHP echo $forbidden_journey ; ?>";
      var splash_mountain = "<?PHP echo $splash_mountain ; ?>";
      var pirates_caribbean = "<?PHP echo $pirates_caribbean ; ?>";
      var et_adventure = "<?PHP echo $et_adventure ; ?>";
      var exp_everest = "<?PHP echo $exp_everest ; ?>";
      var tower_terror = "<?PHP echo $tower_terror ; ?>";
      var thunder_mountain = "<?PHP echo $thunder_mountain ; ?>";
      var jungle_cruise = "<?PHP echo $jungle_cruise ; ?>";
      var rockn_roller = "<?PHP echo $rockn_roller ; ?>";
      var haunted_mansion = "<?PHP echo $haunted_mansion ; ?>";

      var river_adventure_queue ="<?PHP echo $river_adventure_queue ; ?>";
      var revenge_mummy_queue ="<?PHP echo $revenge_mummy_queue ; ?>";
      var forbidden_journey_queue = "<?PHP echo $forbidden_journey_queue ; ?>";
      var splash_mountain_queue = "<?PHP echo $splash_mountain_queue ; ?>";
      var pirates_caribbean_queue ="<?PHP echo $pirates_caribbean_queue ; ?>";
      var et_adventure_queue = "<?PHP echo $et_adventure_queue ; ?>";
      var exp_everest_queue = "<?PHP echo $exp_everest_queue ; ?>";
      var tower_terror_queue = "<?PHP echo $tower_terror_queue ; ?>";
      var thunder_mountain_queue = "<?PHP echo $thunder_mountain_queue ; ?>";
      var jungle_cruise_queue = "<?PHP echo $jungle_cruise_queue ; ?>";
      var rockn_roller_queue = "<?PHP echo $rockn_roller_queue ; ?>";
      var haunted_mansion_queue = "<?PHP echo $haunted_mansion_queue ; ?>";

      var river_adventure_waitTime ="<?PHP echo $river_adventure_waitTime ; ?>";
      var revenge_mummy_waitTime="<?PHP echo $revenge_mummy_waitTime ; ?>";
      var forbidden_journey_waitTime = "<?PHP echo $forbidden_journey_waitTime ; ?>";
      var splash_mountain_waitTime = "<?PHP echo $splash_mountain_waitTime ; ?>";
      var pirates_caribbean_waitTime ="<?PHP echo $pirates_caribbean_waitTime ; ?>";
      var et_adventure_waitTime = "<?PHP echo $et_adventure_waitTime ; ?>";
      var exp_everest_waitTime = "<?PHP echo $exp_everest_waitTime ; ?>";
      var tower_terror_waitTime = "<?PHP echo $tower_terror_waitTime ; ?>";
      var thunder_mountain_waitTime = "<?PHP echo $thunder_mountain_waitTime ; ?>";
      var jungle_cruise_waitTime = "<?PHP echo $jungle_cruise_waitTime ; ?>";
      var rockn_roller_waitTime = "<?PHP echo $rockn_roller_waitTime ; ?>";
      var haunted_mansion_waitTime = "<?PHP echo $haunted_mansion_waitTime ; ?>";

      var max_1 = "<?PHP echo $max_1 ; ?>";
      var max_2 = "<?PHP echo $max_2 ; ?>";
      var max_3 = "<?PHP echo $max_3 ; ?>";


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
          <li class="nav-item active">
            <a class="nav-link" href="./atracciones.php">
              <i class="material-icons">local_play</i>
              <p>Atracciones</p>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./fastpass.php">
              <i class="material-icons">fast_forward</i>
              <p>Fastpass</p>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./estaciones.php">
              <i class="material-icons">touch_app</i>
              <p>Estaciones</p>
            </a>
          </li>
          <li class="nav-item ">
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
		            		<strong>Atracciones - Datos Estadísticos </strong>
		            		<i class="material-icons">poll</i>
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

	      	<!-- Contenido -->
	    	<div class="content black bg-transparent">
		        <div class="container-fluid">
			        <div class="row">
			        	<div class="col-md-11 mx-auto">
			            	<div class="card card-chart">
			            		<div class="card-header card-header-info">
			                		<div class="ct-chart" id="atraccionesBooked"></div>
			                	</div>
			                	<div class="card-body">
			                  		<h4 class="card-title">Reservas por atracción</h4>
			                  		<p class="card-category">
			                   			<span class="text-success"></span>
			                   		</p>
			                	</div>
			                	<div class="card-footer">
			                  		<div class="stats">
			                    		<i class="material-icons">stars</i> La atracción más reservada es<span class="text-info"><strong><?PHP echo ": ". $best_ride_name; ?></strong>
			                  		</div>
			                	</div>
			              	</div>
			            </div>        
			        </div>

		         	<div class="row">
			            <div class="col-md-11 mx-auto">
				            <div class="card card-chart">
				                <div class="card-header card-header-success">
				                	<div class="ct-chart" id="virtualQueueQuantity"></div>
				                </div>
				                <div class="card-body">
				                	<h4 class="card-title">Personas en cada fila virtual</h4>
				                	<p class="card-category">
				                		<span class="text-success"></span>
				                	</p>
				                </div>
				                <div class="card-footer">
				                	<div class="stats">
				                		<i class="material-icons">person</i>La atracción con fila virtual mas larga es <span class="text-success"><strong><?PHP echo ": ".$max_queue_name; ?></strong></span> 
				                	</div>
				                </div>
				            </div>
			            </div>       
		        	</div>

			        <div class="row">
			           	<div class="col-md-11 mx-auto">
			            	<div class="card card-chart">
				                <div class="card-header card-header-primary">
				                	<div class="ct-chart" id="waitTimeAttractions"></div>
				                </div>
				                <div class="card-body">
				                	<h4 class="card-title">Tiempo de espera para cada atracción en minutos</h4>
				                	<p class="card-category">   
				                </div>
				                <div class="card-footer">
				                	<div class="stats">
				                		<i class="material-icons">access_time</i> La atracción con mayor tiempo de espera es <span class="text-primary"><strong><?PHP echo ": ".$max_wait_time ?></strong></span>
				                	</div>
				                </div>
			              	</div>
			            </div>
			        </div>

			        <div class="row row mx-4">
			            <div class="col-lg-6">
			            	<div class="card">
				                <div class="card-header card-header-tabs card-header-danger">
				                	<div class="nav-tabs-navigation">
				                    	<div class="nav-tabs-wrapper">
				                    		<span class="nav-tabs-title"></span>
				                    		<ul class="nav nav-tabs" data-tabs="tabs">
						                        <li class="nav-item">
						                        	<a class="nav-link active" href="#booked" data-toggle="tab">
						                        		<i class="material-icons">restore</i> Reservas
						                        		<div class="ripple-container"></div>
						                        	</a>
						                        </li>
					                        	<li class="nav-item">
					                          		<a class="nav-link" href="#lines" data-toggle="tab">
					                            		<i class="material-icons">swap_calls</i> Filas
					                            		<div class="ripple-container"></div>
					                          		</a>
					                        	</li>
					                        	<li class="nav-item">
					                          		<a class="nav-link" href="#time" data-toggle="tab">
					                            		<i class="material-icons">access_alarm</i> Tiempo de Espera
					                            		<div class="ripple-container"></div>
					                          		</a>
					                        	</li>
				                    		</ul>
				                    	</div>
				                	</div>
				                </div>
				                <div class="card-body">
				                	<div class="tab-content">
				                		<div class="tab-pane active" id="booked">
				                			<table class="table">
					                    		<thead class="text-danger">
					                          		<th>Atracción</th>
					                          		<th>Reservas</th>
					                        	</thead>
					                        	<tbody>
						                        	<tr>
						                        		<td>River Adventure</td>
						                            	<td><?PHP echo $river_adventure; ?></td>
						                          	</tr>
						                          	<tr>
						                            	<td>Revenge of the Mummy</td>
						                            	<td><?PHP echo $revenge_mummy; ?></td>
						                          	</tr>
						                          	<tr>
						                            	<td>Forbidden Journey</td>
						                            	<td><?PHP echo $forbidden_journey; ?></td>
						                          	</tr>
						                          	<tr>
						                           		<td>Splash Mountain</td>
						                            	<td><?PHP echo $splash_mountain; ?></td>
						                          	</tr>
						                           	<tr>
						                            	<td>Pirates of the Caribbean</td>
						                            	<td><?PHP echo $pirates_caribbean; ?></td>
						                          	</tr>
						                           	<tr>
						                            	<td>E.T. Adventure</td>
						                            	<td><?PHP echo $et_adventure; ?></td>
						                          	</tr>
						                           	<tr>
						                            	<td>Expedition Everest</td>
						                            	<td><?PHP echo $exp_everest; ?></td>
						                          	</tr>
						                           	<tr>
						                            	<td>Tower of Terror</td>
						                            	<td><?PHP echo $tower_terror; ?></td>
						                          	</tr>
						                          	<tr>
						                            	<td>Big Thunder Mountain</td>
						                            	<td><?PHP echo $thunder_mountain; ?></td>
						                          	</tr>
						                          	<tr>
						                            	<td>Jungle Cruise</td>
						                            	<td><?PHP echo $jungle_cruise; ?></td>
						                          	</tr>
						                           	<tr>
						                            	<td>Rock ’n’ Roller Coaster</td>
						                            	<td><?PHP echo $rockn_roller; ?></td>
						                          	</tr>
						                           	<tr>
						                            	<td>Haunted Mansion</td>
						                            	<td><?PHP echo $haunted_mansion; ?></td>
						                          	</tr>
					                        	</tbody>
				                      		</table>
				                    	</div>
				                    	<div class="tab-pane" id="lines">
				                    		<table class="table">
				                    		<thead class="text-danger">
				                          		<th>Atracción</th>
				                          		<th>Personas en Fila</th>
				                        	</thead>
				                        	<tbody>
				                          		<tr>
				                            		<td>River Adventure</td>
				                            		<td><?PHP echo $river_adventure_queue; ?></td>
				                          		</tr>
				                          		<tr>
				                            		<td>Revenge of the Mummy</td>
				                            		<td><?PHP echo $revenge_mummy_queue; ?></td>
				                          		</tr>
				                          		<tr>
				                            		<td>Forbidden Journey</td>
				                            		<td><?PHP echo $forbidden_journey_queue; ?></td>
				                          		</tr>
				                          		<tr>
				                            		<td>Splash Mountain</td>
				                            		<td><?PHP echo $splash_mountain_queue; ?></td>
				                          		</tr>
				                           		<tr>
				                            		<td>Pirates of the Caribbean</td>
				                            		<td><?PHP echo $pirates_caribbean_queue; ?></td>
				                          		</tr>
				                           		<tr>
				                           			<td>E.T. Adventure</td>
				                            		<td><?PHP echo $et_adventure_queue; ?></td>
				                          		</tr>
				                           		<tr>
				                            		<td>Expedition Everest</td>
				                            		<td><?PHP echo $exp_everest_queue; ?></td>
				                          		</tr>
				                           		<tr>
				                            		<td>Tower of Terror</td>
				                            		<td><?PHP echo $tower_terror_queue; ?></td>
				                          		</tr>
				                          		<tr>
				                            		<td>Big Thunder Mountain</td>
				                            		<td><?PHP echo $thunder_mountain_queue; ?></td>
				                          		</tr>
				                          		<tr>
				                            		<td>Jungle Cruise</td>
				                            		<td><?PHP echo $jungle_cruise_queue; ?></td>
				                          		</tr>
				                           		<tr>
				                            		<td>Rock ’n’ Roller Coaster</td>
				                            		<td><?PHP echo $rockn_roller_queue; ?></td>
				                          		</tr>
				                           		<tr>
				                            		<td>Haunted Mansion</td>
				                            		<td><?PHP echo $haunted_mansion_queue; ?></td>
				                          		</tr>
				                          	</tbody>
				                      		</table>
				                    	</div>
				                    	<div class="tab-pane" id="time">
				                    	<table class="table">
				                           <thead class="text-danger">
				                            <th>Atracción</th>
				                            <th>Tiempo de Espera (minutos)</th>
				                          </thead>
				                          <tbody>
				                            <tr>
				                              <td>River Adventure</td>
				                              <td><?PHP echo $river_adventure_waitTime; ?></td>
				                            </tr>
				                            <tr>
				                              <td>Revenge of the Mummy</td>
				                              <td><?PHP echo $revenge_mummy_waitTime; ?></td>
				                            </tr>
				                            <tr>
				                              <td>Forbidden Journey</td>
				                              <td><?PHP echo $forbidden_journey_waitTime; ?></td>
				                            </tr>
				                            <tr>
				                              <td>Splash Mountain</td>
				                              <td><?PHP echo $splash_mountain_waitTime; ?></td>
				                            </tr>
				                             <tr>
				                              <td>Pirates of the Caribbean</td>
				                              <td><?PHP echo $pirates_caribbean_waitTime; ?></td>
				                            </tr>
				                             <tr>
				                              <td>E.T. Adventure</td>
				                              <td><?PHP echo $et_adventure_waitTime; ?></td>
				                            </tr>
				                             <tr>
				                              <td>Expedition Everest</td>
				                              <td><?PHP echo $exp_everest_waitTime; ?></td>
				                            </tr>
				                             <tr>
				                              <td>Tower of Terror</td>
				                              <td><?PHP echo $tower_terror_waitTime; ?></td>
				                            </tr>
				                            <tr>
				                              <td>Big Thunder Mountain</td>
				                              <td><?PHP echo $thunder_mountain_waitTime; ?></td>
				                            </tr>
				                            <tr>
				                              <td>Jungle Cruise</td>
				                              <td><?PHP echo $jungle_cruise_waitTime; ?></td>
				                            </tr>
				                             <tr>
				                              <td>Rock ’n’ Roller Coaster</td>
				                              <td><?PHP echo $rockn_roller_waitTime; ?></td>
				                            </tr>
				                             <tr>
				                              <td>Haunted Mansion</td>
				                              <td><?PHP echo $haunted_mansion_waitTime; ?></td>
				                            </tr>
				                            </tbody>
				                           </table>
				                    	</div>
				                    </div>
				                </div>
				            </div>
				        </div>

			            <div class="col-lg-6">
			                <div class="card">
			                	<div class="card-header card-header-danger">
			                		<h4 class="card-title">Próximas Salidas</h4>
			                    	<p class="card-category"></p>
			                  	</div>
			                	<div class="card-body table-responsive">
			                  		<table class="table table-hover">
			                    		<thead class="text-danger">
			                      			<th>Atracción</th>
			                      			<th>Capacidad</th>
			                      			<th>Próximo Carrito</th>
			                    		</thead>
			                    		<tbody>
			                      			<tr>
			                        			<td>River Adventure</td>
			                        			<td>
			                        				<strong>
			                        					<?PHP $capacity=capacidad_de_cada_atraccion(1); echo $capacity. " personas"; ?>
			                        				</strong>
			                        			</td>
			                        			<td>
			                        				Saliendo en..
			                        				<strong>
			                        					<span class="text-success">
			                        						<?PHP $next_cart_in=next_cart_in(1); echo $next_cart_in. "mins"; ?>
			                        					</span>
			                        				</strong>
			                        			</td>
			                      			</tr>
			                      			<tr>
			                        			<td>Revenge of the Mummy</td>
			                          			<td>
			                          				<strong>
			                          					<?PHP $capacity=capacidad_de_cada_atraccion(2); echo $capacity. " personas"; ?>			                          					
			                          				</strong>
			                          			</td>
			                         			<td>
			                         				Saliendo en..
			                         				<strong>
			                         					<span class="text-success"><?PHP $next_cart_in=next_cart_in(2); echo $next_cart_in. "mins"; ?>
			                         					</span>
			                         				</strong>
			                         			</td>
			                      			</tr>
			                      			<tr>
			                        			<td>Forbidden Journey</td>
			                          			<td>
			                          				<strong>
			                          					<?PHP $capacity=capacidad_de_cada_atraccion(3); echo $capacity. " personas"; ?>
			                          				</strong>
			                          			</td>
			                         			<td>
			                         				Saliendo en..
			                         				<strong>
			                         					<span class="text-success"><?PHP $next_cart_in=next_cart_in(3); echo $next_cart_in. "mins"; ?>
			                         					</span>
			                         				</strong>
			                         			</td>
			                      			</tr>
			                      			<tr>
			                        			<td>Splash Mountain</td>
			                          			<td>
			                          				<strong>
			                          					<?PHP $capacity=capacidad_de_cada_atraccion(4); echo $capacity. " personas"; ?>
			                          				</strong>
			                          			</td>
			                         			<td>
			                         				Saliendo en..
			                         				<strong>
			                         					<span class="text-success">
			                         						<?PHP $next_cart_in=next_cart_in(4); echo $next_cart_in. "mins"; ?>
			                         					</span>
			                         				</strong>
			                         			</td>
			                      			</tr>
			                      			<tr>
			                        			<td>Pirates of the Caribbean</td>
			                          			<td>
			                          				<strong>
			                          					<?PHP $capacity=capacidad_de_cada_atraccion(5); echo $capacity. " personas"; ?>
			                          				</strong>
			                          			</td>
			                         			<td>
			                         				Saliendo en..
			                         				<strong>
			                         					<span class="text-success">
			                         						<?PHP $next_cart_in=next_cart_in(5); echo $next_cart_in. "mins"; ?>
			                         					</span>
			                         				</strong>
			                         			</td>
			                      			</tr>
			                      			<tr>
			                        			<td>E.T Adventure</td>
			                          			<td>
			                          				<strong>
			                          					<?PHP $capacity=capacidad_de_cada_atraccion(6); echo $capacity. " personas"; ?>
			                          				</strong>
			                          			</td>
			                         			<td>
			                         				Saliendo en..
			                         				<strong>
			                         					<span class="text-success">
			                         						<?PHP $next_cart_in=next_cart_in(6); echo $next_cart_in. "mins"; ?>
			                         					</span>
			                         				</strong>
			                         			</td>
			                      			</tr>
			                      			<tr>
			                        			<td>Expedition Everest</td>
			                          			<td>
			                          				<strong>
			                          					<?PHP $capacity=capacidad_de_cada_atraccion(7); echo $capacity. " personas"; ?>
			                          				</strong>
			                          			</td>
			                         			<td>
			                         				Saliendo en..
			                         				<strong>
			                         					<span class="text-success"><?PHP $next_cart_in=next_cart_in(7); echo $next_cart_in. "mins"; ?>
			                         					</span>
			                         				</strong>
			                         			</td>
			                      			</tr>
			                      			<tr>
			                        			<td>Tower of Terror</td>
			                          			<td>
			                          				<strong>
			                          					<?PHP $capacity=capacidad_de_cada_atraccion(8); echo $capacity. " personas"; ?>
			                          					</strong>
			                          				</td>
			                         			<td>
			                         				Saliendo en..
			                         				<strong>
			                         					<span class="text-success">
			                         						<?PHP $next_cart_in=next_cart_in(8); echo $next_cart_in. "mins"; ?>
			                         					</span>
			                         				</strong>
			                         			</td>
			                      			</tr>
			                      			<tr>
			                        			<td>Big Thunder Mountain</td>
			                          			<td>
			                          				<strong>
			                          					<?PHP $capacity=capacidad_de_cada_atraccion(9); echo $capacity. " personas"; ?>		
			                          					</strong>
			                          				</td>
			                         			<td>
			                         				Saliendo en..
			                         				<strong>
			                         					<span class="text-success">
			                         						<?PHP $next_cart_in=next_cart_in(9); echo $next_cart_in. "mins"; ?>
			                         					</span>
			                         				</strong>
			                         			</td>
			                      			</tr>
			                      			<tr>
			                        			<td>Jungle Cruise</td>
			                          			<td>
			                          				<strong>
			                          					<?PHP $capacity=capacidad_de_cada_atraccion(10); echo $capacity. " personas"; ?>
			                          					</strong>
			                          				</td>
			                        			<td>
			                        				Saliendo en..
			                        				<strong>
			                        					<span class="text-success"><?PHP $next_cart_in=next_cart_in(10); echo $next_cart_in. "mins"; ?>
			                        					</span>
			                        				</strong>
			                        			</td>
			                      			</tr>
			                      			<tr>
			                        			<td>Rock 'n' Roller Coaster</td>
			                          			<td>
			                          				<strong>
			                          					<?PHP $capacity=capacidad_de_cada_atraccion(11); echo $capacity. " personas"; ?>
			                          					</strong>
			                          				</td>
			                        			<td>
			                        				Saliendo en..
			                        				<strong>
			                        					<span class="text-success"><?PHP $next_cart_in=next_cart_in(11); echo $next_cart_in. "mins"; ?>
			                        					</span>
			                        				</strong>
			                        			</td>
			                      			</tr>
			                      			<tr>
			                        			<td>Haunted Mansion</td>
			                          			<td>
			                          				<strong>
			                          					<?PHP $capacity=capacidad_de_cada_atraccion(12); echo $capacity. " personas"; ?>
			                          				</strong>
			                          			</td>
			                       				<td>
			                       					Saliendo en..
			                       					<strong>
			                       						<span class="text-success">
			                       							<?PHP $next_cart_in=next_cart_in(12); echo $next_cart_in. "mins"; ?>
	                       								</span>
	                       							</strong>	
			                       				</td>
			                      			</tr>
			                    		</tbody>
			                  		</table>
			                	</div> 
			              	</div>
			            </div>
			        </div>
		        </div>
		    </div>
          	<!-- End Contenido -->

          	<!-- Footer -->
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
	    	<!-- End Footer-->
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
  <script src="../assets/js/material-atracciones.js" type="text/javascript"></script>
  <script>
    $(document).ready(function() {
      // Javascript method's body can be found in assets/js/demos.js
      md.initDashboardPageCharts();

    });
  </script>
</body>

</html>