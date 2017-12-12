<!DOCTYPE html>
<html>
<head>
	
	<title>Verdwenen straten Amsterdam</title>

	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link href="https://fonts.googleapis.com/css?family=Nunito:300,700" rel="stylesheet">

	<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.1.0/dist/leaflet.css" integrity="sha512-wcw6ts8Anuw10Mzh9Ytw4pylW8+NAD4ch3lqm9lzAsTxg0GFeJgoAtxuCLREZSC5lUXdVyo/7yfsqFjQ4S+aKw==" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.1.0/dist/leaflet.js" integrity="sha512-mNqn2Wg7tSToJhvHcqfzLMU6J4mkOImSPTxVZAdo+lcPlk+GhZmYgACEe0x35K7YzW1zJ7XyJV/TT1MrdXvMcA==" crossorigin=""></script>


	<style>
		html, body{
			height: 100%;
			margin:0;
		}
		#map {
			width: 100%;
			height: 100%;
		}
		.leaflet-left .leaflet-control{
			margin-top: 30px;
			margin-left: 20px;
		}
		.leaflet-container .leaflet-control-attribution{
			background-color:#000;
			color: #FD6368;
		}
		.leaflet-control-attribution a{
			color: #FD6368;
		}
		#info{
			color: #FD6368;
			position: absolute;
			z-index: 1000;
			right: 30px;
			top: 20px;
			text-align: right;
			font-family: 'Nunito', sans-serif;
		}
		#info a, #info a:visited, #info a:hover{
			color: #FD6368;
			text-decoration: none;
		}
		#pics span{
			text-shadow: 0 0 6px #fff;
		}
		#info h1{
			margin: 0 0 0 0;
			font-size: 38px;
		}
		#info p.years{
			margin:0;
			font-size: 28px;
			font-weight: 300;
		}
		#pics{
			width: 400px;
			position: absolute;
			z-index: 10000;
			bottom: 0;
			right: 30px;
			top:120px;
			overflow-y: scroll;

		}
		#pics img{
			width: 100%;
			margin-bottom: 20px;
		}
		#pics span{
			margin-top: -80px;
			margin-bottom: 40px;
			margin-left: 310px;
			font-family: 'Nunito', sans-serif;
			font-weight: 700;
			font-size: 28px;
			z-index: 10001;
			display: block;
		}
	</style>

	
</head>
<body>

<div id="album"></div>
<div id='map'>
</div>

<div id="info">
	<h1>Verdwenen Straten Amsterdam</h1>

	<p class="years"></p>

	
</div>





<script>
	var map = L.map('map',{
		attributionControl: false
	}).setView([52.370216, 4.895168], 13);

	L.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/dark_nolabels/{z}/{x}/{y}.png', {
		maxZoom: 20,
		attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="http://cartodb.com/attributions">CartoDB</a>',
		id: 'CartoDB.DarkMatterNoLabels',
		maxZoom: 19
	}).addTo(map);

	L.control.attribution({position: 'bottomleft'}).addTo(map);

	var streets = L.geoJson(null, {
	    style: function(feature) {
	        return {
	            color: "#FD6368",
	            weight: 7,
	            opacity: 1,
	            clickable: true
	        };
	    },
	    onEachFeature: function(feature, layer) {
			layer.on({
		        click: whenClicked
		    });
	    },
	    //
        pointToLayer: function (feature, latlng) {
			return L.circleMarker(latlng, {
				radius: 6,
				fillColor: "#fff",
				color: "#FEA3AF",
				weight: 5,
				opacity: 1,
				fillOpacity: 0.8
			});
		}
	}).addTo(map);

	geojsonfile = 'geojson.geojson';
	
	$.getJSON(geojsonfile, function(data) {
        streets.addData(data);
    });

    function whenClicked(e) {
    	streets.setStyle({color: "#FD6368"});
    	$('#album').html('');
        this.setStyle({color: "#fff"});
		var props = e['target']['feature']['properties'];
		$('#info h1').html('<a target="_blank" href="' + props.straat + '">' + props.label + '</a>');
		$('#info p.years').html(props.begin + ' - ' + props.end);
		var img = e['target']['feature']['properties']['img'];
    	var id = e['target']['feature']['properties']['id'];
		$('#album').load('straat.php?streeturi=' + props.straat);
	  	
	}

</script>



</body>
</html>
