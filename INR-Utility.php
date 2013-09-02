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

require( 'inr-functions.php' );

$code = $_POST['code'];
$pwd = $_POST['pwd'];

if($pwd != SECRET_KEY){
	die("<br>Wrong password. Please try again");
}

createDBConnection();

if($code == "Pending-Queries"){

	$train_no = $_POST['train_no'];
	$lu_date = $_POST['lu_date'];
	
	// Create Pending Entries
	createPendingEntries($train_no,$lu_date);

	echo "Entries Created";

}

?>
