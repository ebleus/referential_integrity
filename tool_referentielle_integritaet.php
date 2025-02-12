<?php
//
//
//
// library: mysqli
// Ubuntu: sudo apt-get install php-mysqlnd
//




// var:
//	table_name: Input parameter. The table where we are searching

//$table_name = $argv[1];


$connect = null;




// Scanning of the object-table
// If object not exists in object-table, there is mistake
// 
//
function checkMistakesinTables($searched_table_name, $tablenameToCompare, $columnname){
        
	global $connect;
	
	$host = "localhost:3306";
	$user = 'ilias';
	$password = "dbpass";
	$database = "ilias";

	// Connection to Database
	$connect = new mysqli($host, $user, $password, $database);

	// Check connection
	if ($connect->connect_error) {
    		die("Connection to database failed: " . $connect->connect_error);
	}


	// Get all objects in a table
	$objectArray[] = scanTable($searched_table_name, $columnname);
	
	//echo $objectArray[1] ."\n";
	
	// Saves all non existing objects
	$notExistend = [];
	
	
	// Checks over all objects whether they existing
	while(!empty($objectArray[0])){
		// Get next Object
		$nextObject = array_shift($objectArray[0]);
		//print_r($nextObject);
		//echo "\n";
		
		// Does the object is existing?
		if (isObjectExistend($nextObject, $tablenameToCompare, $columnname)) {
			//
			//echo "$nextObject exists\n";
		} else {
			//echo "It does not exist!!!\n\n";
			$notExistend[] = $nextObject;
		}
		}
	// Returns all non existend objects
	//echo "All not exitsting Objects in $searched_table_name:\n\n";
	

	// Close connection
	$connect->close();
	return $notExistend;
	}
	


//
// Function to scan a table
// Saves the objectIDs
//
// var:
//	searched_table_name: Table which we want to search
//
// returns: 
//	Row obj_id if it exist
//
function scanTable($searched_table_name, $id){
 
	try{	
	//
	$my_sql = "SELECT $id FROM $searched_table_name WHERE EXISTS(SELECT $id FROM $searched_table_name WHERE $id)";
	
	// Query
	global $connect;
	$result = $connect->query($my_sql, MYSQLI_STORE_RESULT);
	
	//returns the result
	if ($result !== false) {
		
		if (mysqli_num_rows($result) > 0) {
			while ($row = mysqli_fetch_assoc($result)) {
				$resultArray[] = $row[$id];
			}
		} else {
		
			return false;
		}
		
	} else {
		
		//echo "Error during execution: " .$connect->error;
		return false;
	} 
	
	return $resultArray;
	} catch(mysqli_sql_exception $e){}
	}
//
// Function to search an object in table 
//
//var:
//	objectID: ID of the object we are looking for
//	tablename: The table were we are searching
//

function isObjectExistend($objectID, $tablename, $columnname){

	//Connection to database 
	global $connect;

	//table for searching the ObjectID
	//$tablename = "object_data";
	
	//$columnname = 'obj_id';
	
	//is object in the table
	$my_sql = "SELECT * FROM $tablename WHERE $columnname = $objectID";
	
	// Query
	$result = $connect->query($my_sql, MYSQLI_STORE_RESULT);
	
	//returns the result
	if ($result !== false) {
		
	if (mysqli_num_rows($result) > 0) {
			return true;
		} else {
			return false;
		}

		
		
	} else {
		
		//echo "Error during execution: " .$connect->error;
		return false;
	}
}


?>

