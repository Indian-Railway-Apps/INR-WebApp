<?php
/*
	Delete old entries
	Create Entries for Day after tomorrow
*/
require_once( 'config.php' );

$linkID;
$host = DB_HOST;
$user = DB_USER;
$pass = DB_PASSWORD;
$database = DB_NAME;

$linkID = mysql_connect($host, $user, $pass) or die("Could not connect to host.");
mysql_select_db($database, $linkID) or die("Could not find database.");

$today = date("Y-m-d",strtotime("now"));

// Get Stats
$sql = "SELECT TrainNo, Status, Count(*) AS C FROM PendingQueries WHERE LookupDate = '$today' GROUP BY TrainNo, Status";
$result = mysql_query($sql, $linkID);

echo $today . "<br>";

while($row = mysql_fetch_assoc($result)) {
	
	echo "Train No: " . $row['TrainNo'] . " Status: " . $row['Status'] . " Count: " . $row['C'] . "<br>";

}

?>
