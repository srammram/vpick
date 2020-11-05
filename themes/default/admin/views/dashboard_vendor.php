 	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<!--    <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" /> -->
   <div id="content">
    
    <div class="row">
    		<?php
			
			if(!empty($url_data['user'])){
				foreach($url_data['user'] as $users){
			?>
                <div class="col-lg-3 col-xs-6">
                  <div class="small-box <?= $users['color'] ?>">
                    <div class="inner">
                      <h3><?= $users['title'] ?></h3>
                     <span class="box_left">
                        <p>Approved</p>
                        <p><?= $users['active'] ?></p>
                      </span>
                      <span class="box_right">
                        <p>Not Approved</p>
                        <p><?= $users['inactive'] ?></p>
                      </span>
                    </div>
                    <div class="icon">
                      <i class="fa fa-bar-chart"></i>
                    </div>
                    <a href="<?= $users['link'] ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                  </div>
                </div>
        	<?php
				}
			}
			?>
          </div>
          
          <div class="row">
          <div ="margin: 25px 15px;">
          	
            <?php
			if(!empty($url_data['booked_ride'])){
			?>
            <div class="col-md-6 col-xs-12  ">
				<div class="box box-success ">
				<h3>Ride Type Details</h3>
					<ul id="skill">
                    	<?php
						foreach($url_data['booked_ride'] as $booked_ride){
						?>
						<li>
							<h4><?= $booked_ride['title'] ?></h4>
							<div class="progress">
								<div class="progress-bar <?= $booked_ride['color'] ?>" role="progressbar" aria-valuenow="<?= $booked_ride['total'] ?>" aria-valuemin="0" aria-valuemax="1000" style="max-width:<?= $booked_ride['total'] ?>% ">
									<span class="title"><?= $booked_ride['total'] ?>%</span>
								</div>
							</div>
						</li>
						<?php
						}
						?>
					</ul>
					</div>
				</div>
            <?php
			}
			?>
            
            <?php
			if(!empty($url_data['ride'])){
			?>
            <div class="col-md-6 col-xs-12  ">
				<div class="box box-success ">
				<h3>Chart Ride Status</h3>
					<ul id="chartdiv">
                    	
					</ul>
					</div>
				</div>
            <?php
			}
			?>
            
            
          	</div>
          </div>
</div>
<style>
#chartdiv {
  width: 100%;
  height: 350px;
}

</style>
<script src="https://www.amcharts.com/lib/4/core.js"></script>
<script src="https://www.amcharts.com/lib/4/charts.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>

<!-- Chart code -->
<script>
// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end



// Create chart instance
var chart = am4core.create("chartdiv", am4charts.RadarChart);

// Add data
chart.data = <?= json_encode($url_data['ride']); ?>
/*chart.data = [{
  "category": "Research",
  "value": 80,
  "full": 100
}, {
  "category": "Marketing",
  "value": 35,
  "full": 100
}, {
  "category": "Distribution",
  "value": 92,
  "full": 100
}, {
  "category": "Distribution1",
  "value": 92,
  "full": 100
},
 {
  "category": "Distribution11",
  "value": 92,
  "full": 100
}, {
  "category": "Human Resources",
  "value": 68,
  "full": 100
}];*/

// Make chart not full circle
chart.startAngle = -90;
chart.endAngle = 180;
chart.innerRadius = am4core.percent(20);

// Set number format
chart.numberFormatter.numberFormat = "#.#'%'";

// Create axes
var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
categoryAxis.dataFields.category = "category";
categoryAxis.renderer.grid.template.location = 0;
categoryAxis.renderer.grid.template.strokeOpacity = 0;
categoryAxis.renderer.labels.template.horizontalCenter = "right";
categoryAxis.renderer.labels.template.fontWeight = 500;
categoryAxis.renderer.labels.template.adapter.add("fill", function(fill, target) {
  return (target.dataItem.index >= 0) ? chart.colors.getIndex(target.dataItem.index) : fill;
});
categoryAxis.renderer.minGridDistance = 10;

var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
valueAxis.renderer.grid.template.strokeOpacity = 0;
valueAxis.min = 0;
valueAxis.max = 100;
valueAxis.strictMinMax = true;

// Create series
var series1 = chart.series.push(new am4charts.RadarColumnSeries());
series1.dataFields.valueX = "full";
series1.dataFields.categoryY = "category";
series1.clustered = false;
series1.columns.template.fill = new am4core.InterfaceColorSet().getFor("alternativeBackground");
series1.columns.template.fillOpacity = 0.08;
series1.columns.template.cornerRadiusTopLeft = 20;
series1.columns.template.strokeWidth = 0;
series1.columns.template.radarColumn.cornerRadius = 20;

var series2 = chart.series.push(new am4charts.RadarColumnSeries());
series2.dataFields.valueX = "value";
series2.dataFields.categoryY = "category";
series2.clustered = false;
series2.columns.template.strokeWidth = 0;
series2.columns.template.tooltipText = "{category}: [bold]{value}[/]";
series2.columns.template.radarColumn.cornerRadius = 20;

series2.columns.template.adapter.add("fill", function(fill, target) {
  return chart.colors.getIndex(target.dataItem.index);
});

// Add cursor
chart.cursor = new am4charts.RadarCursor();
</script>