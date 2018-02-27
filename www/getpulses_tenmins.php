<?php
$host = "localhost"; 
$user = "counter"; 
$pass = ""; 
$db = "counter"; 

$con = pg_connect("host=$host dbname=$db user=$user password=$pass")
    or die ("Could not connect to server\n"); 

$query = "SELECT * FROM pulses where ((now()-time) < interval '10 minutes')"; 

$rs = pg_query($con, $query) or die("Cannot execute query: $query\n");

$rows = [];

while ($row = pg_fetch_row($rs)) {
	$rows[] = $row;
}

echo json_encode( $rows ) ;

pg_close($con); 

php?>
