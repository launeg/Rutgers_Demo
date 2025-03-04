<?php
class Example1 extends mysqlidbObject
{
    public $id;
    public $name;
    public $address;
    
    function __construct($mysqlidb=null, $logmode = 1)
    {
        parent::__construct($mysqlidb);
        $this->dbName = "example";
        $this->tableName = "extable";
        $this->tableKey = "id";
    }
    
    function load()
    {
        //return "THIS";
        return $this->read();
    }
    
    function save($exampleData)
    {
        if(!isset($exampleData) || empty($exampleData))
        {
            throw new Exception('No Example Data');
        }
        
        return $this->saveRecord($exampleData);
    }
    
    function __destruct()
    {
        parent::__destruct();
    }
}
?>