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

$date = $_GET['date'];
if($date == ""){
	
	$rightNow = new DateTime();
	$tz = new DateTimeZone('Asia/Calcutta');
	$rightNow->setTimezone($tz);
	$now = $rightNow->format("Y-m-d H:i:s");
		
	$date = date("Y-m-d", strtotime($now));

}


// Get Stats
$sql = "SELECT q.TrainNo, q.Status, Count(*) AS C FROM PendingQueries as q INNER JOIN TrainInfo as t on q.TrainNo = t.TrainNo".
	" WHERE LookupDate = '$date' GROUP BY TrainNo, Status order by t.ChartingTime";

$result = mysql_query($sql, $linkID);

echo $date . "<br>";

while($row = mysql_fetch_assoc($result)) {
	
	echo "Train No: " . $row['TrainNo'] . " Status: " . $row['Status'] . " Count: " . $row['C'] . "<br>";

}

?>
