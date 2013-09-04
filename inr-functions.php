<?php

require_once( 'config.php' );

function createDBConnection(){
	
	global $linkID;
	$host = DB_HOST;
	$user = DB_USER;
	$pass = DB_PASSWORD;
	$database = DB_NAME;

	$linkID = mysql_connect($host, $user, $pass) or die("Could not connect to host.");
	mysql_select_db($database, $linkID) or die("Could not find database.");

}

function createPendingEntriesForDate($lu_date){
	
	global $linkID;

	// Get Day from date
	$day = date ("D", strtotime($lu_date));
	$d = "0";

	switch ($day) {
	
    		case "Mon": $d = "1"; break;
		case "Tue": $d = "2"; break;
		case "Wed": $d = "3"; break;
		case "Thu": $d = "4"; break;
		case "Fri": $d = "5"; break;
		case "Sat": $d = "6"; break;
		case "Sun": $d = "7"; break;

	}

	// Select all trains that run on this day
	$sql = "SELECT * FROM TrainInfo WHERE RunsOn LIKE '%$d%'";

	$result = mysql_query($sql, $linkID);

	while($row = mysql_fetch_assoc($result)) {
	
		$train_no = $row['TrainNo'];
		
		for($i=0; $i<10; $i++){
		
			$days = $i * 6;
			$dt = strtotime("+".$days." days", strtotime($lu_date));
			$date = date("Y-m-d", $dt);
			$luDate = date("Y-m-d", strtotime($lu_date));
		
			$insert = "INSERT INTO PendingQueries (TrainNo, TravelDate, LookupDate, Class, Status) VALUES ('$train_no','$date','$luDate','3A','New')";
			mysql_query($insert, $linkID);
	
		}
	
	}
	
}

function createPendingEntries($train_no,$lu_date){
	
	global $linkID;

	for($i=0; $i<10; $i++){
		
		$days = $i * 6;
		$dt = strtotime("+".$days." days", strtotime($lu_date));
		$date = date("Y-m-d", $dt);
		$luDate = date("Y-m-d", strtotime($lu_date));
		
		$sql = "INSERT INTO PendingQueries (TrainNo, TravelDate, LookupDate, Class, Status) VALUES ('$train_no','$date','$luDate','3A','New')";
		$result = mysql_query($sql, $linkID);
	
	}
}

function getPendingQueries(){

	global $linkID;
	
	$today = date("Y-m-d", strtotime("now"));
	$ps = date ("Y-m-d H:i:s", strtotime("-1 hour", strtotime("now")));	

	// Get Pending Queries
	$sql = "select q.TrainNo, TravelDate, LookupDate, Class, SourceCode, DestinationCode from PendingQueries as q ".
		"inner join TrainInfo as t on q.TrainNo = t.TrainNo where status = 'Pending' and LookupDate = '$today' and PendingSince <= '$ps'";

	$result = mysql_query($sql, $linkID);
	
	$output = array();
	
	if(mysql_num_rows($result) > 0){
		
		while($row = mysql_fetch_assoc($result)) {
			$output[] = $row;
		}
	
		// Set Status to Pending
		$now = date ("Y-m-d H:i:s");
		$today = date ("Y-m-d");
		$query = "UPDATE PendingQueries SET Status = 'Pending', PendingSince = '$now' ".
					"WHERE PendingSince <= '$ps' and LookupDate = '$today'";

		mysql_query($query, $linkID);
		
	}
	else{
		
	}
	
	// Return the result
	return $output;

}

function getQueryItems(){
	
	global $linkID;
	
	// Find a random train number
	$sql = "select distinct TrainNo from PendingQueries where status = 'New'";
	$result = mysql_query($sql, $linkID);
	
	$row = mysql_fetch_assoc($result);
	$train_no = $row['TrainNo'];
	
	// Get Pending Queries for that.
	$sql = "select q.TrainNo, TravelDate, LookupDate, Class, SourceCode, DestinationCode from PendingQueries as q ".
		"inner join TrainInfo as t on q.TrainNo = t.TrainNo where q.TrainNo = '$train_no' and status = 'New' and LookupDate = current_date";
			
	$result = mysql_query($sql, $linkID);
	
	$output = array();
	
	if(mysql_num_rows($result) > 0){
		
		while($row = mysql_fetch_assoc($result)) {
			$output[] = $row;
		}
	
		// Set Status to Pending
		$now = date ("Y-m-d H:i:s");
		$today = date ("Y-m-d");
		$query = "UPDATE PendingQueries SET Status = 'Pending', PendingSince = '$now' ".
					"WHERE TrainNo = '$train_no' and LookupDate = '$today'";
		
		mysql_query($query, $linkID);
		
	}
	else{
		
	}
	
	// Return the result
	return $output;
}

function saveAvailabilityData($availData){
	
	global $linkID;
	
	foreach ($availData as $availDataRow){
		
		// The input
		$trainNo = $availDataRow['TrainNo'];
		$travelDate = $availDataRow['JourneyDate'];
		$lookupDate = $availDataRow['LookupTimeStamp'];
		$Class = $availDataRow['JClass'];
		$Availability = $availDataRow['Availability'];
		
		// Get Previous Date
		$ydt = strtotime("-1 days", strtotime($lookupDate));
		$yesterday = date("Y-m-d", $ydt);
		
		// Select RAC Quota
		$quotaSQL = "SELECT * FROM TrainQuota where TrainNo = '$trainNo' and Class = '$Class'";
		$quotaResult = mysql_query($quotaSQL, $linkID);
		$quotaRow = mysql_fetch_assoc($quotaResult);
		
		$RACQuota = $quotaRow['RACQuota'];
		
		// Calculate Avail on Absolute Scale !
		$grossAvType = $availDataRow['GrossAvType'];
		$grossAvCount = $availDataRow['GrossAvCount'];
		$netAvType = $availDataRow['NetAvType'];
		$netAvCount = $availDataRow['NetAvCount'];
		
		if($grossAvType == "RAC"){
			$grossAvCount = 0 - $grossAvCount;
		}
		
		if($grossAvType == "WL"){
			$grossAvCount = 0 - $RACQuota - $grossAvCount;
		}
		
		if($netAvType == "RAC"){
			$netAvCount = 0 - $netAvCount;
		}
		
		if($netAvType == "WL"){
			$netAvCount = 0 - $RACQuota - $netAvCount;
		}
		
		$sql = "SELECT * FROM AvailabilityInfo WHERE TrainNo = '$trainNo' and TravelDate = '$travelDate'".
				"and Class = '$Class' order by LookupDate desc limit 1";
		
		//echo $sql . "<br>";
		$result = mysql_query($sql, $linkID);
		
		if(mysql_num_rows($result) > 0){
			
			$row = mysql_fetch_assoc($result);
		
			$YgrossAvType = $row['GrossAvType'];
			$YgrossAvCount = $row['GrossAvCount'];
			$YnetAvType = $row['NetAvType'];
			$YnetAvCount = $row['NetAvCount'];

			// Bookings
			if($YgrossAvCount > $grossAvCount){
				$bookings = $YgrossAvCount - $grossAvCount;
			}
			else{
				$bookings = 0;
			}
		
			// Cancellations
			if($YgrossAvCount < $grossAvCount){
				$cancellations = $grossAvCount - $YgrossAvCount;
			}
			else{
				$cancellations = ($YgrossAvCount - $grossAvCount) - ($YnetAvCount - $netAvCount);
			}
		
		}
		else{
		
			$YgrossAvType = "";
			$YgrossAvCount = 0;
			$YnetAvType = "";
			$YnetAvCount = 0;

			$bookings = 0;
			$cancellations = 0;
			
		}
		
		// Save Avail Data
		$sql = "INSERT INTO AvailabilityInfo ".
				"(TrainNo,TravelDate,LookupDate,Class,Availability,GrossAvType,GrossAvCount,NetAvType,NetAvCount,Bookings,Cancellations) ".
				"VALUES ('$trainNo','$travelDate','$lookupDate','$Class','$Availability','$grossAvType','$grossAvCount',".
				"'$netAvType','$netAvCount','$bookings','$cancellations')";
		
		$result = mysql_query($sql, $linkID);
		
		// Update Pending Status
		$luDate = date("Y-m-d", strtotime($lookupDate));
		
		$sql = "UPDATE PendingQueries SET Status = 'Finished' WHERE TrainNo = '$trainNo' and TravelDate = '$travelDate' ".
				"and LookupDate = '$luDate' and Class = '$Class'";
				
		$result = mysql_query($sql, $linkID);
	}
	
	return $result;

}

?>
