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
    
    function __construct() {
        
    }
    
    public function AddStatement($statement){
      if($statement instanceof MySqlQuery){
        $_queries[] = $statement->toString();
      }else if($statement instanceof string){
        $_queries[] = $statement;
      }else{
        self::_throwException("parameter 'statement' must be a string or instance of MySqlQuery.");
      }
    }
    
    private function _runQuery($q){
        $results;
        // Connect
        $link = mysql_connect($DB_SERVERNAME, $DB_USERNAME, $DB_PASSWORD)
            OR die(mysql_error());
        mysql_select_db($DB_DBNAME,$link);
        
        $results = mysql_query($q, $link);
        
        if(!$results){
        	echo mysql_error();
        }
        
        mysql_close($link);
        return $results;
    }
    
    private function Query(){
        if(!(is_null($this->_queries) || empty($this->_queries))) {
            if(!is_array($this->_queries))
            {
              self::_throwException("parameter 'queries' must be an array of string queries.");
            }else{
              $r = self::_runQuery("START TRANSACTION;" . join(";",$this->_queries) . "; COMMIT;");
            	return $r;
            }
        }
    }
}
?>