<?php

//
// Script for searching overall Tables.
//
// Uses tool_referentielle_integritaet.php-script to search 
// in each table, if there are not existing objects.
//
// library: mysqli
// Ubuntu: sudo apt-get install php-mysqlnd
//

$host = "localhost:3306";
$user = 'ilias';
$password = "dbpass";
$database = "information_schema";


// Connection to Database
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection to database failed: " . $conn->connect_error);
}

////
// Printing all Tablenames
////
$tablename="COLUMNS";


$query="SELECT TABLE_NAME FROM $tablename WHERE TABLE_SCHEMA = 'ilias' AND COLUMN_NAME = 'obj_id'";

$result = $conn->query($query);
//
//
//


//
//
// Objects which are getting found
$obj_results = array();

// Complete result
$complete_result = array();

// All tablenames array
$result_array = array();

// Checking the query
// True: 
//	Calling function with each tablename
// False:
// 	Error
if ($result) {
    // Saves each tablename in array: result_array
    while ($row = $result->fetch_assoc()) {
    	$result_array[] = $row['TABLE_NAME'];
    }

} else {
    // Error message, if there is an error
    echo "Fehler bei der Abfrage: " . $conn->error;
}
$result->close();
// Verbindung schließen
$conn->close();

//
////
//////

foreach ($result_array as $tableName){
       
        // Call the tool with a tablename
        require_once "tool_referentielle_integritaet.php";

        // Gets the result-array of the other script
        $result_temp = checkMistakesinTables($tableName, 'object_data', 'obj_id');
        
        // Print the result, if there is an output 
        if ($result_temp[0] > 0) {
        	echo "Object_IDs of table " .$tableName .":\n";
        	for($i = 0; $i < count($result_temp); $i++){
			echo "\n" .$result_temp[$i];
		}
        	// Stores the complete Results: Tables with problematic objects
        	echo "\n" .$tableName ."\n";
        	$complete_result[] = array("Table" => $tableName, "Objects" => $result_temp);
        }
    }





//////
////
//
echo "Problematic Objects:\n";
echo "\n";

// Clear text-file for new information
file_put_contents("problematic_tables_obj.txt", '');
$iteration = 0;
foreach ($complete_result as $file_object) {
	print_r($complete_result[$iteration]['Objects']);
	$data = json_encode($file_object, JSON_PRETTY_PRINT);
	file_put_contents("problematic_tables_obj.txt", $data, FILE_APPEND);
	$iteration++;
}

//Output of complete results in file
//file_put_contents("problematic_tables_obj.txt", $complete_result);
//
////
//////




?>
