<?php 
  session_start();
  if(!isset($_SESSION['mensaje'])){
    $_SESSION['mensaje']=" ";
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

    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.blue-indigo.min.css"/>
    <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>

    <!-- SCRIPTS -->
    <!-- JQuery -->
    <script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="js/popper.min.js"></script>
    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="js/mdb.min.js"></script>
  </head>

  <body>
    <!--Carousel Default Wrapper-->
      <div id="carousel-default" class="carousel default slide carousel-fade" data-ride="carousel" data-interval="3300">

        <!--Indicators-->
        <ol class="carousel-indicators">
          <li data-target="#carousel-default" data-slide-to="0" class="active"></li>
          <li data-target="#carousel-default" data-slide-to="1"></li>
          <li data-target="#carousel-default" data-slide-to="2"></li>
          <li data-target="#carousel-default" data-slide-to="3"></li>
          <li data-target="#carousel-default" data-slide-to="4"></li>
          <li data-target="#carousel-default" data-slide-to="5"></li>
        </ol>
        <!--/.Indicators-->

        <!--Slides-->
        <div class="carousel-inner default" role="listbox">

          <!--First slide-->
          <div class="carousel-item active default">
            <!--Mask-->
            <div class="view default">
              <div class="full-bg-img flex-center mask rgba-black-light white-text">
                <ul class="animated col-md-12 list-unstyled list-inline">
                  <li>
                    <h1 class="font-weight-normal carouselText">¡Toca con tu brazalete para ingresar a las filas!</h1>
                    <div>
                      <h1 class="font-weight-normal carouselText errormsg">
                        <?php 
                          echo $_SESSION['mensaje'];
                        ?>
                      </h1>
                    </div>
                  </li>
                </ul>
              </div>
              <p class="arrow bounce"></p>
            </div>
            <!--/.Mask-->
          </div>
          <!--/.First slide-->

          <!--Second slide -->
          <div class="carousel-item default">
            <!--Mask-->
            <div class="view default">
              <div class="full-bg-img flex-center mask rgba-black-light white-text">
                <ul class="animated col-md-12 list-unstyled list-inline">
                  <li>
                    <h1 class="font-weight-normal carouselText">¡Toca con tu brazalete para ingresar a las filas!</h1>
                    <div>
                      <h1 class="font-weight-normal carouselText errormsg">
                        <?php 
                          echo $_SESSION['mensaje'];
                        ?>
                      </h1>
                    </div>
                  </li>
                </ul>
              </div>
              <p class="arrow bounce"></p>
            </div>
            <!--/.Mask-->
          </div>
          <!--/.Second slide -->

          <!--Third slide-->
          <div class="carousel-item default">
            <!--Mask-->
            <div class="view default">
              <div class="full-bg-img flex-center mask rgba-black-light white-text">
                <ul class="animated col-md-12 list-unstyled list-inline">
                  <li>
                    <h1 class="font-weight-normal carouselText">¡Toca con tu brazalete para ingresar a las filas!</h1>
                    <div>
                      <h1 class="font-weight-normal carouselText errormsg">
                        <?php 
                          echo $_SESSION['mensaje'];
                        ?>
                      </h1>
                    </div>
                  </li>
                </ul>
              </div>
              <p class="arrow bounce"></p>
            </div>
            <!--/.Mask-->
          </div>
          <!--/.Third slide-->

          <!--Fourth slide-->
          <div class="carousel-item default">
            <!--Mask-->
            <div class="view default">
              <div class="full-bg-img flex-center mask rgba-black-light white-text">
                <ul class="animated col-md-12 list-unstyled list-inline">
                  <li>
                    <h1 class="font-weight-normal carouselText">¡Toca con tu brazalete para ingresar a las filas!</h1>
                    <div>
                      <h1 class="font-weight-normal carouselText errormsg">
                        <?php 
                          echo $_SESSION['mensaje'];
                        ?>
                      </h1>
                    </div>
                  </li>
                </ul>
              </div>
              <p class="arrow bounce"></p>
            </div>
            <!--/.Mask-->
          </div>
          <!--/.Fourth slide-->

          <!--Fifth slide-->
          <div class="carousel-item default">
            <!--Mask-->
            <div class="view default">
              <div class="full-bg-img flex-center mask rgba-black-light white-text">
                <ul class="animated col-md-12 list-unstyled list-inline">
                  <li>
                    <h1 class="font-weight-normal carouselText">¡Toca con tu brazalete para ingresar a las filas!</h1>
                    <div>
                      <h1 class="font-weight-normal carouselText errormsg">
                        <?php 
                          echo $_SESSION['mensaje'];
                        ?>
                      </h1>
                    </div>
                  </li>
                </ul>
              </div>
              <p class="arrow bounce"></p>
            </div>
            <!--/.Mask-->
          </div>
          <!--/.Fifth slide-->

           <!--Sixth slide-->
          <div class="carousel-item default">
            <!--Mask-->
            <div class="view default">
              <div class="full-bg-img flex-center mask rgba-black-light white-text">
                <ul class="animated col-md-12 list-unstyled list-inline">
                  <li>
                    <h1 class="font-weight-normal carouselText">¡Toca con tu brazalete para ingresar a las filas!</h1>
                    <div>
                      <h1 class="font-weight-normal carouselText errormsg">
                        <?php 
                          echo $_SESSION['mensaje'];
                        ?>
                      </h1>
                    </div>
                  </li>
                </ul>
              </div>
              <p class="arrow bounce"></p>
            </div>
            <!--/.Mask-->
          </div>
          <!--/.Sixth slide-->

        </div>
        <!--/.Slides-->
      </div>
    <!--/.Carousel Default Wrapper-->
    <script language="javascript">
      var autoPlayError="<?php echo $_SESSION['autoPlay'];?>";
      if(autoPlayError){
        var audioError = new Audio('sounds/bounce.ogg');
        audioError.play();
      }
    </script>
    <?php 
      $_SESSION['mensaje']=" ";
      $_SESSION['autoPlay']=false;
    ?>
  </body>
</html>

