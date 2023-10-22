<?php
include_once 'conexion.php';

$years = isset($_POST['years']) ? $_POST['years'] : [];
$stringY = '';
if ($years != []) {
    foreach($years as $index=>$y){
        if($index == count($years) - 1){
            $stringY .= "'%$y%'";
            continue;
        }
        $stringY .= "'%$y%' OR fecha LIKE ";
    }
}

if (isset($_POST['ajax_request']) && $_POST['ajax_request'] == 1) {
    $data = [];

    $consulta = "SELECT sum(venta) as venta, fecha FROM detalle_fact
            INNER JOIN encabezado_fact ON detalle_fact.codigo=encabezado_fact.codigo
            GROUP BY YEAR(fecha)
            HAVING sum(venta) >= $totales";


    $executar = mysqli_query($conexion, $consulta);

    while ($dato = mysqli_fetch_array($executar)) {
        $data[] = number_format($dato[0], 2, '.', ''); // Formatea el valor
    }

    echo json_encode($data);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<link rel="stylesheet" href="estilo.css">
<title>Document</title>
</head>

<body>
    <form action="index.php" method="POST" style="text-align:center">
        <label for="">Totales anuales mayores o iguales que </label>
        <br>
        <input type="text" name="totales" id="totales" value="<?php echo isset($_POST['totales']) ? $_POST['totales'] : "" ; ?>">
        <br>
        <?php
        /*$anios = "SELECT DISTINCT YEAR(fecha) as anio 
            FROM encabezado_fac WHERE YEAR(fecha) BETWEEN 2013 AND 2022 ORDER BY (fecha) ASC;";
        $ejecucion= mysqli_query($conexion, $anios);

        while($seleccionAnios= mysqli_fetch_array($ejecucion)){
            $existe = '';
            if (in_array($seleccionAnios[0], $years)) {
                $existe = 'checked';
            }
            echo "<label>".$seleccionAnios[0]."</label>";
            echo "<input name='years[]' $existe  value='$seleccionAnios[0]' type='checkbox' name='' id=''>";

        }*/

        ?>
        <br>
        <!--<input type="submit" value="Graficar">-->
    </form>
<figure class="highcharts-figure">
    <div id="container"></div>
</figure>

</body>
</html>
<script >
    
Highcharts.chart('container', {
    

    title: {
        text: 'Empresa XYZ',
        align: 'center'
    },

    subtitle: {
        text: 'Total de ventas anuales de los ultimos 10 años',
        align: 'center'
    },

    yAxis: {
        title: {
            text: 'Ventas $'
        }
    },

    xAxis: {
        accessibility: {
            rangeDescription: 'Desde: 2013 to 2022'
        }
    },

    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },

    plotOptions: {
        series: {
            label: {
                connectorAllowed: false
            },
            pointStart: 2013
        }
    },

    series: [{
        name: 'Ventas anuales',
        data: [
            
           <?php
            $totales = isset($_POST['totales'])? $_POST['totales']: "";

            
            if (!empty($years) && $totales !== "") {
                $whereConditions = [];
                foreach ($years as $y) {
                    $formattedYear = intval($y); // Formatea el año como un entero
                    $whereConditions[] = "fecha LIKE '%$formattedYear%'";
                }
                $whereClause = implode(" OR ", $whereConditions);

                $consulta = "SELECT sum(venta) as venta, fecha FROM detalle_fac
                    INNER JOIN encabezado_fac ON detalle_fac.codigo=encabezado_fac.codigo
                    WHERE ($whereClause)
                    GROUP BY YEAR(fecha)
                    HAVING sum(venta) >= $totales";

            } else if ($totales !== "") {
                $consulta = "SELECT sum(venta) as venta, fecha FROM detalle_fac
                    INNER JOIN encabezado_fac ON detalle_fac.codigo=encabezado_fac.codigo
                    GROUP BY YEAR(fecha)
                    HAVING sum(venta) >= $totales";

            } else if (!empty($years)) {
                $whereConditions = [];
                foreach ($years as $y) {
                    $formattedYear = intval($y); // Formatea el año como un entero
                    $whereConditions[] = "fecha LIKE '%$formattedYear%'";
                }

                $whereClause = implode(" OR ", $whereConditions);

                $consulta = "SELECT sum(venta) as venta, fecha FROM detalle_fac
                    INNER JOIN encabezado_fac ON detalle_fac.codigo=encabezado_fac.codigo
                    WHERE ($whereClause)
                    GROUP BY YEAR(fecha)";

            } else {
                $consulta = "SELECT sum(venta) as venta, fecha FROM detalle_fac
                    INNER JOIN encabezado_fac ON detalle_fac.codigo=encabezado_fac.codigo
                    GROUP BY YEAR(fecha)";
            }

            $executar = mysqli_query($conexion, $consulta);
            

            while ($dato = mysqli_fetch_array($executar)) {
                $d = number_format($dato[0], 2, '.', ''); // Formatea el valor
                echo $d . ",";
            }
        ?>
            ]
   
    }],

    responsive: {
        rules: [{
            condition: {
                maxWidth: 500
            },
            chartOptions: {
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                }
            }
        }]
    }

});

function updateChart() {
        var totales = document.getElementById('totales').value;
        $.ajax({
            type: "POST",
            url: "index.php",
            data: {
                totales: totales,
                ajax_request: 1
            },
            success: function(data) {
                // Analizar los datos JSON recibidos
                var chartData = JSON.parse(data);

                // Actualizar los datos del gráfico
                var chart = Highcharts.charts[0];
                chart.series[0].setData(chartData);
            }
        });
    }
</script>
<script src="grafico.js"></script>