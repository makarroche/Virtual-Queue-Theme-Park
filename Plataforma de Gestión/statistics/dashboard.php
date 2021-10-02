<?php 
  session_start(); 
  require '../../configure.php';

  if (!isset($_SESSION['logged']) OR !$_SESSION['logged']) {
    header('Location: login.php');
  }

  if (isset($_POST['Logout'])) {
        header('Location: logout.php');
    }

  //Datos que resaltan Atracciones
	$best_ride=star_ride();
	$best_ride_name=best_ride_name($best_ride);
  $max_queue=longest_queue();
  $max_queue_name=best_ride_name($max_queue);
  $max_wait=longest_waitTime();
  $reservas_total=reservas_total();

  //Datos que resaltan Fastpass
  $amount_fastpass=cantidad_de_fastpass();
  $amount_booked_fatpass=fastpass_vendidos();
  $max_fp_sol=max_att_fastpass_sold();
  $max_fp_sol_name=best_ride_name($max_fp_sol);
  $max_att_fp=max_att_fastpass();
  $max_att_fp_name=best_ride_name($max_att_fp);

  //Datos que resaltan Estaciones
  $busiest_station=busy_station();
  $lousy_station=lousy_station();
  $busiest_station_ip=$busiest_station['IP'];
  $busiest_station_booked=$busiest_station['Booked'];

  //Datos que resaltan Brazaletes
  $active_bands=brazaletes_activos();

  //Gráficas
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

  $max_1 = max_ride();

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

      //Tiempo de espera mas alto
      function longest_waitTime(){
        $database = "theme_park";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
        if ($db_found) {
          $SQL = $db_found->prepare("SELECT WaitTime FROM rides WHERE WaitTime in(select max(WaitTime) from rides)");
          if ($SQL) {
            $SQL->execute();
            $res = $SQL->get_result();
            if ($res->num_rows > 0) {
              $row = $res->fetch_assoc();
                    $max_wait = $row['WaitTime'];
            }
          }
        }
        $SQL->close();
        $db_found->close();
      return $max_wait;    
      }

      //Cantidad de reservas en total
      function reservas_total(){
        $database = "theme_park";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
        if ($db_found) {
          $SQL = $db_found->prepare("SELECT SUM(Booked) FROM rides");
          if ($SQL) {
            $SQL->execute();
            $res = $SQL->get_result();
            if ($res->num_rows > 0) {
              $row = $res->fetch_assoc();
                    $reservas_total= $row['SUM(Booked)'];
            }
          }
        }
        $SQL->close();
        $db_found->close();
      return $reservas_total;    
      }

	//Cuenta la cantidad de fastpass disponibles para reserva
	function cantidad_de_fastpass(){
		$database = "theme_park";
		$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
		if ($db_found) {
			$SQL = $db_found->prepare("SELECT COUNT(*) FROM fastpass");
			if ($SQL) {
				$SQL->execute();
				$res = $SQL->get_result();
				if ($res->num_rows > 0) {
					$row = $res->fetch_assoc();
          			$amount_fastpass = $row['COUNT(*)'];
				}
			}
		}
		$SQL->close();
		$db_found->close();
	return $amount_fastpass;		
	}

  //Atracción con mayor fastpass vendidos
      function max_att_fastpass_sold(){
        $database = "theme_park";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
        if ($db_found) {
          $SQL = $db_found->prepare("SELECT IDRide FROM rides WHERE FastpassSales in(select max(FastpassSales) from rides)");
          if ($SQL) {
            $SQL->execute();
            $res = $SQL->get_result();
            if ($res->num_rows > 0) {
              $row = $res->fetch_assoc();
                    $sold_fastpass = $row['IDRide'];
            }
          }
        }
        $SQL->close();
        $db_found->close();
      return $sold_fastpass;    
      }

    //Atracción con mayor fastpass disponible
    function max_att_fastpass(){
      $database = "theme_park";
      $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
      if ($db_found) {
        $SQL = $db_found->prepare("SELECT Ride_fastpass, COUNT(*) FROM fastpass GROUP BY Ride_fastpass ORDER BY COUNT(*) DESC");
        if ($SQL) {
          $SQL->execute();
          $res = $SQL->get_result();
          if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
                  $av_fastpass = $row['Ride_fastpass'];
          }
        }
      }
      $SQL->close();
      $db_found->close();
    return $av_fastpass;    
    }

	//Cuenta la cantidad de fastpass vendidos
	function fastpass_vendidos(){
		$database = "theme_park";
		$db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
		if ($db_found) {
			$SQL = $db_found->prepare("SELECT SUM(FASTPASS) FROM guests");
			if ($SQL) {
				$SQL->execute();
				$res = $SQL->get_result();
				if ($res->num_rows > 0) {
					$row = $res->fetch_assoc();
          			$amount_booked_fatpass = $row['SUM(FASTPASS)'];
				}
			}
		}
		$SQL->close();
		$db_found->close();
	return $amount_booked_fatpass;		
	}


  //Estación más ocupada en el momento
  function busy_station(){
    $database = "theme_park";
    $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
    if ($db_found) {
      $SQL = $db_found->prepare("SELECT IP,Booked FROM estaciones WHERE Booked in(select max(Booked) from estaciones WHERE IP != '127.0.0.1')");
      if ($SQL) {
        $SQL->execute();
        $res = $SQL->get_result();
        if ($res->num_rows > 0) {
          $row = $res->fetch_assoc();
                $busy_station = $row;
        }
      }
    }
    $SQL->close();
    $db_found->close();
  return $busy_station;    
  }


  //Estación menos utilizada en el momento
  function lousy_station(){
    $database = "theme_park";
    $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
    if ($db_found) {
      $SQL = $db_found->prepare("SELECT IP FROM estaciones WHERE Booked in(select min(Booked) from estaciones WHERE IP != '127.0.0.1')");
      if ($SQL) {
        $SQL->execute();
        $res = $SQL->get_result();
        if ($res->num_rows > 0) {
          $row = $res->fetch_assoc();
                $lousy_station = $row['IP'];
        }
      }
    }
    $SQL->close();
    $db_found->close();
  return $lousy_station;    
  }

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
      var max_1 = "<?PHP echo $max_1 ; ?>";
  </script>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/popcorn.png">
  <link rel="icon" type="image/png" href="../assets/img/popcorn.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>Estadísiticas</title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->
  <link href="../assets/css/material-dashboard.css?v=2.1.0" rel="stylesheet" />
</head>

<body class="">
  <div class="wrapper ">
    <div class="sidebar" data-color="azure" data-background-color="black" data-image="../assets/img/960x480-universal-orlando-672x372.png">
      <div class="sidebar-wrapper">
        <ul class="nav">
          <li class="nav-item active  ">
            <a class="nav-link" href="./dashboard.php">
              <i class="material-icons">dashboard</i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item ">
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
            <a class="navbar-brand"><strong>Sistema Gestor de Filas Virtuales - Dashboard</strong></a>
            <a class="navbar-brand"></a>
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
          <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">star_border</i>
                  </div>
                  <p class="card-category">Atracciones</p>
                  <h3 class="card-title"><small><?PHP echo $best_ride_name; ?></small></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-info">stars</i>
                    <a >Atracción más popular</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-success card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">perm_identity</i>
                  </div>
                  <p class="card-category">Atracciones</p>
                  <h3 class="card-title"><small><?PHP echo $max_queue_name; ?></small></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-success">settings_ethernet</i> Fila virtual mas larga
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-danger card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">alarm</i>
                  </div>
                  <p class="card-category">Atracciones</p>
                  <h3 class="card-title"><strong><small><?PHP echo  $max_wait; ?></small> minutos</strong></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-danger">restore</i> Mayor tiempo de espera
                  </div>
                </div>
              </div>
            </div>
             <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">date_range</i>
                  </div>
                  <p class="card-category">Atracciones</p>
                  <h3 class="card-title"><small><?PHP echo  $reservas_total; ?> reservas</small></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-warning">local_offer</i> En total de todas las atracciones
                  </div>
                </div>
              </div>
            </div>
          <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">payment</i>
                  </div>
                  <p class="card-category">Fastpass</p>
                  <h3 class="card-title"><?PHP echo $amount_booked_fatpass; ?></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-warning">trending_up</i>Vendidos en total
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-danger card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">description</i>
                  </div>
                  <p class="card-category">Fastpass</p>
                  <h3 class="card-title"><?PHP echo $amount_fastpass; ?></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-danger">update</i>Disponibles para las atracciones
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-success card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">stars</i>
                  </div>
                  <p class="card-category">Fastpass</p>
                  <h3 class="card-title"><?PHP echo $max_fp_sol_name; ?></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-success">star_border</i>El más vendido
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">cached</i>
                  </div>
                  <p class="card-category">Fastpass</p>
                  <h3 class="card-title"><?PHP echo $max_att_fp_name; ?></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-info">update</i>El más disponible
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">tablet_android</i>
                  </div>
                  <p class="card-category">Estaciones</p>
                  <h3 class="card-title"><?PHP echo $busiest_station_ip; ?></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-info">update</i>La más ocupada
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">mobile_screen_share</i>
                  </div>
                  <p class="card-category">Estaciones</p>
                  <h3 class="card-title"><?PHP echo $lousy_station; ?></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-info">fullscreen</i>La mas disponible
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-primary card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">watch</i>
                  </div>
                  <p class="card-category">Brazaletes</p>
                  <h3 class="card-title"><?PHP echo $active_bands; ?></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-primary">watch</i>Brazaletes Activos
                  </div>
                </div>
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
  <script src="../assets/js/material-dashboard.js?v=2.1.0" type="text/javascript"></script>
  <script>
    $(document).ready(function() {
      // Javascript method's body can be found in assets/js/demos.js
      md.initDashboardPageCharts();

    });
  </script>
</body>

</html>