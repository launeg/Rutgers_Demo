<?php
require_once "mysqlidbObject.php";

class APILog extends mysqlidbObject
{
    public $id;
    public $ruid;
    public $pageURL;
    public $endpoint;
    public $userIP;
    public $browser;
    public $requestQuery;
    public $requestHeader;
    public $response;
    public $responseHeader;
    public $error;
    public $status;
    public $timestart;
    public $timeend;
	private $debugmode = 1; //2 - log all | 1 - log on error | 0 - do not log
    
    function __construct($mysqlidb=null, $debugmode = 2)
    {
        $this->timestart = date("Y-m-d H:i:s");
        $this->error = "";
        parent::__construct($mysqlidb);
        $this->tableName = "apilog";
        $this->tableKey = "id";
        $this->apiReturn = true;
		if(isset($debugmode) && !empty($debugmode)) $this->debugmode = $debugmode;
    }
    
    function logRequest($ruid, $endpoint, $requestQuery = null, $response = null)
    {
        if($this->debugmode == 2 || ($this->debugmode == 1 && isset($this->error) && !empty($this->error))) {
            $warnings = ob_get_contents();
            if(isset($warnings) && !empty($warnings)) $this->addError("warnings: ". $warnings);
            
            $this->ruid = $ruid;
            $this->pageURL = $_SERVER['REQUEST_URI'];
            $this->endpoint = $endpoint;
            $this->userIP = $_SERVER['REMOTE_ADDR'];
            $this->browser = $_SERVER['HTTP_USER_AGENT'];
            $this->requestQuery = ($requestQuery) ? json_encode($requestQuery) : json_encode(file_get_contents('php://input'), true);
            $this->requestHeader = json_encode(getallheaders());
            $this->response = json_encode($response);
            $this->responseHeader = json_encode(headers_list());
            $this->status = http_response_code();
		
        /*
		if($this->debugmode == 2) {			
			$this->results = $this->createRecord();
		} else if($this->debugmode == 1 && isset($this->error) && !empty($this->error)) {
			$this->results = $this->createRecord();
		}
		*/
            $this->results = $this->createRecord();
        
            return $this->results;
        }
        else 
        {
            return false;
        }
        
    }
    
    function addError($error)
    {
        if(isset($error) && !empty($error))
        {
            if(is_array($error))
                $this->error .= json_encode($error);
            else
                $this->error .= $error;
        }
    }
    
    function __destruct()
    {
        parent::__destruct();
    }
}