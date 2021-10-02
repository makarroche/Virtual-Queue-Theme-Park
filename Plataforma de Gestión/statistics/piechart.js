 dataCompletedTasksChart = {
        labels: ['12p', '3p', '6p', '9p', '12p', '3a', '6a', '9a'],
        series: [
          [230, 750, 450, 300, 280, 240, 200, 190]
        ]
      };

      optionsCompletedTasksChart = {
        lineSmooth: Chartist.Interpolation.cardinal({
          tension: 0
        }),
        low: 0,
        high: 1000, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
        chartPadding: {
          top: 0,
          right: 0,
          bottom: 0,
          left: 0
        }
      }


//Busca la cantidad de reservas de todas las atracciones
      function reservas_atracciones($stelar_ride){
        $database = "theme_park";
        $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, $database );
        if ($db_found) {
          $SQL = $db_found->prepare("SELECT IDRide, Booked FROM rides WHERE IDRide != ?");
          if ($SQL) {
            $SQL->bind_param('i', $stelar_ride);
            $SQL->execute();
            $res = $SQL->get_result();
            if ($res->num_rows > 0) {
              $row = $res->fetch_assoc();
                    $todas_atracciones = $row;
            }
          }
        }
        $SQL->close();
        $db_found->close();
      return $todas_atracciones;    
      }