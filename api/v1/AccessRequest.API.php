<?php
//TODO remove for production
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once 'API.class.php';
include_once '../../model/Example1.model.php';
//require_once LF_DIR.'CAS/RUCAS.php';

class AdvisorAssignmentAPI extends API
{
    protected $rucas;
    protected $clientauth;
    private $db;
    private $debuglog = true;
    
    public function __construct($request, $origin)
    {
        parent::__construct($request);
        
        
        // Abstracted out for example
        /*
        $APIKey = new Models\APIKey();
        $User = new Models\User();
    
        if (!array_key_exists('apiKey', $this->request)) {
            throw new Exception('No API Key provided');
        } else if (!$APIKey->verifyKey($this->request['apiKey'], $origin)) {
            throw new Exception('Invalid API Key');
        } else if (array_key_exists('token', $this->request) &&
            !$User->get('token', $this->request['token'])) {
    
                throw new Exception('Invalid User Token');
            }
    
            $this->User = $User;
            */
    }
    
    /**
     * Example of an Endpoint
     */
    //TODO remove in production
    protected function example()
    {
        if ($this->method == 'GET') {
			
			return "GET Worked";
        } else if ($this->method == 'POST') {
            if(!isset($this->verb) || empty($this->verb))
            {
                return "TESTING POST";
            }
            else if($this->verb == "session")
            {
                return $_SESSION;
            }
            else if($this->verb == "jsondata")
            {
                return $this->jsonData;
            }
            else if($this->verb == "args")
            {
                return $this->args;
            }
        } else if ($this->method == 'PUT') {
            return "PUT is being accepted";
        } else {
            return "Only accepts GET, POST, PUT, and DELETE requests";
        }
    }
    
	
    protected function example1()
    {
        /**
         * uses mysqlidb wrapper
         * https://github.com/ThingEngineer/PHP-MySQLi-Database-Class
         * @var MysqliDb $mysqlidb+
         */
        $mysqlidb = new MysqliDb(dbHost, dbUser, dbPass,"example"); //connect to example database
        $exampleObj = new Example1($mysqlidb);
        if($this->method == 'GET')
        {
            if(!isset($this->verb) || empty($this->verb))
            {
                return $exampleObj->read();
            }
            
        }
        if($this->method == 'POST')
        {
            if($this->verb == "submit")
            {
                return $exampleObj->save($this->jsonData['exampleData']);
            }
        }
    }
    
    //Function name is the first example : http://localhost/AccessRequest/api/v1/example2/
    protected function example2()
    {
        /**
         * uses mysqlidb wrapper
         * https://github.com/ThingEngineer/PHP-MySQLi-Database-Class
         * @var MysqliDb $mysqlidb
         */
        $mysqlidb = new MysqliDb(dbHost, dbUser, dbPass,"example");
        if($this->method == 'GET')
        {
            if(!isset($this->verb) || empty($this->verb))
            {
                //SELECT id, name FROM extable
                return $mysqlidb->get("extable", null, "*");
            }
            else if($this->verb == "address")
            {
                //SELECT address FROM extable
                return $mysqlidb->get("extable", null, "address");
            }
            else if($this->verb == "person")
            {
                $data = $mysqlidb->where('id', $this->args[0])->get("extable", null, "*");
                return $data;
            }
        }
        else if($this->method == 'POST')
        {
            if(!isset($this->verb) || empty($this->verb))
            {
                $data=$this->jsonData;
                //$data = array("name"=>"Laura Negrin", "address"=>"4 Somewhere else"); //Test Data
                $saveConfirmation = $mysqlidb->insert("extable", $data);
                return array("data"=>$data, "confirmation"=>$saveConfirmation, "postData"=>$this->jsonData);
            }
            if($this->verb == "queryID")
            {
                $id1=$this->jsonData['id1'];
                $id2=$this->jsonData['id2'];
                $returnData = $mysqlidb->rawQuery("SELECT id, address FROM extable WHERE id IN(?,?)", array($id1,$id2));
                return $returnData;
            }
            else if($this->verb == "submitForm")
            {
                $data=$this->jsonData['exampleSubmit'];
                $response = $mysqlidb->rawQuery(
                    "INSERT INTO extable (name, address) VALUES (?,?)",
                    array($data['name'], $data['address'])
                    );
                return $response;
            }
        }
    }
    protected function form(){
		global $conn;
		$conn = mysqli_connect("localhost","root","!eric123","rutgersaccessgrantsystem");
		mysqli_set_charset($conn,"utf8mb4");
		if($this->method == 'GET'){
			if($this->verb == "netID"){
				$out = "";
				$out = $this->request;
				$netID = $out['q'];
				$sql = "SELECT * FROM `forms` WHERE `userNetID` = '".$netID."';";
				//SELECT * FROM `forms` WHERE `userNetID` = 'kj351' 
				$result=mysqli_query($conn, $sql);
				$out = [];
				while($row = mysqli_fetch_assoc($result)){
					$out = $out + $row;
				}
				if($out==[])return "id not found";//get_object_vars($this);
				else return $out;
			}else
				return "no netID specified";
		}
		
		
		
	}
    protected function user()
    {
		
		global $conn;
		$conn = mysqli_connect("localhost","root","!eric123","rutgersaccessgrantsystem");
		mysqli_set_charset($conn,"utf8mb4");	
		if($this->method == 'GET'){
			if($this->verb == "netID"){
				$out = "";
				$out = $this->request;
				$netID = $out['q'];
				$sql = "SELECT * FROM `users` WHERE `netID` = '".$netID."';";
				//SELECT * FROM `forms` WHERE `userNetID` = 'kj351' 
				$result=mysqli_query($conn, $sql);
				while($row = mysqli_fetch_assoc($result)){
					return $row;
				}
				return "id not found";//get_object_vars($this);
			}else
				return "no netID specified";
		}
		/*
		
		return $result;*/
        //TODO Wednesday
    }
    
    protected function roles()
    {
        //TODO Wednesday
    }
}

?>