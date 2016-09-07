<?php
/*
 * Copyright 2016 - All rights reserved.
 * Rodrigo Tapia-McClung
 *
 * May, 2016
 */
require_once ('dbc.php');
$con = con('ags');

session_name('agsLogin');
session_start();

// Select all the geometry in the table
//$query = "SELECT mslink, text, ST_AsGeoJSON(ST_Transform(predios.the_geom,4326)) as geom FROM predios";

$query = "SELECT row_to_json(fc)
 FROM ( SELECT 'FeatureCollection' As type, array_to_json(array_agg(f)) As features
 FROM (SELECT 'Feature' As type
	, ST_AsGeoJSON(ST_Transform(lg.geom,4326))::json As geometry
	, row_to_json(lp) As properties
	FROM transeunte As lg 
		INNER JOIN (SELECT id, fecha as Folio, fecha as Fecha, mes as Mes FROM transeunte) As lp 
		ON lg.id = lp.id) As f )  As fc;";

$result = pg_query($con, $query);
if (!$result) {
  die('Invalid query: ' . pg_last_error());
}

while ($row = pg_fetch_row($result)){
	/*foreach($row as $i => $attr){
		echo $attr.", ";
	}
	echo ";";*/
	header('Content-type: application/json');
	echo $row[0];
}

?>