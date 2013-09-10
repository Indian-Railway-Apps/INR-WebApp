<?php

require( 'inr-functions.php' );

$avail_data = $_POST['avail_data'];
$avail_data = stripslashes($avail_data);

createDBConnection();

$availData = json_decode($avail_data,true);

$output = saveAvailabilityData($availData);

if($output){
	echo "Success";
}
else{
	echo "Failed";
}

?>
