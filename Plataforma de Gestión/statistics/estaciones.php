<?php
    session_start(); 
    require '../../configure.php';

    if (isset($_POST['Logout'])) {
        header('Location: logout.php');
    }


    $busiest_station=busy_station();
    $lousy_station=lousy_station();
    $busiest_station_ip=$busiest_station['IP'];
    $busiest_station_booked=$busiest_station['Booked'];

    $station_ip_1=booked_stations('192.168.105.0');
    $station_ip_2=booked_stations('192.168.105.1');
    $station_ip_3=booked_stations('192.168.105.2');
    $station_ip_4=booked_stations('192.168.105.3');
    $station_ip_5=booked_stations('192.168.105.4');
    $station_ip_6=booked_stations('192.168.105.5');
    $station_ip_7=booked_stations('192.168.105.6');
    $station_ip_8=booked_stations('192.168.105.7');
    $station_ip_9=booked_stations('192.168.105.8');
    $station_ip_10=booked_stations('192.168.105.9');

    $station_cap_1=capacity_stations('192.168.105.0');
    $station_cap_2=capacity_stations('192.168.105.1');
    $station_cap_3=capacity_stations('192.168.105.2');
    $station_cap_4=capacity_stations('192.168.105.3');
    $station_cap_5=capacity_stations('192.168.105.4');
    $station_cap_6=capacity_stations('192.168.105.5');
    $station_cap_7=capacity_stations('192.168.105.6');
    $station_cap_8=capacity_stations('192.168.105.7');
    $station_cap_9=capacity_stations('192.168.105.8');
    $station_cap_10=capacity_stations('192.168.105.9');

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


      //Reservas en cada estación
      function booked_stations($station_ip){
        $fastpass_ride_name = NULL;
        $database = "theme_park";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );

        if ($db_found) {

          $SQL = $db_found->prepare("SELECT Booked FROM estaciones WHERE IP=?");
          if ($SQL) {
            $SQL->bind_param('s', $station_ip);
            $SQL->execute();
            $res = $SQL->get_result();

            if ($res->num_rows > 0) {
              $row = $res->fetch_assoc();
              $booked_st = $row['Booked'];
            }
          }
        }
        $SQL->close();
        $db_found->close();
      return $booked_st; 
      }

      //Capacidad por estación
      function capacity_stations($station_ip){
        $fastpass_ride_name = NULL;
        $database = "theme_park";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );

        if ($db_found) {

          $SQL = $db_found->prepare("SELECT Capacidad FROM estaciones WHERE IP=?");
          if ($SQL) {
            $SQL->bind_param('s', $station_ip);
            $SQL->execute();
            $res = $SQL->get_result();

            if ($res->num_rows > 0) {
              $row = $res->fetch_assoc();
              $capacity_st = $row['Capacidad'];
            }
          }
        }
        $SQL->close();
        $db_found->close();
      return $capacity_st; 
      }

      //Revisar estaciones saturadas
      function saturated_station($station_booked,  $station_capacity){
      		$alert = "";
      		if($station_booked>$station_capacity){
      			$alert=' &nbsp &nbsp <span class="text-danger"><strong>Capacidad diaria superada<i class="material-icons">error_outline </i></span></strong>';
      		}
      		echo $alert;
      }

    //Mostrar Ips de estaciones
    function mostrar_station_ips(){
      $database = "theme_park";
      $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
      if ($db_found) {
        $SQL = $db_found->prepare("SELECT IP FROM estaciones WHERE IP != '127.0.0.1'");
        if ($SQL) {
          $SQL->execute();
          $res = $SQL->get_result();
          if ($res->num_rows > 0) {
            while($row = $res->fetch_assoc()){
                echo '<a class="dropdown-item" onclick= changeStationInfo("' . $row['IP'] . '")>';
                echo $row['IP'];
                echo '</a>';
            }      
          }
        }
      }
      $SQL->close();
      $db_found->close();
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
	    	Estaciones
	  	</title>
	  	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
	  	<!--     Fonts and icons     -->
	  	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
	  	<!--link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css"-->
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
	  	<!-- CSS Files -->
	  	<link href="../assets/css/material-dashboard.css?v=2.1.0" rel="stylesheet" />

	    <script type="text/javascript">
	      	     var station_ip_1=<?PHP echo $station_ip_1; ?>;
	      	     var station_ip_2=<?PHP echo $station_ip_2; ?>;
	      	     var station_ip_3=<?PHP echo $station_ip_3; ?>;
	      	     var station_ip_4=<?PHP echo $station_ip_4; ?>;
	      	     var station_ip_5=<?PHP echo $station_ip_5; ?>;
	      	     var station_ip_6=<?PHP echo $station_ip_6; ?>;
	      	     var station_ip_7=<?PHP echo $station_ip_7; ?>;
	      	     var station_ip_8=<?PHP echo $station_ip_8; ?>;
	      	     var station_ip_9=<?PHP echo $station_ip_9; ?>;
	      	     var station_ip_10=<?PHP echo $station_ip_10; ?>; 

	      	     var  busiest_station_ip=<?PHP echo "'$busiest_station_ip'"; ?>;
	      	     var  busiest_station_booked=<?PHP echo $busiest_station_booked; ?>;  
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
	          			<li class="nav-item active">
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
		            			<strong>
		            				Estaciones - Datos Estadísticos 
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
		        		<div class="row">
			            	<div class="col-md-12 mx-auto">
			              		<div class="card card-chart">
			                		<div class="card-header card-header-primary">
			                  			<div class="ct-chart" id="EstacionesCarga"></div>
			                		</div>
			                		<div class="card-body">
			                  			<h4 class="card-title">Reservas por estación</h4>
			                		</div>
			                		<div class="card-footer">
			                  			<div class="stats">
			                  				<p>
			                    				<i class="material-icons">video_label</i>
			                    				La estación más ocupada es
			                    				<span class="text-success">
			                    					<strong>
			                    						<?PHP echo ": ". $busiest_station_ip; ?>
			                    					</strong>
			                    				</span>
			                    			</p>
			                  			</div>
			                		</div>
			              		</div>
			            	</div>			
		          		</div>
		          		<div class="row ml-3">
		          			<div class="col-md-5">
				              	<div class="card card-chart">
				                	<div class="card-header card-header">
				                  		<div class="ct-chart" id="estaciones_pie"></div>
				                	</div>
				                	<div class="card-body">
				                  		<h4 class="card-title">Reservas por estación</h4>
				                  		<p class="card-category">
				                    	<span class="text"></span>
				                	</div>
				                	<div class="card-footer">
				                  		<div class="stats">
				                    		<i class="material-icons">trending_down</i>La estación menos ocupada es : <span class="text-info" id="informacion"><strong><?php echo $lousy_station?></span></strong>
				                  		</div>
				                	</div>
				              	</div>
				              	<div class="card card-chart">
				                	<div class="card-header card-header">
				                  		<!-- Split button -->
				                    	<div class="btn-group">
				                      		<button type="button" class="btn btn-success">IP de Estaciones</button>
				                      		<button type="button" class="btn btn-success dropdown-toggle px-3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				                        		<span class="sr-only">Toggle Dropdown</span>
				                      		</button>
				                      		<div class="dropdown-menu">
				                        		<h6 class="dropdown-header" ><strong>IP FIJAS</strong>                   <i class="material-icons">touch_app</i></h6>
				                        		<div class="dropdown-divider"></div>
				                        		<?PHP mostrar_station_ips(); ?> 
				                      		</div>
				                    	</div>
				                	</div>
				                	<div class="card-body">
				                  		<h4 class="card-title">Ubicación</h4>
				                  		<p class="card-category" id="amp">
				                    		<span class="text-success"></span>
				                    		<i class="fas fa-arrow-circle-up"></i>
				                    		Seleccionar una IP para ver la localización de una estación
				                    	</p>
				                	</div>
				                	<div class="card-footer">
				                  		<div class="stats">
				                    		<i class="material-icons">not_listed_location</i> Localización en mapa
				                  		</div>
				                	</div>
				              	</div>
		            		</div>
		            		<div class="col-lg-7 col-md-14">
				              	<div class="card">
				               		<div class="card-header card-header-tabs card-header-info">
				                  		<div class="nav-tabs-navigation">
				                    		<div class="nav-tabs-wrapper">
				                      			<span class="nav-tabs-title">Estaciones:</span>
				                      			<ul class="nav nav-tabs" data-tabs="tabs">
				                        			<li class="nav-item">
				                          				<a class="nav-link active" href="#info_estaciones" data-toggle="tab">
				                            				<i class="material-icons">touch_app</i> Reservas
				                            				<div class="ripple-container"></div>
				                          				</a>
				                        			</li>
				                        			<li class="nav-item">
				                          				<a class="nav-link" href="#capacidad" data-toggle="tab">
				                            				<i class="material-icons">feedback</i> Capacidad Diaria
				                            				<div class="ripple-container"></div>
				                          				</a>
				                        			</li>
				                      			</ul>
				                    		</div>
				                  		</div>
				                	</div>
				                	<div class="card-body">
				                  		<div class="tab-content">
				                    		<div class="tab-pane active" id="info_estaciones">
				                      			<table class="table">
				                        			<thead class="text-primary">
				                          				<th>IP de Estación</th>
				                          				<th>Reservas</th>
				                        			</thead>
				                        			<tbody>                              
				                          				<tr>
				                            				<td><i class="fas fa-square" style="color: #aee256;"></i> 192.168.105.0</td>
				                            				<td><?PHP echo $station_ip_1;?></td> 
				                          				</tr>
				                          				<tr>
				                            				<td><i class="fas fa-square" style="color: #68e256;"></i> 192.168.105.1</td>
				                            				<td><?PHP echo $station_ip_2;?></td>
				                          				</tr>
				                          				<tr>
				                            				<td><i class="fas fa-square" style="color: #56e289;"></i> 192.168.105.2</td>
				                            				<td><?PHP echo $station_ip_3;?></td>
				                          				</tr>
				                          				<tr>
				                            				<td><i class="fas fa-square" style="color: #56e2cf;"></i> 192.168.105.3</td>
				                            				<td><?PHP echo $station_ip_4;?></td>
				                          				</tr>
				                           				<tr>
				                            				<td><i class="fas fa-square" style="color: #56aee2;"></i> 192.168.105.4</td>
				                            				<td><?PHP echo $station_ip_5;?></td>
				                          				</tr>
				                           				<tr>
				                            				<td><i class="fas fa-square" style="color: #5668e2;"></i> 192.168.105.5</td>
				                            				<td><?PHP echo $station_ip_6;?></td>
				                          				</tr>
				                           				<tr>
				                            				<td><i class="fas fa-square" style="color: #8a56e2;"></i> 192.168.105.6</td>
				                            				<td><?PHP echo $station_ip_7;?></td>
				                          				</tr>
				                           				<tr>
				                            				<td><i class="fas fa-square" style="color: #cf56e2;"></i> 192.168.105.7</td>
				                            				<td><?PHP echo $station_ip_8;?></td>
				                          				</tr>
				                          				<tr>
				                           	 				<td><i class="fas fa-square" style="color: #e256ae;"></i> 192.168.105.8</td>
				                            				<td><?PHP echo $station_ip_9;?></td>
				                          				</tr>
				                          				<tr>
				                            				<td><i class="fas fa-square" style="color: #e25668;"></i> 192.168.105.9</td>
				                            				<td><?PHP echo $station_ip_10;?></td>
				                          				</tr>
				                        			</tbody>
				                      			</table>
				                    		</div>
				                    	<div class="tab-pane" id="capacidad">
				                      		<table class="table">
				                         		<thead class="text-primary">
				                          			<th>IP de Estación</th>
				                          			<th>Capacidad diaria</th>
				                        		</thead>
				                        		<tbody>
				                          			<tr>
			                            				<td><i class="fas fa-square" style="color: #aee256;"></i> 192.168.105.0</td>
			                            				<td><?PHP echo $station_cap_1;
			                            					saturated_station($station_ip_1,  $station_cap_1);
			                            				?></td> 
			                          				</tr>
			                          				<tr>
			                            				<td><i class="fas fa-square" style="color: #68e256;"></i> 192.168.105.1</td>
			                            				<td><?PHP echo $station_cap_2; 
			                            				saturated_station($station_ip_2,  $station_cap_2);
			                            				?></td>
			                          				</tr>
			                          				<tr>
			                            				<td><i class="fas fa-square" style="color: #56e289;"></i> 192.168.105.2</td>
			                            				<td><?PHP echo $station_cap_3;
			                            				saturated_station($station_ip_3,  $station_cap_3);
			                            				?></td>
			                          				</tr>
			                          				<tr>
			                            				<td><i class="fas fa-square" style="color: #56e2cf;"></i> 192.168.105.3</td>
			                            				<td><?PHP echo $station_cap_4;
			                            				saturated_station($station_ip_4,  $station_cap_4);
			                            				?></td>
			                          				</tr>
			                           				<tr>
			                            				<td><i class="fas fa-square" style="color: #56aee2;"></i> 192.168.105.4</td>
			                            				<td><?PHP echo $station_cap_5;
			                            				saturated_station($station_ip_5,  $station_cap_5);
			                            				?></td>
			                          				</tr>
			                           				<tr>
			                            				<td><i class="fas fa-square" style="color: #5668e2;"></i> 192.168.105.5</td>
			                            				<td><?PHP echo $station_cap_6;
			                            				saturated_station($station_ip_6,  $station_cap_6);
			                            				?></td>
			                          				</tr>
			                           				<tr>
			                            				<td><i class="fas fa-square" style="color: #8a56e2;"></i> 192.168.105.6</td>
			                            				<td><?PHP echo $station_cap_7;
			                            				saturated_station($station_ip_7,  $station_cap_7);?></td>
			                          				</tr>
			                           				<tr>
			                            				<td><i class="fas fa-square" style="color: #cf56e2;"></i> 192.168.105.7</td>
			                            				<td><?PHP echo $station_cap_8;
			                            				saturated_station($station_ip_8,  $station_cap_8);?></td>
			                          				</tr>
			                          				<tr>
			                           	 				<td><i class="fas fa-square" style="color: #e256ae;"></i> 192.168.105.8</td>
			                            				<td><?PHP echo $station_cap_9;
			                            				saturated_station($station_ip_9,  $station_cap_9);?></td>
			                          				</tr>
			                          				<tr>
			                            				<td><i class="fas fa-square" style="color: #e25668;"></i> 192.168.105.9</td>
			                            				<td><?PHP echo $station_cap_10;
			                            				saturated_station($station_ip_10,  $station_cap_10);?></td>
			                          				</tr>
				                          		</tbody>
				                      		</table>
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
	  	<script src="../assets/js/material-estaciones.js" type="text/javascript"></script>
	  	<script>
	    	$(document).ready(function() {
	      		// Javascript method's body can be found in assets/js/demos.js
	      		md.initDashboardPageCharts();
	      });
      </script>
  </body>
</html>