<?php

$sparqlquery = "
PREFIX dct: <http://purl.org/dc/terms/>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
PREFIX hg: <http://rdf.histograph.io/>
PREFIX geo: <http://www.opengis.net/ont/geosparql#>
PREFIX dc: <http://purl.org/dc/elements/1.1/>
PREFIX sem: <http://semanticweb.cs.vu.nl/2009/11/sem/>
PREFIX void: <http://rdfs.org/ns/void#>

SELECT * WHERE {
		?bbitem dct:spatial <" . $_GET['streeturi'] . "> .
		?bbitem foaf:depiction ?imgurl .
		?bbitem dc:type ?type .
			OPTIONAL { ?bbitem sem:hasBeginTimeStamp ?year } .
		FILTER(?type NOT IN(\"bouwtekening\",\"kaart\"))
	} 
	ORDER BY ?year
	LIMIT 20
";

$url = "https://api.druid.datalegend.net/datasets/adamnet/all/services/endpoint/sparql?query=" . urlencode($sparqlquery) . "";

$querylink = "https://druid.datalegend.net/AdamNet/all/sparql/endpoint#query=" . urlencode($sparqlquery) . "&endpoint=https%3A%2F%2Fdruid.datalegend.net%2F_api%2Fdatasets%2FAdamNet%2Fall%2Fservices%2Fendpoint%2Fsparql&requestMethod=POST&outputFormat=table";


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch,CURLOPT_USERAGENT,'adamlink');
$headers = [
  'Accept: application/sparql-results+json'
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$json = curl_exec ($ch);
curl_close ($ch);

$data = json_decode($json,true);
//print_r($sparqlquery);
//die;

echo '<div id="pics">';
foreach ($data['results']['bindings'] as $row) {
	echo '<div class="pic">';
	echo '<a target="_blank" href="' . $row['bbitem']['value'] . '">';
	echo '<img src="' . $row['imgurl']['value'] . '">';
	echo '</a>';
	if(isset($row['year']['value'])){
		$year = substr($row['year']['value'],0,4);
	}else{
		$year = "????";
	}
	echo '<span>' . $year . '</span>';
	echo '</div>';
}
echo '</div>';

/*
require 'vendor/autoload.php';

EasyRdf_Namespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
EasyRdf_Namespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
EasyRdf_Namespace::set('dct', 'http://purl.org/dc/terms/');
EasyRdf_Namespace::set('dc', 'http://purl.org/dc/elements/1.1/');
EasyRdf_Namespace::set('foaf', 'http://xmlns.com/foaf/0.1/');
EasyRdf_Namespace::set('sem', 'http://semanticweb.cs.vu.nl/2009/11/sem/');

$sparql = new EasyRdf_Sparql_Client('https://api.druid.datalegend.net/datasets/adamnet/all/services/endpoint/sparql');

$sparqlquery = "SELECT * WHERE {
						?bbitem dct:spatial <" . $_GET['streeturi'] . "> .
						?bbitem foaf:depiction ?imgurl .
						?bbitem dc:type ?type .
			  			OPTIONAL { ?bbitem sem:hasBeginTimeStamp ?year } .
						FILTER(?type NOT IN(\"bouwtekening\",\"kaart\"))
					} 
					ORDER BY ?year
					LIMIT 20
				";

$result = $sparql->query($sparqlquery);


if($result->numRows()){
	echo '<div id="pics">';
	foreach ($result as $row) {
		echo '<div class="pic">';
		echo '<a title="' . substr($row->year,0,4) . '" target="_blank" href="' . $row->bbitem . '">';
		echo '<img src="' . $row->imgurl . '">';
		echo '</a>';
		if(isset($row->year)){
			$year = substr($row->year,0,4);
		}else{
			$year = "????";
		}
		echo '<span>' . $year . '</span>';
		echo '</div>';
	}
	echo '</div>';
}

*/