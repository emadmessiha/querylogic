<?php
require_once('ql.configuration.php');
require_once('ql.core.php');

class QueryLogic {
    /*
    The following properties must be set through configuration file
      $DB_SERVERNAME
      $DB_USERNAME
      $DB_PASSWORD
      $DB_DBNAME
    */
    private $_queries = [];
    private $_lastInsertId = 0;
    private $_errors = "";
    
    function __construct() {
        
    }
    
    public function AddStatement($statement){
      if(is_subclass_of($statement,'MySqlQuery')){
        $this->_queries[] = $statement->toString();
      }else if(is_string($statement)){
        $this->_queries[] = str_replace(';','',$statement);
      }else{
        self::_throwQLException(
          "parameter 'statement' must be a string or instance of MySqlQuery.
          '".gettype($statement)."' was provided instead.");
      }
    }
    
    public function HasMultipleStatements(){
      return (count($this->_queries) > 1);
    }
    
    public function IsEmpty(){
      return (count($this->_queries) == 0);
    }
    
    public function ToString(){
      if(!(is_null($this->_queries) || empty($this->_queries))) {
            if(!is_array($this->_queries))
            {
              self::_throwException("Something went wrong, 'statements' in
              QueryLogic object should be array, but is not.");
            }else{
              if($this->HasMultipleStatements()){
                $string = "START TRANSACTION; " 
                  . join(";",$this->_queries) 
                  . "; COMMIT;";
              }else{
                $string = $this->_queries[0].";";
              }
              
            	return $string;
            }
        }
    }
    
    public function Run(){
        $this->_errors = "";
        $results;
        // Connect
        $conn = self::_getConnection();
        
        if($this->HasMultipleStatements()){
          $conn->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
          foreach ($this->_queries as $qu){
            $conn->query($qu);
          }
          $results = $conn->commit();
        }else{
          $results = $conn->query($this->ToString());
        }
        
        if(!$results){
        	$this->_errors = $conn->error;
        }else{
          if(!is_bool($results)){
            $return_results = [];
            while ($row = $results->fetch_assoc()) {
                $return_results[] = $row;
            }
            $results = $return_results;
          }
        }
        $this->_lastInsertId = $conn->insert_id;
        $conn->close();
        $this->_queries = [];// reseting query logic object
        return $results;
    }
    
    
    public function GetLastInsertId(){
      return $this->_lastInsertId;
    }
    
    public function GetErrors(){
      return $this->_errors;
    }
    
    
    private function _throwQLException($message){
      throw new Exception("QueryLogic Error: ".$message);
    }
    
    private function _getConnection(){
      $conn = new mysqli(
        $GLOBALS["DB_SERVERNAME"], 
        $GLOBALS["DB_USERNAME"], 
        $GLOBALS["DB_PASSWORD"], 
        $GLOBALS["DB_DBNAME"]);
      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }else{
        return $conn;
      }
    }
}
?>