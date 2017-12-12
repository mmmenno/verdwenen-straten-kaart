<?php
require 'vendor/autoload.php';

EasyRdf_Namespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
EasyRdf_Namespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
EasyRdf_Namespace::set('dct', 'http://purl.org/dc/terms/');
EasyRdf_Namespace::set('dc', 'http://purl.org/dc/elements/1.1/');
EasyRdf_Namespace::set('foaf', 'http://xmlns.com/foaf/0.1/');
EasyRdf_Namespace::set('sem', 'http://semanticweb.cs.vu.nl/2009/11/sem/');

$sparql = new EasyRdf_Sparql_Client('https://api.adamnet.triply.cc/datasets/menno/straten/containers/test/sparql');


 $result = $sparql->query(
    	"SELECT * WHERE {
			?bbitem dct:spatial <" . $_GET['streeturi'] . "> .
			?bbitem foaf:Depiction ?imgurl .
			?bbitem dc:type ?type .
  			?bbitem sem:hasBeginTimeStamp ?year .
			FILTER(?type NOT IN(\"bouwtekening\",\"kaart\"))
			} 
			ORDER BY ?year
			LIMIT 20
		"
);

if($result->numRows()){
	echo '<div id="pics">';
	foreach ($result as $row) {
		echo '<a title="' . substr($row->year,0,4) . '" target="_blank" href="' . $row->bbitem . '">';
		echo '<img src="' . $row->imgurl . '">';
		echo '</a>';
		echo '<span>' . substr($row->year,0,4) . '</span>';
	}
	echo '</div>';
}

