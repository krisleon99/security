<?php
/*
 * Copyright 2016- All rights reserved.
 * Rodrigo Tapia-McClung
 * 
 * May 2016
 */

require_once ('dbc.php');
$con = con('ags');

date_default_timezone_set('America/Mexico_City');

session_name('agsLogin');
session_start();

//$cat = $_GET['cat'];

$cat = 'todas';

// Select all the rows in the markers table
if ($cat != 'todas') {
	$query = "SELECT * FROM $cat, ST_X(ST_Transform($cat.geom,4326)) as lng, ST_Y(ST_Transform($cat.geom,4326)) as lat order by fecha asc";
} else {
	$query = "SELECT * FROM transeunte, ST_X(ST_Transform(transeunte.geom,4326)) as lng, ST_Y(ST_Transform(transeunte.geom,4326)) as lat order by fecha asc";
}

$result = pg_query($con, $query);
if (!$result) {
  die('Invalid query: ' . pg_error());
}

$markers = array();

while ($row = pg_fetch_assoc($result)){
	if (isset($_SESSION['type']) && $_SESSION['type'] == 'admin'){
		$markers[$row['id']] = array('folio'=>$row['folio'],
										'fecha'=>date($row['fecha']),
										'mes'=>$row['mes'],
										//'dia_sem'=>$row['dia_sem'], // solo para transeunte y negocio
										//'hora'=>$row['hora'], // no esta para negocio
										//'minuto'=>$row['minuto'], // minutos para vehiculo y no esta para negocio
										'lat'=>$row['lat'],
										'lng'=>$row['lng']);
		                    
		                                   
	} else {
		$markers[$row['id']] = array('folio'=>$row['folio'],
										'fecha'=>date($row['fecha']),
										'mes'=>$row['mes'],
										//'dia_sem'=>$row['dia_sem'],
										//'hora'=>$row['hora'],
										//'minuto'=>$row['minuto'],
										'lat'=>$row['lat'],
										'lng'=>$row['lng']);
		 
	}
}
echo json_encode($markers, JSON_NUMERIC_CHECK);
?>