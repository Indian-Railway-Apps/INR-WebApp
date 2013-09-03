<?php

header("Content-type: text/json");

require( 'inr-functions.php' );

createDBConnection();

$output = getPendingQueries();

echo json_encode($output);

?>