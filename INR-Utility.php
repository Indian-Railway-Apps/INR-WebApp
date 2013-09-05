<?php

require( 'inr-functions.php' );

$code = $_POST['code'];
$pwd = $_POST['pwd'];

if($pwd != SECRET_KEY){
	die("<br>Wrong password. Please try again");
}

createDBConnection();

if($code == "Pending-Queries-Train-Date"){

	$train_no = $_POST['train_no'];
	$lu_date = $_POST['lu_date'];
	
	// Create Pending Entries
	createPendingEntries($train_no,$lu_date);

	echo "Entries Created";

}

if($code == "Pending-Queries-Date"){

	$lu_date = $_POST['lu_date'];
	
	// Create Pending Entries
	createPendingEntriesForDate($lu_date);

	echo "Entries Created";

}

if($code == "Calculate-Probability"){

	$train_no = $_POST['train_no'];
	$tr_date = $_POST['tr_date'];
	$tr_class = $_POST['tr_class'];
	$curr_status = $_POST['curr_status'];
	
	// Create Pending Entries
	$output = calculateProbability($train_no,$tr_date,$tr_class,$curr_status);

	echo json_encode($output);

}

?>
