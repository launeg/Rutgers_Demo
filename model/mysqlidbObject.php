<?php
require_once "config/db_connect.php";
require_once "config/definitions.php";
require_once "MysqliDb.php";


/**
 * This Class extensively uses MysqliDb class and acts as an adapter for the purpose of being able to concurrently use a single connection
 * by this class' multiple children or create single connections for each depending on the application
 * 
 * Please refer to the link below about the MysqliDb class:
 * https://github.com/joshcam/PHP-MySQLi-Database-Class
 * 
 * @author Eric Fonseca
 * @author John Salvador
 */
abstract class mysqlidbObject 
{
    
    protected $tablePrefix = null; //tbl_, db_, should we use?
    public $dbName = null;
	public $tableName = null;
	public $tableKey = null;
	protected $tableWhere = array(); //TODO Static Wheres
	protected $tableJoin = array();
	protected $tableOrderBy = array();
	protected $tableGroupBy = array();
	protected $derivedFields = array(); //TODO Derived fields only on read
	protected $mysqlidb = null;
	protected $functionList = array();
	protected $queryList = array(); //TODO Verify that all queries saved
	protected $errorList = array(); 
	protected $extra = array(); // TODO Save extra fields and values not in properties
	protected $currentRecord = null;
	protected $searchCriteria = array();
	protected $lastSearchIndex= null;
	protected $searchDirection = null;
	private $whereCalled=array();
	protected $results = array();
    protected $resultKeys = array(); 	
    protected $_filterMapping = array();
    protected $linkObjects = array();
   
	public $debug = false;
	public $debugFile = "/debuglog.log";
	public $errorLogging = false;
	public $apiReturn = false;

	protected $defaultErrorMessage = '
	                <div class="alert" style="position:fixed; bottom:0; overflow:scroll;">
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                      <strong>Error:</strong> Database error has occurred. Please notify the system administrator regarding your issue.<br>
	                  Additionally, please note your RUID, application being used, time of error and any other<br>
	                  notable information leading to this error. The SASN-IT team appologizes for this inconvenience.
                    </div>';
	
	protected $defaultApiErrorMessage = '
Database error has occurred. Please notify the system administrator regarding your issue.
Additionally, please note your RUID, application being used, time of error and any other notable information leading to this error.
The SASN-IT team appologizes for this inconvenience.
';
	
	protected function __construct($mysqlidb=null, $tbl=null, $key=null, $where=array()) {
	    global $dbHost, $dbUser, $dbPass;
	    
		$this->tablePrefix = "";
		$this->setMysqliDb($mysqlidb);
		if(isset($mysqlidb) && $mysqlidb instanceof MySqliDb ) {
			$this->mysqlidb->setPrefix($this->tablePrefix);
			$this->mysqlidb = $mysqlidb;
		}
		else if(isset($mysqlidb) && !empty($mysqlidb))
		{
		    if(isset($dbHost)&& !empty($dbHost))
		        $this->mysqlidb = new MySqliDb($dbHost, $dbUser, $dbPass, $mysqlidb);
		    else if(defined('dbHost'))
		        $this->mysqlidb = new MySqliDb(dbHost, dbUser, dbPass, $mysqlidb);
		    else
		        throw new Exception('Cannot get connection information');
		    
		    if(is_string($mysqlidb))
		        $this->dbName = $mysqlidb;
		}
		else
		{
		    throw new Exception('Please instantiate with either a MySqliDb connection or a database name to connect');
		}
		
		if(!is_null($tbl))
		{
		    $this->tableName = $tbl;
		}
		if(!is_null($key))
		{
		    $this->tableKey = $key;
		}
		
		return $this->mysqlidb;
	}
	
	public function getMysqliDb()
	{
	    return $this->mysqlidb;
	}
	
	protected function setMysqliDb($mysqlidb) {
		if(isset($mysqlidb)) {
			$this->mysqlidb = $mysqlidb;
		}
	}
	
	protected function unsetMysqliDb() {
		if(isset($this->mysqlidb)) $this->mysqlidb = NULL;
	}
	
	public function getTableName()
	{
	    return $this->tableName;
	}
	
	
	/**
	 * Inherited method to read the table with options for manual tablename, number of rows and column names
	 *
	 * @param string $tableName    - Manual entry of the table name of the class
	 * @param int $numRows         - Optional entry of the number of rows that the query will fetch
	 * @param array $columns       - Optional entry of column names of the table to be select queried
	 *
	 * @return array               - The results of the query
	 */
	
	public function create($tableName = null, $data = null)
	{
	    if(!isset($tableName)||empty($tableName)) $tableName = $this->tableName;
	    if(!isset($tableName)) return array("error"=>"No Table Specified");
	    $dbName = ($this->dbName) ? $this->dbName.'.' : '';
	
	    if(!isset($this->linkObjects) || empty($this->linkObjects))
	    {
            $id = $this->mysqlidb->insert($dbName.$tableName, $data);
	    }
	    else
	    {
	        foreach($this->linkObjects as $name=>$link)
	        {
	            $this->insertLinkRecord($data);
	        }
	    }
	
	    $this->whereCalled = array();
	    $this->debugFunctions("createObject", $this->mysqlidb->getLastQuery(), $this->mysqlidb->getLastError());
	    $id = ($this->hasDbError()) ? $this->handleDbError($id):$id;
	
	    return $id;
	}
	
	public function createMulti($tableName = null, $data = null)
	{
	    if(!isset($tableName)||empty($tableName)) $tableName = $this->tableName;
	    if(!isset($tableName)) return array("error"=>"No Table Specified");
	    $dbName = ($this->dbName) ? $this->dbName.'.' : '';
	
	    $id = $this->mysqlidb->insertMulti($dbName.$tableName, $data);
	
	    $this->whereCalled = array();
	    $this->debugFunctions("createObject", $this->mysqlidb->getLastQuery(), $this->mysqlidb->getLastError());
	    $id = ($this->hasDbError()) ? $this->handleDbError($id):$id;
	
	    return $id;
	}
	
	public function read($tableName = null, $numRows = null, $columns = "*")
	{
	    if(!isset($tableName)||empty($tableName)) $tableName = $this->tableName;
	    if(!isset($tableName)) return array("error"=>"No Table Specified");
	    $dbName = ($this->dbName) ? $this->dbName.'.' : '';
	    
	    $columns = $this->addLinkObjectJoins($columns);
	
	    $results = $this->mysqlidb->get($dbName.$tableName, $numRows, $columns);
	    
	    $this->whereCalled = array();
	    $this->debugFunctions("readObject", $this->mysqlidb->getLastQuery(), $this->mysqlidb->getLastError());
	    $results = ($this->hasDbError()) ? $this->handleDbError($results): $results;
	    
	    $results = $this->translateLinks($results);
	
	    return $results;
	}
	
	public function update($tableName = null, $tableData = null, $numRows=null)
	{
	    if(!isset($tableName)||empty($tableName)) $tableName = $this->tableName;
	    if(!isset($tableName)) return array("error"=>"No Table Specified");
	    $dbName = ($this->dbName) ? $this->dbName.'.' : '';
	   
	    if(!isset($this->linkObjects) || empty($this->linkObjects))
	    {
	        $bool = $this->mysqlidb->update($dbName.$tableName, $tableData, $numRows);
	    }
	    else
	    {
	        foreach($this->linkObjects as $name=>$link)
	        {
	            $this->updateLinkRecord($data);
	        }
	    }
	
	    $this->whereCalled = array();
	    $this->debugFunctions("updateObject", $this->mysqlidb->getLastQuery(), $this->mysqlidb->getLastError());
	    $bool = ($this->hasDbError()) ? $this->handleDbError($bool): $bool;
	
	    return $bool;
	}
	
	public function deleteAll($tableName = null)
	{
	    if(!isset($tableName)||empty($tableName)) $tableName = $this->tableName;
	    if(!isset($tableName)) return array("error"=>"No Table Specified");
	    $dbName = ($this->dbName) ? $this->dbName.'.' : '';
	    
	    $bool=false;
        $bool = $this->mysqlidb->delete($dbName.$tableName);
	
	    $this->whereCalled = array();
	    $this->debugFunctions("deleteObject", $this->mysqlidb->getLastQuery(), $this->mysqlidb->getLastError());
	    $bool = ($this->hasDbError()) ? $this->handleDbError($bool): $bool;
	
	    return $bool;
	}	
	
	public function replace($tableName, $insertData)
	{
	    if(!isset($tableName)||empty($tableName)) $tableName = $this->tableName;
	    if(!isset($tableName)) return array("error"=>"No Table Specified");
	    $dbName = ($this->dbName) ? $this->dbName.'.' : '';
	    
	    $bool = $this->mysqlidb->replace($tableName, $insertData);
	    
	    $this->whereCalled = array();
	    $this->debugFunctions("deleteObject", $this->mysqlidb->getLastQuery(), $this->mysqlidb->getLastError());
	    $bool = ($this->hasDbError()) ? $this->handleDbError($bool): $bool;
	    
	    return $bool;
	}
	
	public function _onDuplicate($updateColumns, $lastInsertId=null)
	{
	    $this->mysqlidb->onDuplicate($updateColumns, $lastInsertId);
	}
	
	public function currentRecordIndex()
	{
	    return $this->currentRecord;
	}
	
	public function setCurrentRecord($index)
	{
	    $this->currentRecord = $index;
	}

	public function recordCount()
	{
	    return count($this->results);
	}
	
	public function getRecord($index=null)
	{
	    $index = (is_null($index) || $index=="")?$this->currentRecordIndex():$index;
        if(!is_null($index))
	       return $this->results[$index]; //reread current record if no parameter
        else 
           return null;
	}
	
	public function nextRecord()
	{
	
	    if($this->recordCount()>0 && $this->currentRecordIndex()<$this->recordCount()-1)
	    {
	        $this->gotoRecord($this->currentRecordIndex()+1);
	    }
	    elseif($this->currentRecordIndex()>=$this->recordCount()-1)
	    {
	        return false;
	    }
	    return $this->getRecord();
	}
	
	public function prevRecord()
	{
	
	    if($this->recordCount()>0 && $this->currentRecordIndex()>0)
	    {
	        $this->gotoRecord($this->currentRecordIndex()-1);
	    }
	    elseif($this->currentRecordIndex()==0)
	    {
	        return false;
	    }
	
	    return $this->getRecord();
	}
	
	public function lastRecord()
	{
	    $this->gotoRecord($this->recordCount()-1);
	    return $this->getRecord();
	}
	
	public function firstRecord()
	{
	    $this->currentRecord = 0;
	    $this->setArrayData($this->results[$this->currentRecordIndex()]);
	    return $this->getRecord();
	}
	
	public function gotoRecord($index)
	{
	
	    if($this->currentRecordIndex()!==$index)
	    {	        
    	    if($this->recordCount()>0 && $index>=0 && $index < $this->recordCount())
    	    {
    	        $this->currentRecord = $index;
    	        $this->setArrayData($this->results[$this->currentRecordIndex()]);
    	    }
    	    else
    	    {
    	        return false;
    	    }
	    }
	    return $this->getRecord();
	}
	
	public function findRecord($search=array(), $start=null, $direction=null)
	{
	    if(is_null($this->results) || $this->recordCount()==0 || (count($search)==0 && count($this->searchCriteria)==0))
	    {
	        return false;
	    }
	    
	    if((is_null($search) || count($search)==0) && (!is_array($this->searchCriteria) || count($this->searchCriteria)==0))
	    {
	        return false;
	    }
	    elseif(count($search)==0)
	    {
	        $search = $this->searchCriteria;
	    }
	    else
	    {
	        $this->searchCriteria = $search;
	        $this->searchDirection = null;
	        $this->lastSearchIndex = null;
	    }
	    
	    if(!is_null($direction) && trim($direction)!="")
	    {
	        $this->searchDirection = $direction;
	    }
	    elseif(!is_null($this->searchDirection) && trim($this->searchDirection)!="")
	    {
	       $direction = $this->searchDirection;
	    }
	    else 
	    {
	        $direction = "forward";
	        $this->searchDirection = $direction;
	    }    
	        
	    if((!is_null($start) && trim($start)!="" && $start>0 && $start<count($this->recordCount())))
	    {
	        $this->lastSearchIndex = null;
	    }
	    elseif(!is_null($this->lastSearchIndex) && trim($this->lastSearchIndex)!="" && $this->lastSearchIndex>=0 && $this->lastSearchIndex<$this->recordCount())
	    {
	        if($this->lastSearchIndex==$this->currentRecordIndex())
	        {	       
	            if($direction=="backward")
	            {
	                if($this->prevRecord()===false)
	                {
	                    $this->lastRecord();
	                }
	            }    
                else
                {
                    if($this->nextRecord()===false)
                    {
                        $this->firstRecord();
                    }
                }
	        }   
	        $start = $this->currentRecordIndex();
	    }
	    else
	    {
	        $start = 0;
	        $this->lastSearchIndex = null;
	    }
	    
	    if($this->currentRecordIndex()!=$start)
	       $this->gotoRecord($start);  
	    
	    do
	    {
	       $matchArr = array();
	       foreach($search as $field=>$value)
	       {
               if((is_array($value) && array_search($this->$field, $value)!==false) || ($this->$field==$value))
	           {
                    $matchArr[] = $field;
               }
	       }
	       if(count($search) == count($matchArr))
	       {
	           $this->lastSearchIndex = $this->currentRecordIndex();
	           return $this->currentRecordIndex();
	       }
	       if($direction=="backward")
	       {   
	       	   if($this->prevRecord()===false)
	           {
	               $this->lastRecord();
	           }
	       }
	       else
	       {
	           if($this->nextRecord()===false)
	           {
	               $this->firstRecord();
	           }
	       }
	    }
	    while($this->currentRecordIndex()!=$start);
	    
	    $this->lastSearchIndex = null;
	    $this->gotoRecord($start);
	    return false;
	}
	
	public function saveRecord($data = null)
	{  
	    //TODO INSERT ON DUPLICATE
	    $key = $this->tableKey;
	    if(is_null($this->$key))
	    {
	        $this->createRecord($data);
	    }
	    else 
	    {
	        $this->updateRecord($data);
	    }
	}
	
	public function createRecord($data = null)
	{
	    if(isset($data) && !empty($data) && is_array($data))
	    {
	        $this->setArrayData($data);
	        
	    }
	    else
	    {
	        $data = $this->takeObjectData();
	    }

	    $id = $this->create(null, $data);
	
	    //$this->recid = $id;
	    return $id;
	}
	
	public function readRecord($numRows = null, $columns = "*")
	{
	    $keyCnt = 0;
	    $keys = (is_array($this->tableKey))?$this->tableKey:array($this->tableKey);
	    foreach($keys as $j=>$key)
	    {
	        $columns .= ",".$this->tableName.".".$key." AS tkey_".$j;
	        $keyCnt++;
	    }
	    
	    $this->results = $this->read(null,$numRows, $columns);
	    
	    if($this->recordCount()>0)
	    {
	        $this->splitResultKeys($keyCnt);
	        $this->setArrayData($this->results[0]);
	        $this->setCurrentRecord(0);
	    }
	    else
	    {
	        $this->clearObjectData();
	    }
	    return $this->results;
	}
	
    public function updateRecord($tableData = array())
	{
	    if(isset($tableData) && !empty($tableData) && is_array($tableData))
	    {
	        //Take into account if multiple data set
	        $this->setArrayData($tableData);
	    }
	    else
	    {
	        $tableData = $this->takeObjectData();
	    }
	    	    
	    //TODO IF $tableKey is NULL or Array(0) then throw error?  Though what about table wide update?

	    $bool = $this->_whereArr($this->tableKey,$this->readResultKeys())->update(null, $tableData);
   
	    return $bool;
	}
	
	public function deleteRecord($index=null)
	{
        if(!is_null($index) && $index>=0 && $index<$this->recordCount())
        {
            $this->gotoRecord($index);             
        }    
           
        $bool = $this->_whereArr($this->tableKey, $this->readResultKeys())->delete();
	
	    return $bool;
	}

	/**
	 * This method allows you to specify multiple (method chaining optional) AND WHERE statements for SQL queries.
	 * 
	 * @uses $MySqliDb->_where('id', 7)->where('title', 'MyTitle');
	 * 
	 * @param string $whereProp    - The name of the database field.
	 * @param mixed  $whereValue   - The value of the database field.
	 * @param string $operator     - The operator to compare the where clause
	 * @param string $cond         - Additional conditionals
	 * 
	 * @return mysqlidbObject and objects that inherit it(for method chaining)
	 */
	public function _where($whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
	{
	    $this->mysqlidb->where($whereProp, $whereValue, $operator, $cond);
	    $this->whereCalled[] = Array($whereProp, $whereValue, $operator, $cond);
	    //$this->whereCalled++;
	    
	    return $this;
	}

	public function _orderBy($orderByField, $orderbyDirection = "DESC", $customFields = null)
	{
	    $this->mysqlidb->orderBy($orderByField, $orderbyDirection, $customFields);
	    $this->tableOrderBy[] = Array($orderByField, $orderbyDirection, $customFields);
	    return $this;
	}
	
	/**
	 * This method allows you to concatenate joins for the final SQL statement.
	 *
	 * @uses $MySqliDb->_join($mysqliDbObject, 'LEFT')
	 *
	 * @param string $joinTable The name of the table.
	 * @param string $joinCondition the condition.
	 * @param string $joinType 'LEFT', 'INNER' etc.
	 *
	 * @throws Exception
	 * @return MysqliDb
	 */
	public function _join($mysqlidbObject, $mysqlidbObjectRef, $thisRef = null, $joinType = NULL)
	{
	    if(!isset($mysqlidbObject)) throw new Exception('Requires a mysqlidbObject, its key reference and the current object\'s reference OR a string of mysql join statement');
	    if($mysqlidbObject instanceof mysqlidbObject) {
	        $tablename = $mysqlidbObject->dbName.".".$mysqlidbObject->tableName;
    	    $joinCondition = $this->tableName.".".$thisRef." = ".$mysqlidbObject->tableName.".".$mysqlidbObjectRef;
    	    $this->mysqlidb->join($tablename, $joinCondition, $joinType);
    	    return $this;
	    }
	    else if(is_string($mysqlidbObject))
	    {
	        $this->mysqlidb->join($mysqlidbObject, $mysqlidbObjectRef, $joinType);
	        return $this;
	    }
	}
	
	public function _rawJoin($joinTable, $joinCondition, $joinType = NULL)
	{
	    $this->mysqlidb->join($joinTable, $joinCondition, $joinType);
	    return $this;
	}
	
	/**
	 * Execute raw SQL query.
	 *
	 * @param string $query      User-provided query to execute.
	 * @param array  $bindParams Variables array to bind to the SQL statement.
	 *
	 * @return array Contains the returned rows from the query.
	 */
	public function _rawQuery($query, $bindParams = null)
	{
	    return $this->mysqlidb->rawQuery($query, $bindParams);
	}
	
	/**
	 * This method allows you to specify multiple (method chaining optional) ORDER BY statements for SQL queries.
	 *
	 * @uses $MySqliDb->orderBy('id', 'desc')->orderBy('name', 'desc');
	 *
	 * @param string $orderByField The name of the database field.
	 * @param string $orderByDirection Order direction.
	 *
	 * @return MysqliDb
	 */
	public function orderByLiteral($orderByField, $orderbyDirection = "DESC", $customFields = null)
	{
	    $allowedDirection = Array ("ASC", "DESC");
	    $orderbyDirection = strtoupper (trim ($orderbyDirection));
	    $orderByField = preg_replace ("/[^-a-z0-9\.\(\),_'\" `]+/i",'', $orderByField);
	
	    // Add table prefix to orderByField if needed.
	    //FIXME: We are adding prefix only if table is enclosed into `` to distinguish aliases
	    // from table names
	    $orderByField = preg_replace('/(\`)([`a-zA-Z0-9_ ]*\.)/', '\1' . (mysqlidb::$prefix) .  '\2', $orderByField);
	
	
	    if (empty($orderbyDirection) || !in_array ($orderbyDirection, $allowedDirection))
	        die ('Wrong order direction: '.$orderbyDirection);
	
	    if (is_array ($customFields)) {
	        foreach ($customFields as $key => $value)
	            $customFields[$key] = preg_replace ("/[^-a-z0-9\.\(\),_` ]+/i",'', $value);
	
	        $orderByField = 'FIELD (' . $orderByField . ', "' . implode('","', $customFields) . '")';
	    }
	
	    $this->mysqlidb->_orderBy[$orderByField] = $orderbyDirection;
	    return $this;
	}

	public function _groupBy($groupByField)
	{
	    $this->mysqlidb->groupBy($groupByField);
	    $this->tableGroupBy[] = $groupByField;
	    return $this;
	}
	
	public function _whereArr($wherePropArr = array(), $whereValueArr = array(), $operatorArr = array(), $condArr = array(), $parenArr = array())
	{
	    if(!is_array($wherePropArr) && trim($wherePropArr)!="")
	    {
	        $tmp = $wherePropArr;
	        $wherePropArr = array($tmp);
	    }
	    if(!is_array($whereValueArr) && trim($whereValueArr)!="")
	    {
	        $tmp = $whereValueArr;
	        $whereValueArr = array($tmp);
	    }
	    if(!is_array($operatorArr) && trim($operatorArr)!="")
	    {
	        $tmp = $operatorArr;
	        $operatorArr = array($tmp);
	    }
	    if(!is_array($condArr) && trim($condArr)!="")
	    {
	        $tmp = $condArr;
	        $condArr = array($tmp);
	    }
	    if(!is_array($parenArr) && trim($parenArr)!="")
	    {
	        $tmp = $parenArr;
	        $parenArr = array($tmp);
	    }	     
	    
	    $rawfield = "";
	    $rawvals = array();
	    $rawcond = "";
	    $parenOpen = 0;
	    $cnt=0;
	    $keys = array_keys($whereValueArr);
	    foreach($wherePropArr as $k=>$field)
	    {
	        //$value = $whereValueArr[$k];   //TODO SET VALUE = ITEM K
	        
	        $key = $keys[$k];
	        $value = $whereValueArr[$key];
	        $op =(!is_array($operatorArr) || count($operatorArr)==0 || !isset($operatorArr[$k]) || $operatorArr[$k]=="")?"=": $operatorArr[$k];
	        $cond = (!is_array($condArr) || count($condArr)==0 || !isset($condArr[$k]) || $condArr[$k]=="")?(($cnt==0)?"":"AND"): $condArr[$k];
            $parentheses = (!is_array($parenArr) || count($parenArr)==0 || !isset($parenArr[$k]) || $parenArr[$k]=="")?"":$parenArr[$k];
	        
	        if($parentheses=="" && $parenOpen==0)
	        {
	            $this->mysqlidb->where($field, $value, $op, $cond);
	            $this->whereCalled[] = Array($field, $value, $op, $cond);
	        }
	        elseif($parentheses=="(")
	        {
	            $rawcond = $cond;
	            $rawfield .= " (";
	            $parenOpen++;
	            $rawfield .= " $field $op ? ";
	            $rawvals[] = $value;  
	        }
	    	elseif($parentheses==")")
	        {
	            //TODO IF ) before ( then throw ERROR
	            $rawfield .= "$cond $field $op ? )";
	            $rawvals[] = $value;  
	            $parenOpen--;
	            if($parenOpen==0)
	            {
	                if(strtolower($rawcond) == "or")
	                   $this->mysqlidb->orWhere($rawfield, $rawvals);
	                else 
	                    $this->mysqlidb->where($rawfield, $rawvals);
	                $this->whereCalled[] = Array($rawfield, $rawvals, "", $rawcond);
	                $rawfield = "";
                    $rawvals = array();
	                $rawcond = "";
	                
	            }

	        }
	        elseif($parenOpen>0)
	        {
	            $rawfield .= "$cond $field $op ? ";
	            $rawvals[] = $value;	            
	        }
	        
	        $cnt++;
	    }
	        
	        //TODO IF parenOpen>0 then throw ERROR

	    return $this;
	}
	
	public function queryBuilder($whereArr = array(), $joinArr = array(), $orderByArr = array(), $groupByArr = array())
	{
	    if(isset($whereArr) && is_array($whereArr))
	    {
	        foreach($whereArr as $key=>$where)
	        {
	            $whereProp = $where[0];						//string $whereProp - The name of the database field
	            $whereValue = $where[1];					//mixed $whereValue - The value of the database field
	            $operator = $where[2];                    //optional string $operator - '=' or other operators for the field and value
                $condition = $where[3];            //optional string $condition - '=' or other operators for the field and value
                
	    
	            $this->mysqlidb->where($whereProp, $whereValue, $operator);
	        }
	    }
	    
	    if(isset($joinArr) && is_array($joinArr))
	    {
	        foreach($joinArr as $key=>$join)
	        {
	            $joinTable = $join[0];						//string $joinTable - The name of the table
	            $joinCondition = $join[1];					//string $joinCondition - The condition
	            $joinType = (isset($join[2])) ? $join[2] : 'LEFT';						//string $joinType - 'LEFT', 'INNER', etc
	    
	            $this->mysqlidb->join($joinTable, $joinCondition, $joinType);
	        }
	    }
	    
	    if(isset($orderByArr) && is_array($orderByArr))
	    {
	        foreach($orderByArr as $key=>$order)
	        {
	            $orderByField = $order[0];					//string $orderByField - The name of the database field
	            $orderByDirection = $order[1];				//string $orderByDirection - Order direction
	            $customFields = $order[2];					//array $customFields - string array of database fields
	    
	            $this->mysqlidb->orderBy($orderByField, $orderByDirection, $customFields);
	        }
	    }
	    
	    if(isset($groupByArr) && is_array($groupByArr))
	    {
	        foreach($groupByArr as $key=>$group)
	        {
	            $this->mysqlidb->groupBy($group);			//string $group - The name of the database field
	        }
	    }
	    
	    return $this;
	}

	public function whereArr($where)
	{
	    $rawfield = "";
	    $rawvals = array();
	    $rawcond[] = array();
	    $parenOpen = 0;
	    $cnt=0;
	    foreach($where as $k=>$row)
	    {
	        //$value = $whereValueArr[$k];   //TODO SET VALUE = ITEM K
	         
	        $field = $row[0];
	        $value = $row[1];
	        $op =(!isset($row[2]) || $row[2]=="")?"=": $row[2];
	        $cond = (!isset($row[3]) || $row[3]=="")?(($cnt==0)?"":"AND"): $row[3];
	        $parentheses = (!isset($row[4]) || $row[4]=="")?"":$row[4];
	         
	        if($parentheses=="" && $parenOpen==0)
	        {
	            $this->mysqlidb->where($field, $value, $op, $cond);
	            $this->whereCalled[] = Array($field, $value, $op, $cond);
	        }
	        elseif($parentheses=="(")
	        {
	            $rawcond[] = $cond;
	            $rawfield .= " (";
	            $parenOpen++;
	            if(is_null($value))
	            {
	                $rawfield .= " $field $op NULL $cond";
	            }
	            else 
	            {
	                $rawfield .= " $field $op ? $cond";
	                $rawvals[] = $value;
	            }
	        }
	        elseif($parentheses==")")
	        {
	            //TODO IF ) before ( then throw ERROR
	            //$rawfield .= "$rawcond $field $op ? )";
	            if(is_null($value))
	            {
    	            $rawfield .= " $field $op NULL )";
	            }
	            else
	            {
    	            $rawfield .= " $field $op ? )";
    	            $rawvals[] = $value;
	            }
	            $parenOpen--;
	            if($parenOpen==0)
	            {
	                if(strtolower($cond) == "or")
	                    $this->mysqlidb->orWhere($rawfield, $rawvals);
	                else
	                    $this->mysqlidb->where($rawfield, $rawvals);
	                $this->whereCalled[] = Array($rawfield, $rawvals, "", $rawcond);
	                $rawfield = "";
	                $rawvals = array();
	                $rawcond = "";
	                 
	            }
	
	        }
	        elseif($parenOpen>0)
	        {
	            //$rawfield .= "$cond $field $op ? ";
	            if(is_null($value))
	            {
	                $rawfield .= " $field $op NULL $cond ";
	            }
	            else
	            {
                    $rawfield .= " $field $op ? $cond ";
                    $rawvals[] = $value;
                }
	        }
	         
	        $cnt++;
	    }
	     
	    //TODO IF parenOpen>0 then throw ERROR
	
	    return $this;
	}
	
	public function _now($diff = null, $func = "NOW()") {
	    return $this->mysqlidb->now($diff, $func);
	}
	
	public function _interval($diff, $func = "NOW()") {
	    return $this->mysqlidb->interval($diff, $func);
	}
	
	public function setLinkObjects($linkArr) {
	    $this->linkObjects = $linkArr;
	}
	
	public function addLinkObjects($link) {
	    array_push($this->linkObjects, $link);
	}
	
	private function addLinkObjectJoins($columns)
	{
	    if(!isset($this->linkObjects) || empty($this->linkObjects)) return $columns;
	    $columns = $this->getPropertiesString().",";;
	    foreach($this->linkObjects as $name=>$link)
	    {
	        if(isset($link['join']) && !empty($link['join']))
	        {
	            $this->mysqlidb->join($link['join'][0],$link['join'][1],$link['join'][2]);
	        }
	        //$columns .= $link["obj"]->tableName . ".*,";
	        $columns .=  $link["obj"]->getPropertiesString().",";
	    }
	    $columns = rtrim($columns, ",");
	    return $columns;
	}
	
	private function translateLinks($results)
	{
        if(!isset($this->linkObjects) || empty($this->linkObjects)) return $results;
        $resultArr = array();
        //TODO Slow algorithm
        $resultArr = $this->pullLinkObjectData($results);
        foreach($this->linkObjects as $name=>$link)
        {
            if(isset($link["multiple"]))
            {
                if(!isset($resultArr[$name]) || empty($resultArr[$name])) $resultArr[$name] = array();
                $resultArr[$name][0] = $link["obj"]->pullLinkObjectData($results);
            }
            else
            {
                $resultArr[$name] = $link["obj"]->pullLinkObjectData($results);
            }
        }
        
        //TODO faster algorithm
        /*
        foreach($results as $row)
        {
            //Arrange to tree
            foreach($row as $col=>$data)
            {
                $tableData = array();
                $key = explode(".", $col);
                $tableData[$key[0]][$key[1]] = $data;
            }
            
            
            foreach($tableData as $table=>$data)
            {
                if($this->tableName == $table)
                {
                    //TODO check if unique
                    $resultArr[$table] == $data;
                }
                else
                {
                    foreach($this->linkObjects as $name=>$link)
                    {
                        $resultArr[$table][][$name] = $this->
                    }
                }
            }
        }
        */
        
        
        return $resultArr;
	}
	
	public function insertLinkRecord($data)
	{
	    /*
	    $refclass = new ReflectionClass($this);
	    $propList = array();
	    foreach($refclass->getProperties(ReflectionProperty::IS_PUBLIC) as $property)  //IS_PUBLIC so variables needed for logic can be private or protected
	    {
	        if($property->class == $refclass->name)
	            $propList[$property->name] = 1;
	    }
	    
	    $dataFields = array_diff($propList, $this->derivedFields);
	    $propertiesString = "";
	    
	    $result = array();
	    //TODO
	    foreach($dataFields as $property=>$x)  //IS_PUBLIC so variables needed for logic can be private or protected
	    {
	        
	        $result[]=$this->create($x);
	    }
	    
	    return $result;
	    */
	}
	
	public function updateLinkRecord($data)
	{
	    $refclass = new ReflectionClass($this);
	    $propList = array();
	    foreach($refclass->getProperties(ReflectionProperty::IS_PUBLIC) as $property)  //IS_PUBLIC so variables needed for logic can be private or protected
	    {
	        if($property->class == $refclass->name)
	            $propList[$property->name] = 1;
	    }
	    
	    $dataFields = array_diff($propList, $this->derivedFields);
	    $propertiesString = "";
	    
	    $result = array();
	    //TODO
	    foreach($dataFields as $property=>$x)  //IS_PUBLIC so variables needed for logic can be private or protected
	    {
	        
	    }
	    
	    return false;
	}
	
	public function getPropertiesString()
	{
	    $refclass = new ReflectionClass($this);
	    $propList = array();
	    foreach($refclass->getProperties(ReflectionProperty::IS_PUBLIC) as $property)  //IS_PUBLIC so variables needed for logic can be private or protected
	    {
	        if($property->class == $refclass->name)
	            $propList[$property->name] = 1;
	    }
	    
	    $dataFields = array_diff($propList, $this->derivedFields);
	    $propertiesString = "";
	    foreach($dataFields as $property=>$x)  //IS_PUBLIC so variables needed for logic can be private or protected
	    {
	        //$data[$property] = $this->$property;
	        $propertiesString .= $this->tableName.".".$property." as `".$this->tableName.".".$property."`,";
	    }
	    $propertiesString = rtrim($propertiesString, ",");
	    
	    return $propertiesString;
	}
	
	public function pullLinkObjectData($data)
	{
	    $filtered = array();
	    foreach($data as $row)
	    {
	        
    	    foreach($row as $key=>$value)
    	    {
    	        $key = explode(".",$key);
    	        if(property_exists($this, $key[1]))
    	            $filtered[$key[1]] = $value;
    	    }
	    }
	    
	    return $filtered;
	}
	
	public function setArrayData($data)
	{
	    //TODO, store extra variables that into an array if no property exists
	    foreach($data as $key=>$value)
	    {
	        if(property_exists($this, $key))
	           $this->$key = $value;
	        else
	           array_push($this->extra, array($key=>$value));
	    }
	}
	
	public function filterData($data)
	{
	    $filtered = array();
	    foreach($data as $key=>$value)
	    {
	        if(property_exists($this, $key))
	            $filtered[$key] = $value;
	    }
	    
	    return $filtered;
	}
	
	public function numOftableKeys()
	{
	    if((is_array($this->tableKeys) && count($this->tableKeys)==0) || is_null($this->tableKeys) || $this->tableKeys=="")
	        return 0;
	    return ((is_array($this->tableKeys))?count($tbl->tableKeys):1);
	    
	}
	
	public function setResultKeys($keys)
	{
        $this->resultKeys = $keys;
	}
	
	public function clearResultKeys()
	{
	    $this->resultKeys = null;
	}
	
	
	public function printObjectVariables()
	{
	    $refclass = new ReflectionClass($this);
        echo "<h3>mysqlidbObject::printObjectVariables():</h3>";
        foreach($refclass->getProperties() as $property) {
            $name = $property->name;
            if($property->class == $refclass->name)
                print "{$property->name} => {$this->$name}<br>";
        }
	}
	
	protected function takeObjectData()
	{
	    $refclass = new ReflectionClass($this);
	    $propList= array();
	    foreach($refclass->getProperties(ReflectionProperty::IS_PUBLIC) as $property)  //IS_PUBLIC so variables needed for logic can be private or protected
	    {
	        if($property->class == $refclass->name)
    	       $propList[$property->name] = 1; 
	    }
	    
	    $dataFields = array_diff($propList, $this->derivedFields);
	    
	    foreach($dataFields as $property=>$x)  //IS_PUBLIC so variables needed for logic can be private or protected
	    {

	        $data[$property] = $this->$property;
	    }
	    
	    return $data;
	}
	
	public function clearObjectData()
	{
	    $refclass = new ReflectionClass($this);
	    foreach ($refclass->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED) as $property)
	    {
	        $name = $property->name;
	        if ($property->class == $refclass->name && $name!="DAOobjects")
	        {
	            if(is_array($this->$name))
	            {
	                $this->$name = array();
	            }
	            else 
	            {
	                $this->$name = null;
	            }
	        }
	    }
	    $this->results = null;
	    $this->currentRecord = null;
	}

	protected function clearObject()
	{
	    $refclass = new ReflectionClass($this);
	    foreach ($refclass->getProperties() as $property)
	    {
	        $name = $property->name;
	        if ($property->class == $refclass->name)
	        {
	        	if(is_array($this->$name))
	            {
	                $this->$name = array();
	            }
	            else 
	            {
	                $this->$name = null;
	            }
	        }
	    }
	}
	
	/*
	 * Errors and Debugging
	 */
	
	/**
	 * Function that checks if the last query made with mysqlidb have an error or not
	 * 
	 * @return boolean - Depicts whether there is an error present or not
	 */
	protected function hasDbError()
	{
	    $error = $this->mysqlidb->getLastError();
	    return (isset($error) && !empty($error));
	}
	
	protected function handleDbError($input)
	{
	    $temp = $input;
	    $input = array(0=>$temp);
	    if(array_search($_SERVER["SERVER_ADDR"], TEST_ADDR)!==false || array_search($_SERVER["HTTP_HOST"], TEST_HOST)!==false || $this->debug)   //if server is one of the defined test servers
	    {
	        if($this->apiReturn)
	        {
                $input["error"] = $this->getJsonDebug();
	        }
	        else
	        {
	            $this->displayDebug();
	        }
	    }
	    else
	    {
    	    if($this->apiReturn)
    	    {
    	        //$input["error"] = $this->defaultApiErrorMessage;
    	        $input["error"] = $this->getJsonDebug(); //TODO temporary for debugging apis
    	    }
    	    else
    	    {
    	        echo $this->defaultErrorMessage;
    	    }
	    }
	    //TODO return dbtable inserted id, query, dberror
	    
// 	    function writeDebugLog($msg,$minDebugMode=0,$file="")
	    
	    
        $file = $this->debugFile;
        file_put_contents($file,"\r\n". date("Y-m-d g:iA")." [".$_SERVER['REQUEST_URI']."] [".$_SERVER['REMOTE_ADDR']."  ".((isset($_SESSION) && $_SESSION['phpCAS']['user']!="")?$_SESSION['phpCAS']['user']:"NO USER")."] ".json_encode($this->getJsonDebug())."\r\n", FILE_APPEND | LOCK_EX);
	    
	    //$this->logDbError();
	    return $input;
	}
	
	protected function logDbError()
	{
	    //TODO log only dbquery and error
	    $error = array(
	        "cookie"=>json_encode($_COOKIE, true)
	    );
	    $this->mysqlidb->insert("tbl_dberror", $error);
	}
	
	protected function debugFunctions($funcName, $query, $error)
	{
	    array_push($this->functionList, $funcName);
	    array_push($this->queryList, $query);
	    array_push($this->errorList, $error);
	}
	
	public function displayDebug()
	{
        echo '<div class="alert" style="position:fixed; bottom:0; overflow:scroll;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <ul>';
        foreach($this->functionList as $key=>$func)
        {
            echo "<li>$key - $func<br>Query: ".$this->queryList[$key]."<br>Error: ".$this->errorList[$key]."</li>";
        }
        echo "</ul></div>";
	}
	
	public function getJsonDebug()
	{
	    $errors = array();
	    foreach($this->functionList as $key=>$func)
	    {
	        $errors[]="$key\nFunction: $func\nQuery: ".$this->queryList[$key]."\n\nError: ".$this->errorList[$key];
	    }
	    return $errors;
	}
	
	public function getFilterOptions($filterSpecs)
	{
	    
	    $filterOptions = array();
	    foreach($filterSpecs as $returnName)
	    {
	        if(is_array($this->_filterMapping[$returnName]))
	        {
	            $columnName = "";
	            foreach($this->_filterMapping[$returnName] as $columns)
	            {
	                $this->_groupBy($columns);
	                $columnName .= "$columns,'-',";
	            }
	            //$columnName = preg_replace("\/\,\'\-\'", "", $columnName);
	            $columnName = substr($columnName, 0, -strlen(",'-',"));
	            $options = $this->read(null, null, "CONCAT(".$columnName.") as ".$returnName);
	            $filterOptions[$returnName] = $options;
	        }
	        else
	        {
	            $columnName = $this->_filterMapping[$returnName];
	            $this->_groupBy($columnName);
	            $dept = "";
	            if($returnName == "dept")
	            {
	                $this->_join("db_userdata.tbl_subjectcode", "tbl_subjectcode.subCode=schooldata.SUBJ_CD", "LEFT");
	                $this->_join("db_userdata.dept", "dept.deptID=tbl_subjectcode.deptID", null,  "LEFT");
	                $dept .= ", dept.deptName";
	                $this->_orderBy("deptName", "ASC");
	            }
	            $options = $this->read(null, null, $columnName." as ".$returnName.$dept);
	            $filterOptions[$returnName] = $options;
	        }
	    }
	    return $filterOptions;
	}
	
	public function __destruct() {
		$this->unsetMysqliDb();
	}
	
	protected function splitResultKeys($numKeys)
	{
	    foreach($this->results as $k=>$row)
	    {
	        $this->resultKeys[$k] = array_splice($row,-$numKeys);
	        //$this->results[$k] = array_splice($row,0,1-$numKeys);
	        $this->results[$k] = array_splice($row, 0, count($this->results[$k])-$numKeys);
	    }
	}
	
	protected function readResultKeys($index=null, $tableKey=null)
	{
	    $tableKey = (!is_null($tableKey) && ((is_array($tableKey) && count($tableKey)>0) || (!is_array($tableKey) && trim($tableKey)!="")))?$tableKey:$this->tableKey;
	    $index = (!is_null($index))?$index:$this->currentRecordIndex();
	    
	    if(is_array($tableKey))
	    {
	        $tableKeyVal = array();
	        foreach($tableKey as $k=>$key)
	        {
	            $tableKeyVal[$k] = array_shift($this->resultKeys[$index]);
	        }
	    }
	    else
	    {
	        //$tableKeyVal = array_shift($this->resultKeys[$index]);
	        //$tableKeyVal = $this->resultKeys[$index];
	        //$tableKey = array($this->tableKey);
	        $tableKey = $this->tableKey;
	        $tableKeyVal = (isset($index) && $this->resultKeys[$index]) ? $this->resultKeys[$index] : $this->$tableKey;
	    
	    }
	    
	    return $tableKeyVal;
	}
}

?>