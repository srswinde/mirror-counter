<?php
$host = "localhost"; 
$user = "counter"; 
$pass = ""; 
$db = "counter"; 

if ( array_key_exists("deltaT", $_GET) )
{
	$units = substr( $_GET['deltaT'], strlen($_GET['deltaT'])-1 );
	$time = substr( $_GET['deltaT'], 0, strlen($_GET['deltaT'])-1 );
	
	switch($units)
	{
		case 'm':
			$timeStr= "$time minutes";
			break;
		case 's':
			$timeStr= "$time seconds";
			break;
		case 'h':
			$timeStr= "$time hours";
			break;
		default:
			$timeStr = "10 minutes";
	}		
}
else
{
	$timeStr = "10 minutes";
}

$con = pg_connect("host=$host dbname=$db user=$user password=$pass")
    or die ("Could not connect to server\n"); 

$query = "SELECT * FROM pulses where ((now()-time) < interval '$timeStr')"; 

$rs = pg_query($con, $query) or die("Cannot execute query: $query\n");

$rows = [];

while ($row = pg_fetch_row($rs)) {
	$date = new DateTime($row[0]);
	$dateStr = $date->format("Y-m-d");
	$dateStr= $dateStr."T".substr( $date->format("H:i:s.u"), 0, -3)."Z";
	
	$rows[] = array($dateStr, $row[1]);
}

echo json_encode( $rows ) ;

pg_close($con); 

php?>
