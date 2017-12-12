<?php
require 'vendor/autoload.php';

EasyRdf_Namespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
EasyRdf_Namespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
EasyRdf_Namespace::set('sem', 'http://semanticweb.cs.vu.nl/2009/11/sem/');
EasyRdf_Namespace::set('hg', 'http://rdf.histograph.io/');
EasyRdf_Namespace::set('geo', 'http://www.opengis.net/ont/geosparql#');
$sparql = new EasyRdf_Sparql_Client('https://api.adamnet.triply.cc/datasets/menno/straten/containers/test/sparql');

 $result = $sparql->query(
    	"SELECT ?straat ?label ?wkt ?begin ?end {
		  ?straat sem:hasEarliestEndTimeStamp ?end ;
		          sem:hasEarliestBeginTimeStamp ?begin ;
		          a hg:Street ;
		          geo:hasGeometry/geo:asWKT ?wkt ;
		          rdfs:label ?label .
		  FILTER (year(xsd:dateTime(?end)) < 2017)
		} 
		"
);

$fc = array("type"=>"FeatureCollection","features"=>array());

foreach ($result as $row) {
	$street = array("type"=>"Feature");
	foreach ($row as $k => $v) {
		if($k!="wkt"){
			$street['properties'][$k]=(string)$v;
		}
	}
	$street['geometry'] = wkt2geojson($row->wkt);
	$fc['features'][] = $street;
}

$json = json_encode($fc);

file_put_contents('geojson.geojson', $json);

die($json);

//echo "Total number of countries:" . $result->numRows();

function wkt2geojson($wkt){
	$coordsstart = strpos($wkt,"(");
	$type = trim(substr($wkt,0,$coordsstart));
	$coordstring = substr($wkt, $coordsstart);

	switch ($type) {
	    case "LINESTRING":
	    	$geom = array("type"=>"LineString","coordinates"=>array());
			$coordstring = str_replace(array("(",")"), "", $coordstring);
	    	$pairs = explode(",", $coordstring);
	    	foreach ($pairs as $k => $v) {
	    		$coords = explode(" ", $v);
	    		$geom['coordinates'][] = array((double)$coords[0],(double)$coords[1]);
	    	}
	    	return $geom;
	    	break;
	    case "MULTILINESTRING":
	    	$geom = array("type"=>"MultiLineString","coordinates"=>array());
	    	preg_match_all("/\([0-9. ,]+\)/",$coordstring,$matches);
	    	//print_r($matches);
	    	foreach ($matches[0] as $linestring) {
	    		$linestring = str_replace(array("(",")"), "", $linestring);
		    	$pairs = explode(",", $linestring);
		    	$line = array();
		    	foreach ($pairs as $k => $v) {
		    		$coords = explode(" ", $v);
		    		$line[] = array((double)$coords[0],(double)$coords[1]);
		    	}
		    	$geom['coordinates'][] = $line;
	    	}
	    	return $geom;
	    	break;
	    case "POINT":
			$coordstring = str_replace(array("(",")"), "", $coordstring);
	    	$coords = explode(" ", $coordstring);
	    	print_r($coords);
	    	$geom = array("type"=>"Point","coordinates"=>array((double)$coords[0],(double)$coords[1]));
	    	return $geom;
	        break;
	}
}
