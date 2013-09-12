<?php
/*
	Delete old entries
	Create Entries for Day after tomorrow
*/
require( 'inr-functions.php' );

createDBConnection();

// Day after tomorrow
$dat = date ("Y-m-d", strtotime("+2 days", strtotime("now")));

// Create Pending entries for Day after tomorrow
createPendingEntriesForDate($dat);
echo "Entries created for: " . $dat . "<br>";

// Delete Old finished entries
deleteOldEntries();
echo "Old Entries Deleted" . "<br>";

?>
