<?php

// This file contains the database access information. 
// This file also establishes a connection to PosgreSQL 
// and selects the database.

function con($db) {
	
	$conn=pg_connect("host=dev.centrogeo.org.mx dbname=$db user=postgres password=r3cur505hum4n05.")
	or die("<p>Error de conexi&oacute;n: " . pg_last_error() . "</p>");
		
	return $conn;
}?>
