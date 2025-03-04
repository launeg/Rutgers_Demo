<?php
require_once '../../model/apilog.model.php';
include_once '../../model/config/definitions.php';

abstract class API
{
    /**
     * Property: method
     * The HTTP method this request was made in, either GET, POST, PUT or DELETE
     */
    protected $method = '';
    /**
     * Property: endpoint
     * The Model requested in the URI. eg: /files
     */
    protected $endpoint = '';
    /**
     * Property: verb
     * An optional additional descriptor about the endpoint, used for things that can
     * not be handled by the basic methods. eg: /files/process
     */
    protected $verb = '';
    /**
     * Property: args
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     */
    protected $args = Array();
    /**
     * Property: file
     * Stores the input of the PUT request
    */
    protected $file = Null;
    /**
     * Property: boolean
     * Specifies if the api will receive json objects
    **/
    protected $receiveJson = true;
    /**
     * Property: Received json data storage
     * 
     */
    protected $jsonData = array();
    /**
     * Property: Exception
     * Errors that were thrown being caught by the API
     */
    protected $errors = Null;
    /**
     * 
     * 
     */
    protected $warnings = Null;
    /**
     * 
     * @var unknown
     */
    protected $response = Null;
    
    /**
     * Constructor: __construct
     * Allow for CORS, assemble and pre-process the data
     */
    public function __construct($request) {
        ob_start();
		
        try {
            $this->args = explode('/', rtrim($request, '/'));
            $this->endpoint = array_shift($this->args);
            if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
                $this->verb = array_shift($this->args);
            }
    
            $this->method = $_SERVER['REQUEST_METHOD'];
            if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
                if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                    $this->method = 'DELETE';
                } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                    $this->method = 'PUT';
                } else {
                    throw new Exception("Unexpected Header");
                }
            }
    
            switch($this->method) {
                case 'DELETE':
                case 'POST':
                    $this->request = $this->_cleanInputs($_POST);
                    if($this->receiveJson)
                    {
                        $json = file_get_contents('php://input');
                        $this->jsonData = json_decode($json, true);
                    }
                    break;
                case 'GET':
                    $this->request = $this->_cleanInputs($_GET);
                    break;
                case 'PUT':
                    $this->request = $this->_cleanInputs($_GET);
                    if($this->receiveJson)
                    {
                        $json = file_get_contents('php://input');
                        $this->jsonData = json_decode($json, true);
                    }
                    else
                        $this->file = file_get_contents("php://input");
                    break;
                default:
                    $this->_response('Invalid Method', 405);
                    break;
            }
        }
        catch(Exception $e) {
            $this->errors = $e;
        }
    }
    
    public function processAPI() {
        if (method_exists($this, $this->endpoint) && !isset($this->errors)) {
            try {
                return $this->_response($this->{$this->endpoint}($this->args));
            } catch(Exception $e) {
                $this->errors = $e;
            }
        } else if(!isset($this->errors))
            return $this->_response("No Endpoint: $this->endpoint", 404);
        
        if(isset($this->errors)) {
            $apilog = new APILog("db_log");
            $ruid = (isset($_SESSION) && $_SESSION['phpCAS']['user']) ? $_SESSION['phpCAS']['user']: "";
            $endpoint = 'Main error thrown';
            $requestQuery = $this->jsonData;
            $apilog->addError(array("error"=>$this->errors->getMessage()));
            if(array_search($_SERVER["SERVER_ADDR"], TEST_ADDR)!==false || array_search($_SERVER["HTTP_HOST"], TEST_HOST)!==false)
            {
                $this->response = $this->_response(array("error"=>$this->errors->getMessage()), 500);
            }
			else if(http_response_code () == "401")
            {
                //$this->response = $this->_response(array("error"=>$this->errors->getMessage()), 500);
				$this->response = $this->_response(array("error"=>$this->errors->getMessage()), 500);
            }
            else
            {
                $this->response = $this->_response(array("error"=>"A Server Error Has Occurred. Please contact system administrators at sasnit@newark.rutgers.edu"), 500);
            }
            $apilog->logRequest($ruid, $endpoint, $requestQuery, $this->response);
            return $this->response;
        }
    }
    
    /**
     * 
     * @param int $mode     mode number for debugging >=1 with errors, >=2 with json request
     * @return string
     */
    public function getRequestLogInfo($mode = null) {
        $message = @$this->method . " /" . $this->endpoint . " / " . $this->verb . " / ";
        if(isset($this->args) && count($this->args)>0)
        {
            foreach($this->args as $arg)
            {
                $message .= $arg."/";
            }
        }
        $message .= "\r\n";
        if(!isset($mode)) $mode = 0;
        if($mode >= 2)
        {
            $message .= json_encode($this->jsonData);
            $message .= "\r\n";
        }
        if($mode >= 1 && isset($this->errors))
        {
            $message .= $this->errors->getMessage()."\r\n";
        }
        return $message;
    }
    
    private function _response($data, $status = 200) {
		$this->warnings = ob_get_clean();
		http_response_code($status);
		//header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
		header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
        header("X-Frame-Options: DENY");
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

//         $data['data'] = $data;
//         if(isset($warnings) && !empty($warnings)) {
//             $data['warning'] = json_encode($warnings);
//         }
        if(!is_null($this->args) && array_key_exists(0,$this->args) && $this->args[0] == "file")
            return $data;
        else {
            if(!empty($this->warnings) && (array_search($_SERVER["SERVER_ADDR"], TEST_ADDR)!==false || array_search($_SERVER["HTTP_HOST"], TEST_HOST)!==false)) {
                header("X-Warnings-Present: TRUE");
                $apilog = new APILog("db_log");
                if(isset($_SESSION) && isset($_SESSION['phpCas']))
                    $ruid = $_SESSION['phpCAS']['user'];
                else 
                    $ruid = "";

                //$ruid = (isset($_SESSION) && isset($_SESSION['phpCAS'])) ? $_SESSION['phpCAS']['user'] : null ;
                $endpoint = 'Main warning present';
                $requestQuery = $this->jsonData;
                $apilog->addError(array("warnings"=>$this->warnings));
                $apilog->logRequest($ruid, $endpoint, $requestQuery, json_encode($data));
                //$data["warnings"] = $this->warnings;
            }
            return json_encode($data);
        }
            
    }
    
    private function _cleanInputs($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }
    
    private function _requestStatus($code) {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return @($status[$code])?$status[$code]:$status[500];
    }
}

?>