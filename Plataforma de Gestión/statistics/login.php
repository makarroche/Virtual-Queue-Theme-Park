<?php
session_start();
if (!isset($_COOKIE['user'])) {
	$_COOKIE['user'] = "";
}

if (!isset($_SESSION['error'])) {
	$_SESSION['error'] = "";
}

if (isset($_SESSION['logged']) AND $_SESSION['logged']) {
	header('Location: dashboard.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Iniciar Sesión</title>
        <link rel="icon" type="image/png" href="../assets/img/popcorn.png">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <!-- Bootstrap core CSS -->
        <link href="../assets/css/login/bootstrap.min.css" rel="stylesheet">
        <!-- Material Design Bootstrap -->
        <link href="../assets/css/login/mdb.min.css" rel="stylesheet">
        <!-- Your custom styles (optional) -->
        <link href="../assets/css/login/style.css" rel="stylesheet">
    </head>
    <body>
        <div class="bg-sign">
        <!-- Default form login -->
        <div class="container">
            <!--Row-->
            <div class="row">
            	<div class="col-md-3"></div>
            	<div class="col-md-6 col-xl-5 mb-4 my-xl-5"></div>
            	<div class="col-md-3"></div>
            </div>
            <div class="row">
    		    <div class="col-md-3"></div>
    		    <div class="col-md-6 col-xl-5 mb-4 my-xl-5">
                    <div class="card mx-xl-5">
                        <!-- Card body -->
                        <div class="card-body">
                            <!-- Material form register -->
                            <form method="POST" action="loginProcess.php">

                                <p class="h4 text-center py-2">Iniciar Sesión</p>
                                <p class="text-center pb-4"><strong><small>Área Estadísticas</small></strong></p>


                                <!-- Material input email -->
                                <div class="md-form">
                                    <i class="fa fa-user-circle prefix grey-text"></i>
                                    <input type="text" id="userInput" name="userInput" class="form-control"
                                    	value=<?php echo $_COOKIE['user']?>>         
                                    <label for="userInput" class="font-weight-light">Usuario</label>
                                </div>

                                <!-- Material input password -->
                                <div class="md-form">
                                    <i class="fa fa-lock prefix grey-text"></i>
                                    <input type="password" id="passwordInput" name="passwordInput" class="form-control">
                                    <label for="passwordInput" class="font-weight-light">Contraseña</label>
                                </div>

                                <div class="text-center py-4 mt-3">
                                    <button class="btn btn-info waves-effect waves-light" type="submit">Ingresar</button>
                                </div>
                                <div class="red-text text-center">
                                    <?php echo $_SESSION['error'];?>
                                </div>
                            </form>
                            <!-- Material form register -->
                          </div>
                          <!-- Card body -->
                    </div>
                </div>
    		    <div class="col-md-3"></div>
    		</div>
        </div>
		<!-- Default form login -->

        <!-- SCRIPTS -->
        <!-- JQuery -->
        <script type="text/javascript" src="../assets/js/login/jquery-3.3.1.min.js"></script>
        <!-- Bootstrap tooltips -->
        <script type="text/javascript" src="../assets/js/login/popper.min.js"></script>
        <!-- Bootstrap core JavaScript -->
        <script type="text/javascript" src="../assets/js/login/bootstrap.min.js"></script>
        <!-- MDB core JavaScript -->
        <script type="text/javascript" src="../assets/js/login/mdb.min.js"></script>
    </body>
</html>


<?php
unset($_SESSION['error']);
?>
