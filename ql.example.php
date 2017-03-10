<?php
require_once("ql.class.php");

// creating test database table using straight string
$queryLogic = new QueryLogic();
$queryLogic->AddStatement("CREATE TABLE IF NOT EXISTS querylogictest (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    record_name VARCHAR(250) NOT NULL,
    record_valid TINYINT(1) NOT NULL
    );");
$createResult = $queryLogic->Run();
echo "Create result = ";
var_dump($createResult);
echo "<br/><br/>";

/* 
To perform CRUD operations using more robust logic
see examples below using QueryLogic core classes
*/ 


////////////// Inserting data //////////////
$insert = MySqlQuery::init(MySqlQueryType::$INSERT);
$insert->INTO("querylogictest");
$insert->COLUMNS(["record_name","record_valid"]);
$insert->ADD_ROW(["first row","0"]);
$insert->ADD_ROW(["second row","0"]);
$insert->ADD_ROW(["third row","1"]);

$queryLogic->AddStatement($insert);
$result = $queryLogic->Run();
echo "Insert result = ";
var_dump($result);
echo "<br/><br/>";
echo "Last Inserted ID: ".$queryLogic->GetLastInsertId()."<br/><br/>";





////////////// Selecting data //////////////
$select = MySqlQuery::init(MySqlQueryType::$SELECT);
$select->FROM("querylogictest");
$select->COLUMNS(["record_id","record_name","record_valid"]);

$queryLogic->AddStatement($select);
$result = $queryLogic->Run();
echo "Select result = ";
var_dump($result);
echo "<br/><br/>";




////////////// Updating data //////////////
$update = MySqlQuery::init(MySqlQueryType::$UPDATE);
$update->TABLE("querylogictest");
$update->ADD_COLUMN_VALUE_PAIR("record_valid",1);
$update->ADD_CONDITION("record_id=1");

$queryLogic->AddStatement($update);
$result = $queryLogic->Run();
echo "Update result = ";
var_dump($result);
echo "<br/><br/>";




////////////// Deleting data //////////////
$delete = MySqlQuery::init(MySqlQueryType::$DELETE);
$delete->FROM("querylogictest");
$delete->ADD_CONDITION("record_id=1");

$queryLogic->AddStatement($delete);

$result = $queryLogic->Run();
echo "Delete result = ";
var_dump($result);
echo "<br/><br/>";





////////////// Transaction //////////////
/* 
To execute a transaction, simply add more statements to your
QueryLogic object
*/

$updateA = MySqlQuery::init(MySqlQueryType::$UPDATE);
$updateA->TABLE("querylogictest");
$updateA->ADD_COLUMN_VALUE_PAIR("record_valid",true);
$updateA->ADD_COLUMN_VALUE_PAIR("record_name","second row edit");
$updateA->ADD_CONDITION("record_id=2");
$queryLogic->AddStatement($updateA);

$insertA = MySqlQuery::init(MySqlQueryType::$INSERT);
$insertA->INTO("querylogictest");
$insertA->COLUMNS(["record_name","record_valid"]);
$insertA->ADD_ROW(["forth row is here",0]);
$queryLogic->AddStatement($insertA);


echo "Statement string:<br/> '".$queryLogic->ToString()."'<br/><br/>";

$result = $queryLogic->Run();
echo "Transaction result = ";
var_dump($result);
echo "Last Inserted ID: ".$queryLogic->GetLastInsertId()."<br/><br/>";
echo "<br/><br/>";
echo "Errors: ".$queryLogic->GetErrors();
?>