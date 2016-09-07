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

$mes = $_GET['mes_q'];
/*$mesAndYear = $_GET['mes_q'];
$dateStart = "";
$dateEnd = "";
$year = "";
$mes = "";
list($mes, $year) = split('[/.-]', $mesAndYear);
$dateStart = "01/01/".$year;
$dateEnd = "12/01/".$year;*/
switch ($mes) {
    case "1":
        $mes = "ENERO";
        break;
    case "2":
        $mes = "FEBRERO";
        break;
    case "3":
        $mes = "MARZO";
        break;
    case "4":
        $mes = "ABRIL";
        break;
    case "5":
        $mes = "MAYO";
        break;
    case "6":
        $mes = "JUNIO";
        break;
    case "7":
        $mes = "JULIO";
        break;
    case "8":
        $mes = "AGOSTO";
        break;
    case "9":
        $mes = "SEPTIEMBRE";
        break;
    case "10":
        $mes = "OCTUBRE";
        break;
    case "11":
        $mes = "NOVIEMBRE";
        break;
    case "12":
        $mes = "DICIEMBRE";
        break;
}
 

$cat = 'todas';


// Select all the rows in the markers table
if ($cat != 'todas') {
	$query = "SELECT * FROM $cat, ST_X(ST_Transform($cat.geom,4326)) as lng, ST_Y(ST_Transform($cat.geom,4326)) as lat order by fecha asc";
} else {
	$query = "SELECT * FROM transeunte, ST_X(ST_Transform(transeunte.geom,4326)) as lng, ST_Y(ST_Transform(transeunte.geom,4326)) as lat where mes like '$mes' order by fecha asc";
	//$query = "SELECT * FROM transeunte, ST_X(ST_Transform(transeunte.geom,4326)) as lng, ST_Y(ST_Transform(transeunte.geom,4326)) as lat where mes like '$mes' and fecha BETWEEN '$dateStart' AND '$dateEnd' order by fecha asc";
	
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