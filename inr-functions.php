<!--
	Copyright 2012  Varun Verma  (email : varunverma@varunverma.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
-->
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

?>
