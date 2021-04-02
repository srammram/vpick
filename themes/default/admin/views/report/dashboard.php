<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
    
    <div class="box-content">
    
    <div class="row">
    		<div class="col-lg-12">
            	<h2>Health Total Hours</h2>
            </div>
            <div class="col-lg-12">
                <!-- Styles -->
                <style>
                #chartdiv {
                  width: 100%;
                  height: 500px;
                }

                </style>

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>

<!-- Chart code -->
<script>
/**
 * ---------------------------------------
 * This demo was created using amCharts 4.
 *
 * For more information visit:
 * https://www.amcharts.com/
 *
 * Documentation is available at:
 * https://www.amcharts.com/docs/v4/
 * ---------------------------------------
 */

am4core.useTheme(am4themes_animated);

// Create chart instance
var chart = am4core.create("chartdiv", am4charts.XYChart);
var jsonData = <?php echo json_encode($health) ?>;
// Add data
chart.data = jsonData;


// Create axes
var xAxis = chart.xAxes.push(new am4charts.CategoryAxis());
xAxis.dataFields.category = "stage";
xAxis.renderer.grid.template.location = 0;
xAxis.renderer.minGridDistance = 0;

var yAxis = chart.yAxes.push(new am4charts.DurationAxis());
yAxis.baseUnit = "second";
yAxis.title.text = "Healt Spending Total Duration";

// Create series
var series = chart.series.push(new am4charts.ColumnSeries());
series.dataFields.valueY = "duration";
series.dataFields.categoryX = "stage";
//series.columns.template.tooltipText = "{categoryX}: {valueY}";
</script>

<!-- HTML -->
<div id="chartdiv"></div>
            </div>
        </div>
    </div>
</div>

