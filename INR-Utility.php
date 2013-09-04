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

?>
