 	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<!--    <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" /> -->
   <div id="content">
    
    

    <div class="row">
    
    <?php
	function h2m($hours) { 
            $t = explode(":", $hours); 
            $h = $t[0]; 
            if (isset($t[1])) { 
                $m = $t[1]; 
            } else { 
                $m = "00"; 
            } 
            $mm = ($h * 60)+$m; 
			
            return $mm; 
    }
	//$minutes=1510;
//echo $hours = intdiv($minutes, 60).':'. ($minutes % 60);
//$datetime1 = new DateTime('2019-08-25 01:47:00');
//$datetime2 = new DateTime('2019-08-26 16:14:52');
//$interval = $datetime1->diff($datetime2);
//echo $interval->format('%d')." Days ".$interval->format('%h')." Hours ".$interval->format('%i')." Minutes";

$dateDiff = intval((strtotime('2019-08-26 18:00:00')-strtotime('2019-08-26 16:00:52'))/60);

//echo $minutes = round(abs(strtotime('2019-08-26 18:00:00') - strtotime('2019-08-26 16:00:52')) / 60);

$minutes = round(abs(strtotime('2019-08-26 18:00:00') - strtotime('2019-08-26 16:00:52')) / 60);
$time    = h2m('04:10:00');

//echo $hours = intval($dateDiff/60).'hour';
//echo $minutes = ($dateDiff%60).'min';

?>
    		<?php
			
			if(!empty($url_data['user'])){
				foreach($url_data['user'] as $users){
			?>
            	<div class="col-sm-3 col-xs-6">
                    <div class="small-box <?= $users['color'] ?> ">
                       <div class="outer-inner">
                            <h3><?= $users['title'] ?></h3>
                       </div>
                        <div class="inner">
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
                          <div class="<?= $users['circle'] ?>"></div>
                        </div>
                        <a href="<?= $users['link'] ?>" class="small-box-footer"><?= lang('see_detail') ?> </a>
                  </div>
          	</div>
                
        	<?php
				}
			}
			?>
            
           
          </div>
	
    <div class="row">
    	<div class="col-lg-6">
        	<h3 class="dashboard_h3">Enquiry Customer Rating</h3>
            <div class="box enquiry_cust_rate">
				<?php
                for( $x = 0; $x < 5; $x++ )
                {
                    if( floor($url_data['rating'][0]['star'])-$x >= 1 )
                    { 
						echo '<i class="fa fa-star"></i>'; 
					}else{ 
						echo '<i class="fa fa-star-o"></i>'; 
					}
                }
				echo '- '.$url_data['rating'][0]['total'];
                ?>
                 <br>
                <?php
                for( $x = 0; $x < 5; $x++ )
                {
                    if( floor($url_data['rating'][1]['star'])-$x >= 1 )
                    { echo '<i class="fa fa-star"></i>'; }
                   
                    else
                    { echo '<i class="fa fa-star-o"></i>'; }
                }
				echo '- '.$url_data['rating'][1]['total'];
                ?>
                <br>
                <?php
                for( $x = 0; $x < 5; $x++ )
                {
                    if( floor($url_data['rating'][2]['star'])-$x >= 1 )
                    { echo '<i class="fa fa-star"></i>'; }
                   
                    else
                    { echo '<i class="fa fa-star-o"></i>'; }
                }
				echo '- '.$url_data['rating'][2]['total'];
                ?>
                <br>
                <?php
                for( $x = 0; $x < 5; $x++ )
                {
                    if( floor($url_data['rating'][3]['star'])-$x >= 1 )
                    { echo '<i class="fa fa-star"></i>'; }
                    
                    else
                    { echo '<i class="fa fa-star-o"></i>'; }
                }
				echo '- '.$url_data['rating'][3]['total'];
                ?>
                <br>
                <?php
                for( $x = 0; $x < 5; $x++ )
                {
                    if( floor($url_data['rating'][4]['star'])-$x >= 1 )
                    { echo '<i class="fa fa-star"></i>'; }
                   
                    else
                    { echo '<i class="fa fa-star-o"></i>'; }
                }
				echo '- '.$url_data['rating'][4]['total'];
                ?>
            </div>
        </div>
        <div class="col-lg-6">
        	<h3 class="dashboard_h3">Average Trip </h3>
            <div class="box  ">
				<ul id="skill">
                    	<?php
						foreach($url_data['average_ride'] as $average_ride){
						?>
						<li>
							<h4><?= $average_ride['title'] ?></h4>
							<div class="progress">
								<div class="progress-bar <?= $average_ride['color'] ?>" role="progressbar" aria-valuenow="<?= $average_ride['total'] ?>" aria-valuemin="0" aria-valuemax="1000" style="max-width:<?= $average_ride['total'] ?>% ">
									<span class="title"><?= $average_ride['total'] ?>%</span>
								</div>
							</div>
						</li>
						<?php
						}
						?>
					</ul>
            </div>
        </div>
       </div>
          
          <div class="row">
          <div>
          	
            <?php
			if(!empty($url_data['booked_ride'])){
			?>
           
            <div class="col-md-6 col-xs-12  ">
            <h3 class="dashboard_h3">Ride Type Details</h3>
				<div class="box  ">
				
				
				<div id="chartdiv"></div>
					<?php /*?><ul id="skill">
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
					</ul><?php */?>
					</div>
				</div>
            <?php
			}
			?>
            
            <?php
			if(!empty($url_data['ride'])){
			?>
           
            <div class="col-md-6 col-xs-12  ">
            <h3 class="dashboard_h3">Chart Ride Status</h3>
				<div class="box">
					<div id="chartdiv1"></div>
					</div>
				</div>
            <?php
			}
			?>
            
          	</div>
          </div>

	<div class="row">
    	<div class="col-lg-4">
        	<h3 class="dashboard_h3">Enquiry</h3>
            <div class="box  ">
                <div id="chartdiv2"></div>
            </div>
        </div>
        <div class="col-lg-4">
        	<h3 class="dashboard_h3">Cabs</h3>
            <div class="box  ">
                <div id="chartdiv3"></div>
            </div>
        </div>
        <div class="col-lg-4">
        	<h3 class="dashboard_h3">CRM Tickets</h3>
            <div class="box  ">
                <div id="chartdiv4"></div>
            </div>
        </div>
    </div>
    
    
    
</div>
<style>
#chartdiv {
  width: 100%;
  height: 500px;
}
#chartdiv1 {
  width: 100%;
  height: 500px;
}
#chartdiv2 {
  width: 100%;
  height: 300px;
}
#chartdiv3 {
  width: 100%;
  height: 300px;
}
#chartdiv4 {
  width: 100%;
  height: 300px;
}
	.amcharts-chart-div > a {
    display: none !important;
}
</style>
<script src="https://www.amcharts.com/lib/4/core.js"></script>
<script src="https://www.amcharts.com/lib/4/charts.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>


<script>

am4core.ready(function() {
am4core.useTheme(am4themes_animated);
var chart = am4core.create("chartdiv", am4charts.XYChart);
chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
chart.paddingBottom = 20;
chart.data = <?php echo json_encode($url_data['booked_ride']); ?>;

var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
categoryAxis.dataFields.category = "name";
categoryAxis.renderer.grid.template.strokeOpacity = 0;
categoryAxis.renderer.minGridDistance = 10;
categoryAxis.renderer.labels.template.dy = 35;
categoryAxis.renderer.tooltip.dy = 35;
	categoryAxis.renderer.labels.template.horizontalCenter = "right";
categoryAxis.renderer.labels.template.verticalCenter = "middle";
categoryAxis.renderer.labels.template.rotation = 270;

var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
valueAxis.renderer.inside = true;
valueAxis.renderer.labels.template.fillOpacity = 0.3;
valueAxis.renderer.grid.template.strokeOpacity = 0;
valueAxis.min = 0;
valueAxis.cursorTooltipEnabled = false;
valueAxis.renderer.baseGrid.strokeOpacity = 0;

var series = chart.series.push(new am4charts.ColumnSeries);
series.dataFields.valueY = "steps";
series.dataFields.categoryX = "name";
series.tooltipText = "{valueY.value}";
series.tooltip.pointerOrientation = "vertical";
series.tooltip.dy = - 6;
series.columnsContainer.zIndex = 100;

var columnTemplate = series.columns.template;
columnTemplate.width = am4core.percent(50);
columnTemplate.maxWidth = 52;
columnTemplate.column.cornerRadius(50, 50, 5, 5);
columnTemplate.strokeOpacity = 0;

series.heatRules.push({ target: columnTemplate, property: "fill", dataField: "valueY", min: am4core.color("#e5dc36"), max: am4core.color("#5faa46") });
series.mainContainer.mask = undefined;

var cursor = new am4charts.XYCursor();
chart.cursor = cursor;
cursor.lineX.disabled = true;
cursor.lineY.disabled = true;
cursor.behavior = "none";

var bullet = columnTemplate.createChild(am4charts.CircleBullet);
bullet.circle.radius = 30;
bullet.valign = "bottom";
bullet.align = "center";
bullet.isMeasured = true;
bullet.mouseEnabled = false;
bullet.verticalCenter = "bottom";
bullet.interactionsEnabled = false;

var hoverState = bullet.states.create("hover");
var outlineCircle = bullet.createChild(am4core.Circle);
outlineCircle.adapter.add("radius", function (radius, target) {
    var circleBullet = target.parent;
    return circleBullet.circle.pixelRadius + 10;
})

var image = bullet.createChild(am4core.Image);
image.width = 50;
image.height = 50;
image.horizontalCenter = "middle";
image.verticalCenter = "middle";

image.adapter.add("href", function (href, target) {
    var dataItem = target.dataItem;
	var img_name = dataItem.categoryX.toLowerCase();
	var imgsmall = img_name.replace(/ /g,"_");
    if (dataItem) {
        return image_site+"themes/default/admin/assets/images/" + imgsmall + ".png";
    }
})


image.adapter.add("mask", function (mask, target) {
    var circleBullet = target.parent;
    return circleBullet.circle;
})

var previousBullet;
chart.cursor.events.on("cursorpositionchanged", function (event) {
    var dataItem = series.tooltipDataItem;

    if (dataItem.column) {
        var bullet = dataItem.column.children.getIndex(1);

        if (previousBullet && previousBullet != bullet) {
            previousBullet.isHover = false;
        }

        if (previousBullet != bullet) {

            var hs = bullet.states.getKey("hover");
            hs.properties.dy = -bullet.parent.pixelHeight + 30;
            bullet.isHover = true;

            previousBullet = bullet;
        }
    }
})

}); 
</script>


<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

var chart = am4core.create("chartdiv1", am4charts.XYChart);
chart.hiddenState.properties.opacity = 0; // this makes initial fade in effect

chart.data = <?php echo json_encode($url_data['ride']); ?>;


var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
categoryAxis.renderer.grid.template.location = 0;
categoryAxis.dataFields.category = "category";
categoryAxis.renderer.minGridDistance = 20;
	categoryAxis.renderer.labels.template.horizontalCenter = "right";
categoryAxis.renderer.labels.template.verticalCenter = "middle";
categoryAxis.renderer.labels.template.rotation = 270;

var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

var series = chart.series.push(new am4charts.CurvedColumnSeries());
series.dataFields.categoryX = "category";
series.dataFields.valueY = "value";
series.tooltipText = "{valueY.value}"
series.columns.template.strokeOpacity = 0;
series.columns.template.tension = 1;

series.columns.template.fillOpacity = 0.75;

var hoverState = series.columns.template.states.create("hover");
hoverState.properties.fillOpacity = 1;
hoverState.properties.tension = 0.8;

chart.cursor = new am4charts.XYCursor();

// Add distinctive colors for each column using adapter
series.columns.template.adapter.add("fill", (fill, target) => {
  return chart.colors.getIndex(target.dataItem.index);
});

//chart.scrollbarX = new am4core.Scrollbar();
//chart.scrollbarY = new am4core.Scrollbar();

}); // end am4core.ready()
</script>

<!-- Chart code -->
<script>
am4core.ready(function() {
am4core.useTheme(am4themes_animated);
// Create chart instance
var chart = am4core.create("chartdiv2", am4charts.RadarChart);

// Add data
chart.data = <?php echo json_encode($url_data['enquiry']); ?>;

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

}); // end am4core.ready()
</script>

<!-- Chart code -->
<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

// Create chart instance
var chart = am4core.create("chartdiv3", am4charts.PieChart);

// Set data
var selected;
var types = <?php echo json_encode($url_data['cabs']); ?>;

// Add data
chart.data = generateChartData();

// Add and configure Series
var pieSeries = chart.series.push(new am4charts.PieSeries());
pieSeries.dataFields.value = "percent";
pieSeries.dataFields.category = "type";
pieSeries.slices.template.propertyFields.fill = "color";
pieSeries.slices.template.propertyFields.isActive = "pulled";
pieSeries.slices.template.strokeWidth = 0;

function generateChartData() {
  var chartData = [];
  for (var i = 0; i < types.length; i++) {
    if (i == selected) {
      for (var x = 0; x < types[i].subs.length; x++) {
        chartData.push({
          type: types[i].subs[x].type,
          percent: types[i].subs[x].percent,
          color: types[i].color,
          pulled: true
        });
      }
    } else {
      chartData.push({
        type: types[i].type,
        percent: types[i].percent,
        color: types[i].color,
        id: i
      });
    }
  }
  return chartData;
}

pieSeries.slices.template.events.on("hit", function(event) {
  if (event.target.dataItem.dataContext.id != undefined) {
    selected = event.target.dataItem.dataContext.id;
  } else {
    selected = undefined;
  }
  chart.data = generateChartData();
});

}); // end am4core.ready()
</script>
<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

// Create chart instance
var chart = am4core.create("chartdiv4", am4charts.PieChart);

// Add data
chart.data = <?php echo json_encode($url_data['tickets']); ?>;

// Add and configure Series
var pieSeries = chart.series.push(new am4charts.PieSeries());
pieSeries.dataFields.value = "litres";
pieSeries.dataFields.category = "country";
pieSeries.innerRadius = am4core.percent(50);
pieSeries.ticks.template.disabled = true;
pieSeries.labels.template.disabled = true;

var rgm = new am4core.RadialGradientModifier();
rgm.brightnesses.push(-0.8, -0.8, -0.5, 0, - 0.5);
pieSeries.slices.template.fillModifier = rgm;
pieSeries.slices.template.strokeModifier = rgm;
pieSeries.slices.template.strokeOpacity = 0.4;
pieSeries.slices.template.strokeWidth = 0;

chart.legend = new am4charts.Legend();
chart.legend.position = "right";

}); // end am4core.ready()
</script>
