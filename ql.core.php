<?php
class MySqlQuery{
    function __construct($queryType) {
        switch($queryType){
            case MySqlQueryType::$$SELECT:
                return new MySqlSelect();
                break;
            case MySqlQueryType::$$INSERT:
                return new MySqlInsert();
                break;
            case MySqlQueryType::$$UPDATE:
                return new MySqlUpdate();
                break;
            case MySqlQueryType::$$DELETE:
                return new MySqlDelete();
                break;
            default:
                $this->_throwException(": cannot create object with type '".$queryType."'. 
                Valid arguments are 'SELECT', 'INSERT', 'UPDATE', or 'DELETE'.
                Tip: consider using properties MySqlQueryType");
                break;
        }
    }
    
    private function _throwException($message){
      throw new Exception("MySqlQuery ".$message);
    }
}

class MySqlSelect extends MySqlQuery
{
	private $cols = array();
	private $table;
	private $jointables = array();
	private $conditions = array();
	private $groupBy;
	private $orderBys = array();
	private $limit;
	
	function __construct() {}
	
	function COLUMNS($columns){
	    if(!(is_null($columns) || empty($columns))){
			if(is_array($columns))
			{
				$this->cols = array_merge($this->cols,$columns);
			}else if($columns instanceOf string){
				array_push($this->cols,$columns);
			}else{
			    parent::_throwException("[SELECT]: columns must be an instance of string or array");
			}
		}
	}
	function FROM($tableName){$this->table = $tableName;}
	function JOIN($tableName){array_push($this->jointables, $tableName);}
	function ADD_CONDITION($stringCondition){array_push($this->conditions,$stringCondition);}
	function ADD_SORT($columnName,$direction){array_push($this->orderBys,$columnName . " " .$direction);}
	function LIMIT($startIndex,$numberOfRecords){$this->limit = $startIndex . "," .$numberOfRecords;}
	function GROUPBY($groupByField){$this->groupBy = $groupByField;}
	function toString(){
		return "SELECT ".join(",",$this->cols)." FROM " . $this->table . (empty($this->jointables) ? "" : " JOIN ". join(",",$this->jointables)) . (empty($this->conditions) ? "" : " WHERE " . join(" AND ",$this->conditions)) . (empty($this->groupBy) ? "" : " GROUP BY ".$this->groupBy) . (empty($this->orderBys) ? "" : " ORDER BY ". join(" ",$this->orderBys)) . (empty($this->limit) ? "" : " LIMIT ".$this->limit);
	}
}
class MySqlDelete extends MySqlQuery
{
	private $table;
	private $conditions = array();
	private $limit;
	
	function __construct() {}
	
	function FROM($tableName){$this->table = $tableName;}
	function ADD_CONDITION($stringCondition){array_push($this->conditions,$stringCondition);}
	function LIMIT($startIndex,$numberOfRecords){$this->limit = $startIndex . "," .$numberOfRecords;}
	function toString(){
		return "DELETE FROM " . $this->table . (empty($this->conditions) ? "" : " WHERE " . join(" AND ",$this->conditions)) . (empty($this->limit) ? "" : " LIMIT ".$this->limit);
	}
}
class MySqlInsert extends MySqlQuery
{
	private $cols = array();
	private $rows = array();
	private $table;
	private $on_duplicate_key;
	
	function __construct() {}
	
	function COLUMNS($columns){
		if(!(is_null($columns) || empty($columns))){
			if(is_array($columns))
			{
				$this->cols = array_merge($this->cols,$columns);
			}else if($columns instanceOf string){
				array_push($this->cols,$columns);
			}else{
			    parent::_throwException("[INSERT]: columns must be an instance of string or array");
			}
		}
	}
	function ADD_ROW($rowValues){
		if(!(is_null($rowValues) || empty($rowValues))) {
			if(!is_array($rowValues))
			{
				parent::_throwException("[INSERT]: row values must be an array of string values.");
			}else{
				array_push($this->rows,"('" . join("','",$rowValues) . "')");
			}
		}
	}
	function ON_DUPLICATE_KEY_UPDATE($duplicate_update){
		$this->on_duplicate_key = $duplicate_update;
	}
	function INTO($tableName){$this->table = $tableName;}
	function toString(){
		return "INSERT INTO " . $this->table . " (".join(",",$this->cols).") VALUES ".join(",",$this->rows) . "" . ($this->on_duplicate_key ? " ON DUPLICATE KEY UPDATE ".$this->on_duplicate_key : "");
	}
}
class MySqlUpdate extends MySqlQuery
{
	private $columnValues = array();
	private $table;
	private $conditions = array();
	private $limit;
	
	function __construct() {}
	
	function ADD_COLUMN_VALUE_PAIR($colName,$val){ if(!(is_null($colName) || empty($colName)) && !(is_null($val) || empty($val))) { $this->columnValues[$colName] = $val; }  }
	function TABLE($tableName){$this->table = $tableName;}
	function ADD_CONDITION($stringCondition){array_push($this->conditions,$stringCondition);}
	function LIMIT($startIndex,$numberOfRecords){$this->limit = $startIndex . "," .$numberOfRecords;}
	function toString(){
		$updates = array();
		foreach ($this->columnValues as $key => $value){
			array_push($updates,$key . "='" . $value . "'");
		}
		
		return "UPDATE " . $this->table . " SET ". join(" , " , $updates) . (empty($this->conditions) ? "" : " WHERE " . join(" AND ",$this->conditions)) . (empty($this->limit) ? "" : " LIMIT ".$this->limit);;
	}
}
class MySqlOperand
{
	public static $AND='AND';
	public static $OR='OR';
}
class MySqlQueryType
{
	public static $SELECT='SELECT';
	public static $DELETE='DELETE';
	public static $INSERT='INSERT';
	public static $UPDATE='UPDATE';
}
?>