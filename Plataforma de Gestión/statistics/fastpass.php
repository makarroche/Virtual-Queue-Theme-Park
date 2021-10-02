<?php
    session_start(); 
    require '../../configure.php';

    if (isset($_POST['Logout'])) {
        header('Location: logout.php');
    }

    $av_fp_1=available_fastpass(1);
    $av_fp_2=available_fastpass(2);
    $av_fp_3=available_fastpass(3);
    $av_fp_4=available_fastpass(4);
    $av_fp_5=available_fastpass(5);
    $av_fp_6=available_fastpass(6);
    $av_fp_7=available_fastpass(7);
    $av_fp_8=available_fastpass(8);
    $av_fp_9=available_fastpass(9);
    $av_fp_10=available_fastpass(10);
    $av_fp_11=available_fastpass(11);
    $av_fp_12=available_fastpass(12);

    $max_att_fp=max_att_fastpass();
    $max_att_fp_name=best_ride_name($max_att_fp);


    $fp_sale_1=fastpass_sales_by_attracion(1);
    $fp_sale_2=fastpass_sales_by_attracion(2);
    $fp_sale_3=fastpass_sales_by_attracion(3);
    $fp_sale_4=fastpass_sales_by_attracion(4);
    $fp_sale_5=fastpass_sales_by_attracion(5);
    $fp_sale_6=fastpass_sales_by_attracion(6);
    $fp_sale_7=fastpass_sales_by_attracion(7);
    $fp_sale_8=fastpass_sales_by_attracion(8);
    $fp_sale_9=fastpass_sales_by_attracion(9);
    $fp_sale_10=fastpass_sales_by_attracion(10);
    $fp_sale_11=fastpass_sales_by_attracion(11);
    $fp_sale_12=fastpass_sales_by_attracion(12);

    $total_fp_sales=fastpass_vendidos();
    $max_fp_sol=max_att_fastpass_sold();
    $max_fp_sol_name=best_ride_name($max_fp_sol);

    //Fastpass disponibles por atracción
      function available_fastpass($rideNumber){
        $database = "theme_park";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
        if ($db_found) {
          $SQL = $db_found->prepare("SELECT COUNT(*) FROM fastpass WHERE Ride_fastpass=?");
          if ($SQL) {
             $SQL->bind_param('i', $rideNumber);
            $SQL->execute();
            $res = $SQL->get_result();
            if ($res->num_rows > 0) {
              $row = $res->fetch_assoc();
                    $av_fastpass = $row['COUNT(*)'];
            }
          }
        }
        $SQL->close();
        $db_found->close();
      return $av_fastpass;    
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
      return $fastpass_ride_name; 
      }

    //Cuenta la cantidad de fastpass disponibles para reserva en total 
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

    //Cuenta la cantidad de fastpass vendidos en total
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


    //Cuenta la cantidad de fastpass vendidos por cada guest
    function fastpass_comprados_por_cada_guest($guest_id){
      $database = "theme_park";
      $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
      if ($db_found) {
        $SQL = $db_found->prepare("SELECT FASTPASS FROM guests WHERE ID=?");
        if ($SQL) {
           $SQL->bind_param('i', $guest_id);
          $SQL->execute();
          $res = $SQL->get_result();
          if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
                  $amount_booked_fatpass = $row['FASTPASS'];
          }
        }
      }
      $SQL->close();
      $db_found->close();
    return $amount_booked_fatpass;    
    }
   
   //Muestra el ID de cada usuario
    function mostrar_guest_ids(){
      $database = "theme_park";
      $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
      if ($db_found) {
        $SQL = $db_found->prepare("SELECT ID FROM guests WHERE !ISNULL(FASTPASS)");
        if ($SQL) {
          $SQL->execute();
          $res = $SQL->get_result();
          if ($res->num_rows > 0) {
            while($row = $res->fetch_assoc()){
                echo '<a class="dropdown-item" name = '. fastpass_comprados_por_cada_guest($row['ID']). ' id="'. $row['ID']. '" onclick= "changeGuestInfo('. $row['ID'] . ')">';
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

    //Horarios de cada fastpass
      function fastpass_hour($rideNumber){
        $fastpass_ride_name = NULL;
        $database = "theme_park";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );

        if ($db_found) {

          $SQL = $db_found->prepare("SELECT Time_Fastpass FROM fastpass WHERE Ride_fastpass=?");
          if ($SQL) {
            $SQL->bind_param('i', $rideNumber);
            $SQL->execute();
            $res = $SQL->get_result();

            if ($res->num_rows > 0) {
              while($row = $res->fetch_assoc()){
              	  if(!is_null($row["Time_Fastpass"])){
	                  echo substr($row["Time_Fastpass"], 0, -3);
	                  echo " &nbsp;";
                  }
              } 
            }
            else{
            	if($res->num_rows == 0){
                  	echo "N/A";
                  }
            }
          }
        }
        $SQL->close();
        $db_found->close();
      }

      //Fastpass vendidos por atracción
      function fastpass_sales_by_attracion($rideNumber){
        $fastpass_ride_name = NULL;
        $database = "theme_park";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );

        if ($db_found) {

          $SQL = $db_found->prepare("SELECT FastpassSales FROM rides WHERE IDRide=?");
          if ($SQL) {
            $SQL->bind_param('i', $rideNumber);
            $SQL->execute();
            $res = $SQL->get_result();

            if ($res->num_rows > 0) {
              $row = $res->fetch_assoc();
              $fastpass_sale = $row['FastpassSales'];
            }
          }
        }
        $SQL->close();
        $db_found->close();
      return $fastpass_sale; 
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
	    	Fastpass
	  	</title>
	  	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
	  	<!--     Fonts and icons     -->
	  	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
	  	<!--link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css"-->
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
	  	<!-- CSS Files -->
	  	<link href="../assets/css/material-dashboard.css?v=2.1.0" rel="stylesheet" />

	    <script type="text/javascript">
	      	var $av_fp_1=<?PHP echo $av_fp_1; ?>;
	      	var $av_fp_2=<?PHP echo $av_fp_2; ?>;
	      	var $av_fp_3=<?PHP echo $av_fp_3; ?>;
	      	var $av_fp_4=<?PHP echo $av_fp_4; ?>;
	      	var $av_fp_5=<?PHP echo $av_fp_5; ?>;
	      	var $av_fp_6=<?PHP echo $av_fp_6; ?>;
	      	var $av_fp_7=<?PHP echo $av_fp_7; ?>;
	      	var $av_fp_8=<?PHP echo $av_fp_8; ?>;
	      	var $av_fp_9=<?PHP echo $av_fp_9; ?>;
	      	var $av_fp_10=<?PHP echo $av_fp_10; ?>;
	      	var $av_fp_11=<?PHP echo $av_fp_11; ?>;
	      	var $av_fp_12=<?PHP echo $av_fp_12; ?>;

	      	var $fp_sale_1=<?PHP echo $fp_sale_1; ?>;
	      	var $fp_sale_2=<?PHP echo $fp_sale_2; ?>;
	      	var $fp_sale_3=<?PHP echo $fp_sale_3; ?>;
	      	var $fp_sale_4=<?PHP echo $fp_sale_4; ?>;
	      	var $fp_sale_5=<?PHP echo $fp_sale_5; ?>;
	      	var $fp_sale_6=<?PHP echo $fp_sale_6; ?>;
	      	var $fp_sale_7=<?PHP echo $fp_sale_7; ?>;
	      	var $fp_sale_8=<?PHP echo $fp_sale_8; ?>;
	      	var $fp_sale_9=<?PHP echo $fp_sale_9; ?>;
	      	var $fp_sale_10=<?PHP echo $fp_sale_10; ?>;
	      	var $fp_sale_11=<?PHP echo $fp_sale_11; ?>;
	      	var $fp_sale_12=<?PHP echo $fp_sale_12; ?>;      
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
	          			<li class="nav-item active">
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
		            			<strong>
		            				Fastpass - Datos Estadísticos 
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
			            	<div class="col-md-11 mx-auto">
			              		<div class="card card-chart">
			                		<div class="card-header card-header-success">
			                  			<div class="ct-chart" id="fastpassBooked"></div>
			                		</div>
			                		<div class="card-body">
			                  			<h4 class="card-title">Fastpass disponibles por atracción</h4>
			                		</div>
			                		<div class="card-footer">
			                  			<div class="stats">
			                  				<p>
			                    				<i class="material-icons">fast_forward</i>
			                    				La atracción con mayor cantidad de fastpass disponibles es
			                    				<span class="text-success">
			                    					<strong>
			                    						<?PHP echo ": ". $max_att_fp_name; ?>
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
				                  		<div class="ct-chart" id="fastpass_vendidos"></div>
				                	</div>
				                	<div class="card-body">
				                  		<h4 class="card-title">Fastpass Vendidos Por Atracción</h4>
				                  		<p class="card-category">
				                    	<span class="text">De <strong><?php echo $total_fp_sales; ?></strong> fastpass vendidos en total</p></span>
				                	</div>
				                	<div class="card-footer">
				                  		<div class="stats">
				                    		<i class="material-icons">access_time</i>La atracción con mayor ventas es: <span class="text-primary" id="informacion"><strong><?php echo "&nbsp" . $max_fp_sol_name; ?></strong></span>
				                  		</div>
				                	</div>
				              	</div>
				              	<div class="card card-chart">
				                	<div class="card-header card-header">
				                  		<!-- Split button -->
				                    	<div class="btn-group">
				                      		<button type="button" class="btn btn-info">Brazaletes con Fastpass</button>
				                      		<button type="button" class="btn btn-info dropdown-toggle px-3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				                        		<span class="sr-only">Toggle Dropdown</span>
				                      		</button>
				                      		<div class="dropdown-menu">
				                        		<h6 class="dropdown-header" ><strong>Tags RFID</strong>                   <i class="material-icons">watch</i></h6>
				                        		<div class="dropdown-divider"></div>
				                        		<?PHP mostrar_guest_ids();?> 
				                      		</div>
				                    	</div>
				                	</div>
				                	<div class="card-body">
				                  		<h4 class="card-title">Fastpass comprados</h4>
				                  		<p class="card-category" id="amp">
				                    		<span class="text-success" id="informacion"></span>
				                    		<i class="fas fa-arrow-circle-up"></i>
				                    		Seleccionar un TAG para ver la cantidad de Fastpass comprados para ese brazalete
				                    	</p>
				                	</div>
				                	<div class="card-footer">
				                  		<div class="stats">
				                    		<i class="material-icons">person</i> Ventas por usuario
				                  		</div>
				                	</div>
				              	</div>
		            		</div>
		            		<div class="col-lg-7 col-md-14">
				              	<div class="card">
				               		<div class="card-header card-header-tabs card-header-primary">
				                  		<div class="nav-tabs-navigation">
				                    		<div class="nav-tabs-wrapper">
				                      			<span class="nav-tabs-title">Fastpass:</span>
				                      			<ul class="nav nav-tabs" data-tabs="tabs">
				                        			<li class="nav-item">
				                          				<a class="nav-link active" href="#horarios" data-toggle="tab">
				                            				<i class="material-icons">alarm</i> Horarios
				                            				<div class="ripple-container"></div>
				                          				</a>
				                        			</li>
				                        			<li class="nav-item">
				                          				<a class="nav-link" href="#cantidad" data-toggle="tab">
				                            				<i class="material-icons">bookmark_border</i> Cantidad por atracción
				                            				<div class="ripple-container"></div>
				                          				</a>
				                        			</li>
				                      			</ul>
				                    		</div>
				                  		</div>
				                	</div>
				                	<div class="card-body">
				                  		<div class="tab-content">
				                    		<div class="tab-pane active" id="horarios">
				                      			<table class="table">
				                        			<thead class="text-primary">
				                          				<th>Atracción</th>
				                          				<th>Horarios</th>
				                        			</thead>
				                        			<tbody>                              
				                          				<tr>
				                            				<td><i class="fas fa-square" style="color: #aee256;"></i> River Adventure</td>
				                            				<td><?PHP fastpass_hour(1);?></td> 
				                          				</tr>
				                          				<tr>
				                            				<td><i class="fas fa-square" style="color: #68e256;"></i> Revenge of the Mummy</td>
				                            				<td><?PHP echo fastpass_hour(2); ?></td>
				                          				</tr>
				                          				<tr>
				                            				<td><i class="fas fa-square" style="color: #56e289;"></i> Forbidden Journey</td>
				                            				<td><?PHP echo fastpass_hour(3); ?></td>
				                          				</tr>
				                          				<tr>
				                            				<td><i class="fas fa-square" style="color: #56e2cf;"></i> Splash Mountain</td>
				                            				<td><?PHP echo fastpass_hour(4); ?></td>
				                          				</tr>
				                           				<tr>
				                            				<td><i class="fas fa-square" style="color: #56aee2;"></i> Pirates of the Caribbean</td>
				                            				<td><?PHP echo fastpass_hour(5); ?></td>
				                          				</tr>
				                           				<tr>
				                            				<td><i class="fas fa-square" style="color: #5668e2;"></i> E.T. Adventure</td>
				                            				<td><?PHP echo fastpass_hour(6); ?></td>
				                          				</tr>
				                           				<tr>
				                            				<td><i class="fas fa-square" style="color: #8a56e2;"></i> Expedition Everest</td>
				                            				<td><?PHP echo fastpass_hour(7); ?></td>
				                          				</tr>
				                           				<tr>
				                            				<td><i class="fas fa-square" style="color: #cf56e2;"></i> Tower of Terror</td>
				                            				<td><?PHP echo fastpass_hour(8); ?></td>
				                          				</tr>
				                          				<tr>
				                           	 				<td><i class="fas fa-square" style="color: #e256ae;"></i> Big Thunder Mountain</td>
				                            				<td><?PHP echo fastpass_hour(9); ?></td>
				                          				</tr>
				                          				<tr>
				                            				<td><i class="fas fa-square" style="color: #e25668;"></i> Jungle Cruise</td>
				                            				<td><?PHP echo fastpass_hour(10); ?></td>
				                          				</tr>
				                           				<tr>
				                            				<td><i class="fas fa-square" style="color: #e28956;"></i> Rock ’n’ Roller Coaster</td>
				                            				<td><?PHP echo fastpass_hour(11); ?></td>
				                          				</tr>
				                           				<tr>
				                            				<td><i class="fas fa-square" style="color: #e2cf56;"></i> Haunted Mansion</td>
				                            				<td><?PHP echo fastpass_hour(12); ?></td>
				                          				</tr>
				                        			</tbody>
				                      			</table>
				                    		</div>
				                    	<div class="tab-pane" id="cantidad">
				                      		<table class="table">
				                         		<thead class="text-primary">
				                          			<th>Atracción</th>
				                          			<th>Cantidad por Atracción</th>
				                        		</thead>
				                        		<tbody>
				                          			<tr>
				                            			<td><i class="fas fa-square" style="color: #aee256;"></i> River Adventure</td>
				                            			<td><?PHP echo $av_fp_1; ?></td>
				                          			</tr>
				                          			<tr>
				                            			<td><i class="fas fa-square" style="color: #68e256;"></i> Revenge of the Mummy</td>
				                            			<td><?PHP echo $av_fp_2; ?></td>
				                          			</tr>
				                          			<tr>
				                            			<td><i class="fas fa-square" style="color: #56e289;"></i> Forbidden Journey</td>
				                            			<td><?PHP echo $av_fp_3; ?></td>
				                          			</tr>
				                          			<tr>
				                            			<td><i class="fas fa-square" style="color: #56e2cf;"></i> Splash Mountain</td>
				                            			<td><?PHP echo $av_fp_4; ?></td>
				                          			</tr>
				                           			<tr>
				                            			<td><i class="fas fa-square" style="color: #56aee2;"></i> Pirates of the Caribbean</td>
				                            			<td><?PHP echo $av_fp_5; ?></td>
				                          			</tr>
				                           			<tr>
				                            			<td><i class="fas fa-square" style="color: #5668e2;"></i> E.T. Adventure</td>
				                            			<td><?PHP echo $av_fp_6; ?></td>
				                          			</tr>
				                           			<tr>
				                            			<td><i class="fas fa-square" style="color: #8a56e2;"></i> Expedition Everest</td>
				                            			<td><?PHP echo $av_fp_7; ?></td>
				                          			</tr>
				                           			<tr>
				                            			<td><i class="fas fa-square" style="color: #cf56e2;"></i> Tower of Terror</td>
				                            			<td><?PHP echo $av_fp_8; ?></td>
				                          			</tr>
				                          			<tr>
				                            			<td><i class="fas fa-square" style="color: #e256ae;"></i> Big Thunder Mountain</td>
				                            			<td><?PHP echo $av_fp_9; ?></td>
				                          			</tr>
				                          			<tr>
				                            			<td><i class="fas fa-square" style="color: #e25668;"></i> Jungle Cruise</td>
				                            			<td><?PHP echo $av_fp_10; ?></td>
				                          			</tr>
				                           			<tr>
				                            			<td><i class="fas fa-square" style="color: #e28956;"></i> Rock ’n’ Roller Coaster</td>
				                            			<td><?PHP echo $av_fp_11; ?></td>
				                          			</tr>
				                           			<tr>
				                            			<td><i class="fas fa-square" style="color: #e2cf56;"></i> Haunted Mansion</td>
				                            			<td><?PHP echo $av_fp_12; ?></td>
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
	  	<script src="../assets/js/material-fastpass.js" type="text/javascript"></script>
	  	<script>
	    	$(document).ready(function() {
	      		// Javascript method's body can be found in assets/js/demos.js
	      		md.initDashboardPageCharts();
	      });
      </script>
  </body>
</html>