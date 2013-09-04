<?php

header("Content-type: text/json");

require( 'inr-functions.php' );

$input = $_POST['input'];

createDBConnection();

if($input == "Correction"){
	// For Correction
	$output = getPendingQueries();
}
else{
	// Get Items for Query
	$output = getQueryItems();
}

// Output
echo json_encode($output);

?>
