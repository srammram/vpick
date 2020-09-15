<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
</body>
</html>
public function demo1_get(){
		
		//$var = "0,6,19,6,4,0,17,111,3,1174,2859,6,0,2,20,275,2,16,80,2903,4,0,0,1720,236,1136,15,5,8,170,527,617,36,497,3511,873,18,2,2865,3718,672,11,319,43";
		
		//$d = explode(',', $var);
		//print_r($d);
		//echo array_sum($d);
		
		//$d = array(13.123521, 80.191368,3,13.123622, 80.191489,3,13.123741, 80.191512,3,13.123860, 80.191539,3,13.123932, 80.191654,3,13.123959, 80.191837,3,13.123989, 80.192089,3,13.123985, 80.192243,3,13.123969, 80.192394,3,13.123972, 80.192547,3,13.123967, 80.192802,3,13.123965, 80.192967,3,13.123959, 80.193158,3,13.123956, 80.193325,3,13.123954, 80.193506,3,13.123954, 80.193713,3,13.123956, 80.193930,3,13.123952, 80.194137,3,13.123983, 80.194318,3,13.124106, 80.194403,3,13.124231, 80.194478,3,13.124299, 80.194545,3,13.124299, 80.194672,3,13.124279, 80.194886,3,13.124224, 80.195105,3,13.124119, 80.195258,3,13.124040, 80.195396,3,13.123980, 80.195533,3,13.123888, 80.195662,3,13.123864, 80.195687,3,13.123726, 80.195811,3,13.123607, 80.195906,3,13.123515, 80.196018,3,13.123464, 80.196163,3,13.123447, 80.196312,3,13.123568, 80.196352,3,13.123706, 80.196251,3,13.123853, 80.196129,3,13.123996, 80.196016,3,13.124130, 80.195917,3,13.124253, 80.195818,3,13.124402, 80.195621,3,13.124574, 80.195486,3,13.124723, 80.195366,3);
		$d = array(11.1860923,78.0778515,3,11.1736662,78.049922,3,11.147918,78.0244241,3,11.0998988,78.0049057,3,11.0747984,78.0083333,3,11.0539987,78.0428653,3,11.0423785,78.0580198,3,11.000344,78.0674983,3,11.084852, 78.003025, 3, 11.059193, 77.954233, 3, 11.044281, 77.931964, 3, 10.961667, 78.061307, 3,10.895864,78.0282075,3);
		//print_r($d);
		$count=1;
		foreach ($d as $k => $v) {
			if ($count%3 == 1) {
				$d1[] = $v;
			}elseif ($count%3 == 2) {
				$d2[] = $v;
			}else{
				$d3[] = $v;
			}
			$count++;
		}
		//print_r($d1);
		//print_r($d2);
		
		//echo $this->site->GetDrivingDistance_New(13.1241492, 80.1926269, 13.1246303, 80.1933357, 'Km');	
		
		//die;
		$result = array();
		$result_new1111 = '';
		
		for($i=0; $i<count($d1); $i++){
			if($d3[$i] == 3){
				//echo 'a';
				
				/*if(!empty($d1[$i+1]) && $d2[$i+1]){
					$result[] = array('start_lat' => $d1[$i], 'start_lng' => $d2[$i], 'end_lat' => $d1[$i+1], 'end_lng' => $d2[$i+1], 'status' => $d3[$i]);			
				}else{
					$result[] = array('start_lat' => $d1[$i], 'start_lng' => $d2[$i], 'end_lat' => 13.1218483, 'end_lng' => 80.1897365, 'status' => $d3[$i]);				
				}*/
				//echo $d1[$i];
				$result_new1111 .= '%7C'.$d1[$i].'%2C'.$d2[$i];
				//echo $result_new1111;
				//echo '%7C'.$d1[$i].'%2C'.$d2[$i];
			}
		}
		//var_dump($result_new1111);
		//echo json_encode($result_new);
		
		//11.1860923,78.0778515,2,11.1860923,78.0778515,2,11.1860923,78.0778515,2,11.1860923,78.0778515
		
		//echo "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=11.1860923,78.0778515&destinations=".$result_new1111."&key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8";
		die;
		
		//foreach($result as $res){
			//$distance+= $this->site->GetDrivingDistance_New($res['start_lat'], $res['start_lng'], $res['end_lat'], $res['end_lng'], 'Km');	
		//}
		echo $distance;
		die;
	}
	
	function get_tag( $attr, $value, $xml ) {

        $attr = preg_quote($attr);
        $value = preg_quote($value);

        $tag_regex = '/<div[^>]*'.$attr.'="'.$value.'">(.*?)<\\/div>/si';

        preg_match($tag_regex,
        $xml,
        $matches);
        return $matches[1];
    }
	
	function cUrlGetData($url, $post_fields = null, $headers = null) {
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($post_fields && !empty($post_fields)) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    }
    if ($headers && !empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return $data;
}

	/*public function test_get(){
		$html = '
		<div style="width:900px;">
			<div align="left" style="width:450px;"><img src="http://13.233.9.134/assets/uploads/logo/logo.png"  ></div>
			<div align="left" style="width:800px;">
				<p align="right">INVOICE NO:17852642</p>
				<P align="right">15 JUN,2016</p>
			</div>
		</div>
		
		<div align="center"><img src="http://13.233.9.134/assets/uploads/logo/logo.png"  ></div>
		<h2>Trip Recipt :</h2>
		<hr>
		<h3>Customer Details :</h3>
		<p>
			<span style="width:200px;">Name</span> <span style="width:10px"> : </span> <span style="width:"300px">Ananthan</span>			
		</p>
		<p>
			<span style="width:200px;">Name</span> <span style="width:10px"> : </span> <span style="width:"300px">Ananthan</span>			
		</p>
		<p>
			<span style="width:200px;">Name</span> <span style="width:10px"> : </span> <span style="width:"300px">Ananthan</span>			
		</p>
		
		';
		
		//$header ="<div><img src='http://13.233.9.134/assets/uploads/logo/logo.png' ></div>";
		$name = 'download_pdf2.pdf';
		$this->sma->generate_pdf($html, $name, 'S');
		
		$array = array(18.5352911,73.8337237|waypoints=18.5352911,73.8337237|waypoints=18.5352828,73.8336963,2,18.5352435,73.8336493|waypoints=18.5352372,73.8337016|waypoints=18.5352655,73.8337781|waypoints=18.5352208,73.8337294|waypoints=18.5350842,73.8335657|waypoints=18.5346597,73.8335699|waypoints=18.5335547,73.833763|waypoints=18.5324657,73.8340849|waypoints=18.5328528,73.8343388|waypoints=18.5321492,73.8344014|waypoints=18.5329431,73.8339937|waypoints=18.5327358,73.8337579|waypoints=18.5327286,73.8336558|waypoints=18.5324442,73.832776|waypoints=18.5322618,73.8325828|waypoints=18.5319883,73.8325614|waypoints=18.5305023,73.832674|waypoints=18.5301536,73.8326848|waypoints=18.5299605,73.8326955|waypoints=18.5295827,73.8328401|waypoints=18.5294453,73.8327436|waypoints=18.5293502,73.8327042|waypoints=18.5291559,73.8313168|waypoints=18.5291666,73.8313007|waypoints=18.5291041,73.8306665|waypoints=18.5285346,73.8314302|waypoints=18.5276862,73.8299599|waypoints=18.5256273,73.8297448|waypoints=18.5257746,73.829747|waypoints=18.5257422,73.8297338|waypoints=18.5257004,73.8296962|waypoints=18.5256886,73.8296341|waypoints=18.525692,73.8296246|waypoints=18.5256859,73.829672|waypoints=18.5257205,73.8296891|waypoints=18.5257257,73.8298367|waypoints=18.5256252,73.8298597|waypoints=18.5253157,73.8297888|waypoints=18.5249984,73.8297504|waypoints=18.524183,73.8296431|waypoints=18.5231799,73.8296592|waypoints=18.5225201,73.8294446|waypoints=18.5218173,73.8292783|waypoints=18.5207874,73.8290852|waypoints=18.5204548,73.829568|waypoints=18.5201919,73.8308501|waypoints=18.5203046,73.8316602|waypoints=18.5200524,73.8328189|waypoints=18.5201275,73.8331997|waypoints=18.5200363,73.8338113|waypoints=18.5199237,73.8346857|waypoints=18.520031,73.8350022|waypoints=18.519811,73.8356191|waypoints=18.5192317,73.8367885|waypoints=18.5194355,73.8371962|waypoints=18.5196298,73.8378215|waypoints=18.5199881,73.8386446|waypoints=18.5197037,73.8393044|waypoints=18.519634,73.8400769|waypoints=18.5193926,73.8406456|waypoints=18.5194899,73.8406816|waypoints=18.5193916,73.8409347|waypoints=18.5193803,73.8411955|waypoints=18.5195042,73.841209|waypoints=18.5196588,73.8412077|waypoints=18.5198048,73.8412293|waypoints=18.5199445,73.8412058|waypoints=18.5200758,73.8411675|waypoints=18.520237,73.841119|waypoints=18.5204487,73.8410517|waypoints=18.5215062,73.841123|waypoints=18.5215701,73.8411295|waypoints=18.5216712,73.8411026|waypoints=18.5217823,73.8409854|waypoints=18.52198,73.8409639|waypoints=18.5223377,73.8410264|waypoints=18.5229009,73.840844,3);
		
		$count=1;
		
		foreach ($array as $k => $v) {
				
				if ($count%3 == 1) {
					$d1[] = $v;
					$d4[] = array('lat' => $v);
				}elseif ($count%3 == 2) {
					$d2[] = $v;
					$d4[] = array_combine($d4['lat'], array('lng' => $v));
					
				}
				else {
					$d3[] = $v;
				}
			
			
			$count++;
		}
		print_r($d4);
		print_r($d1);
		print_r($d2);
		print_r($d3);
		die;
		$point1_lat = '18.5352911';
		$point1_long = '73.8337237';
		$point2_lat = '18.5229009';
		$point2_long = '73.840844';
		
		die;
		$fare = $this->site->GetDrivingDistance_New($point1_lat, $point1_long, $point2_lat, $point2_long, 'Km');
		print_r($fare);
		die;
		
		
	}*/