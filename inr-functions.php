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
	
	$d = getDayCodeFromDate($lu_date);

	// Select all trains that run on this day
	$sql = "SELECT * FROM TrainInfo WHERE RunsOn LIKE '%$d%'";

	$result = mysql_query($sql, $linkID);

	while($row = mysql_fetch_assoc($result)) {
	
		$train_no = $row['TrainNo'];
		
		createPendingEntries($train_no,$lu_date);
	
	}
	
}

function createPendingEntries($train_no,$lu_date){
	
	global $linkID;
	
	// Find the days on which this train runs
	$sql = "SELECT * FROM TrainInfo WHERE TrainNo = '$train_no'";
	$result = mysql_query($sql, $linkID);
	$row = mysql_fetch_assoc($result);
	$daysOfRun = $row['RunsOn'];

	// Lookup Date	
	$luDate = date("Y-m-d", strtotime($lu_date));

	// Look until 60 days from Lookup Date
	$limitDate = date("Y-m-d",strtotime("+60 days", strtotime($lu_date)));

	// Travel Date
	$date = new DateTime($lu_date);
	$trDate = $date->format('Y-m-d');
	
	$counter = -1;

	while($trDate <= $limitDate){
		
		$d = getDayCodeFromDate($trDate);

		// Check if this train runs on this day.
		if (strpos($daysOfRun, $d) !== FALSE){
			
			$counter++;

			if($counter % 6 == 0){

				$sql = "INSERT INTO PendingQueries (TrainNo, TravelDate, LookupDate, Class, Status) VALUES ('$train_no','$trDate','$luDate','3A','New')";
				$result = mysql_query($sql, $linkID);

			}

		}

		$trDate = date("Y-m-d",strtotime("+1 days", strtotime($trDate)));

	}
}

function getDayCodeFromDate($date){

	// Get Day from date
	$day = date ("D", strtotime($date));
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

	return $d;

}

function getPendingQueries(){

	global $linkID;
	
	$rightNow = new DateTime();
	$tz = new DateTimeZone('Asia/Calcutta');
	$rightNow->setTimezone($tz);
	$now = $rightNow->format("Y-m-d H:i:s");
		
	$today = date("Y-m-d", strtotime($now));
	$ps = date ("Y-m-d H:i:s", strtotime("-1 hour", strtotime($now)));	

	// Get Pending Queries
	$sql = "select q.TrainNo, TravelDate, LookupDate, Class, SourceCode, DestinationCode from PendingQueries as q ".
		"inner join TrainInfo as t on q.TrainNo = t.TrainNo where Status = 'Pending' and LookupDate = '$today' and PendingSince <= '$ps'";

	$result = mysql_query($sql, $linkID);
	
	$output = array();
	
	if(mysql_num_rows($result) > 0){
		
		while($row = mysql_fetch_assoc($result)) {
			$output[] = $row;
		}
	
		// Set Status to Pending
		$rightNow = new DateTime();
		$tz = new DateTimeZone('Asia/Calcutta');
		$rightNow->setTimezone($tz);
		$now = $rightNow->format("Y-m-d H:i:s");

		$query = "UPDATE PendingQueries SET Status = 'Pending', PendingSince = '$now' ".
					"WHERE Status = 'Pending' and PendingSince <= '$ps' and LookupDate = '$today'";

		mysql_query($query, $linkID);
		
	}
	else{
		
	}
	
	// Return the result
	return $output;

}

function getQueryItems(){
	
	global $linkID;

	$rightNow = new DateTime();
	$tz = new DateTimeZone('Asia/Calcutta');
	$rightNow->setTimezone($tz);
	$now = $rightNow->format("Y-m-d H:i:s");
		
	$today = date("Y-m-d", strtotime($now));
	
	// Find a random train number
	$sql = "select distinct q.TrainNo from PendingQueries as q inner join TrainInfo as t on q.TrainNo = t.TrainNo ".
		"where status = 'New' and LookupDate = '$today' order by t.ChartingTime";

	$result = mysql_query($sql, $linkID);
	
	$row = mysql_fetch_assoc($result);
	$train_no = $row['TrainNo'];
	
	// Get Pending Queries for that.
	$sql = "select q.TrainNo, TravelDate, LookupDate, Class, SourceCode, DestinationCode from PendingQueries as q ".
		"inner join TrainInfo as t on q.TrainNo = t.TrainNo where q.TrainNo = '$train_no' and status = 'New' and LookupDate = '$today'";
			
	$result = mysql_query($sql, $linkID);
	
	$output = array();
	
	if(mysql_num_rows($result) > 0){
		
		while($row = mysql_fetch_assoc($result)) {
			$output[] = $row;
		}
	
		// Set Status to Pending
		$rightNow = new DateTime();
		$tz = new DateTimeZone('Asia/Calcutta');
		$rightNow->setTimezone($tz);
		$now = $rightNow->format("Y-m-d H:i:s");

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
			
			// Special Case handling
			if($grossAvType == "REG"){
				// Regret
				$tmpNet = $netAvCount - $YnetAvCount;
				if($tmpNet > 0){
					$grossAvCount = $YgrossAvCount;
				}
				else{
					$grossAvCount = $YgrossAvCount + $tmpNet;
				}
				
			}
			
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
			
			// Special Case handling
			if($grossAvType == "REG"){
				$grossAvCount = 0 - $RACQuota - 300;
			}
			
		}
		
		// Special case
		if($bookings < 0){
			$bookings = 0;
		}
		if($cancellations < 0){
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
				
		mysql_query($sql, $linkID);
	}
	
	return $result;

}

function calculateProbability($train_no,$tr_date,$tr_class,$curr_status){
	
	global $linkID;

	// Select RAC Quota
	$quotaSQL = "SELECT * FROM TrainQuota where TrainNo = '$train_no' and Class = '$tr_class'";
	$quotaResult = mysql_query($quotaSQL, $linkID);
	$quotaRow = mysql_fetch_assoc($quotaResult);
		
	$RACQuota = $quotaRow['RACQuota'];

	// Calculate the current deficit
	$curr_status = str_replace(" ", "", $curr_status);

	$currStatus = explode("/", $curr_status);

	$netAvail = $currStatus[1];
	$avCount = 0;
	
	if (strpos($netAvail, 'WL') !== FALSE){
		$avType = "WL";
		$pos = strpos($netAvail, 'WL') + 2;
		$avCount = $RACQuota + substr($netAvail,$pos);
	}

	if (strpos($netAvail, 'RAC') !== FALSE){
		$avType = "RAC";
		$pos = strpos($netAvail, 'RAC') + 3;
		$avCount = substr($netAvail,$pos);
	}

	if (strpos($netAvail, 'AVAILABLE') !== FALSE){
		$avType = "CNF";
		$avCount = 0;
	}
	
	// Calculate Date Diff
	$now = time(); // Now
    $trDate = strtotime($tr_date);
    $diff = $trDate - $now;
	$days = floor($diff / (24*60*60));

	//echo $days;

	$sql = "select t.TrainNo, t.Class, t.TravelDate, sum(l.Cancellations) as Cancellations ".
		"from (select distinct TrainNo, Class, TravelDate from AvailabilityInfo where TrainNo = '$train_no' and Class = '$tr_class') as t ".
		"inner join AvailabilityInfo as l on t.TrainNo = l.TrainNo and t.Class = l.class ".
		"and t.TravelDate = l.TravelDate and l.LookupDate >= (t.TravelDate - $days) and l.LookupDate < t.TravelDate ".
		"where t.TrainNo = '$train_no' and t.Class = '$tr_class' group by t.TrainNo, t.Class, t.TravelDate";
	
	echo $sql;
	
	$result = mysql_query($sql, $linkID);

	$CNF_Deficit = $avCount;
	$RAC_Deficit = $CNF_Deficit - $RACQuota;

	$CNF_Count = 0;
	$RAC_Count = 0;
	$Total = 0;

	while($row = mysql_fetch_assoc($result)) {
			
		$cancellations = $row['Cancellations'];

		if($cancellations >= $CNF_Deficit){
			$CNF_Count++;
		}

		if($cancellations >= $RAC_Deficit){
			$RAC_Count++;
		}

		$Total++;
		
	}
	
	$RAC_Prob = 100 * $RAC_Count / $Total;
	$CNF_Prob = 100 * $CNF_Count / $Total;

	$output = array("RAC" => $RAC_Prob, "CNF" => $CNF_Prob);
	return $output;

}

function deleteOldEntries(){
	
	global $linkID;
	
	// Yesterday
	$yesterday = date ("Y-m-d", strtotime("-1 days", strtotime("now")));

	$sql = "DELETE FROM PendingQueries WHERE LookupDate < '$yesterday' AND Status = 'Finished'";
	$result = mysql_query($sql, $linkID);
	
	$dby = date ("Y-m-d", strtotime("-2 days", strtotime("now")));

	$sql = "DELETE FROM PendingQueries WHERE LookupDate < '$dby' AND Status = 'Pending'";
	$result = mysql_query($sql, $linkID);
	
}

?>
