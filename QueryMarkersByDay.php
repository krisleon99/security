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

//$mes = $_GET['mes_q'];
$mesAndYear = $_GET['mes_q'];
$dateStart = "";
$year = "";
$mes = "";
$day = "";
list($day,$mes, $year) = split('[/.-]', $mesAndYear);
$dateStart = "".$mes."/".$day."/".$year;
$dateEnd = "31/".$day."/".$year;
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
 
$cat = $_GET['type'];
//$cat = 'todas';
$tipos = ["transeunte", "vehiculo", "negocio"];
$table = $tipos[$cat];

// Select all the rows in the markers table
	//$query = "SELECT * FROM $table, ST_X(ST_Transform($table.geom,4326)) as lng, ST_Y(ST_Transform($table.geom,4326)) as lat where mes like '$mes' and fecha BETWEEN '$dateStart' AND '$dateEnd' order by fecha asc";
	$query = "SELECT * FROM transeunte, ST_X(ST_Transform($table.geom,4326)) as lng, ST_Y(ST_Transform($table.geom,4326)) as lat where mes like '$mes' and fecha ='$dateStart' order by fecha asc";

$result = pg_query($con, $query);
if (!$result) {
  die('Invalid query: ' . pg_error());
}

$markers = array();
$allData = array();
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

$query = "select count(id) as num from $table where mes like '$mes' and fecha between '03/01/2016' and '03/31/2016' group by fecha order by num desc limit 1";
$result = pg_query($con, $query);
if (!$result) {
    die('Invalid query: ' . pg_error());
}
while ($row = pg_fetch_assoc($result)){
    $numMax = $row['num'];
}

$allData['data']=$markers;
$allData['max']=$numMax;
//echo json_encode($markers, JSON_NUMERIC_CHECK);
echo json_encode($allData, JSON_NUMERIC_CHECK);
?>