<?php
require 'vendor/autoload.php';

EasyRdf_Namespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
EasyRdf_Namespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
EasyRdf_Namespace::set('dct', 'http://purl.org/dc/terms/');
EasyRdf_Namespace::set('dc', 'http://purl.org/dc/elements/1.1/');
EasyRdf_Namespace::set('foaf', 'http://xmlns.com/foaf/0.1/');
EasyRdf_Namespace::set('sem', 'http://semanticweb.cs.vu.nl/2009/11/sem/');

$sparql = new EasyRdf_Sparql_Client('https://api.data.adamlink.nl/datasets/AdamNet/all/services/endpoint/sparql');

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

