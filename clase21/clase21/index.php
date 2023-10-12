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
<figure class="highcharts-figure">
    <div id="container"></div>
</figure>
</body>
</html>
<script >
    
Highcharts.chart('container', {
    

    title: {
        text: 'U.S Solar Employment Growth',
        align: 'left'
    },

    subtitle: {
        text: 'By Job Category. Source: <a href="https://irecusa.org/programs/solar-jobs-census/" target="_blank">IREC</a>.',
        align: 'left'
    },

    yAxis: {
        title: {
            text: 'Number of Employees'
        }
    },

    xAxis: {
        accessibility: {
            rangeDescription: 'Range: 2010 to 2020'
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
            include_once'conexion.php';
            $consulta="SELECT SUM(venta) as venta, fecha FROM detalle_fac 
            INNER JOIN encabezado_fac on detalle_fac.codigo=encabezado_fac.codigo
            GROUP BY YEAR(fecha)";
            $executar= mysqli_query($conexion,$consulta);
            while($dato=mysqli_fetch_array($executar))
            {
                $d= number_format($dato[0],2,'.','');
                echo $d.",";
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


</script>