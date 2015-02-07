<?php
// error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');

define("LOG", true);

define("DB_HOST", "db.yourServer.com"); 
define("DB_NAME", "dbName"); 
define("DB_USER", "dbUser"); 
define("DB_PASS", "dbPass");
define("DB_PORT", "5432"); 

class Api
{
	private $vo;
	private $db;
	private $ip;

	private $data;
	private $ban;
	private $error;
	private $note;

	private $publicMethods = array(
		"testMethod",
		"oneMoreTestMethod"
	); 

	// Constructor
	public function __construct($post) {
		// Counter
		//$this->counter();

		// Links
		$this->vo = $post; // Save input variables
		$this->ip = AddSlashes(getenv("REMOTE_ADDR")); // Get IP address

		// Check public methods
		if (!in_array( $this->vo['method'], $this->publicMethods ))
			$this->showResultError("Method not found!");

		// Database PDO connection
		try {	
			//$this->startProfiling('dbConnect');
			$this->db = new PDO('pgsql:host='.DB_HOST.' port='.DB_PORT.' dbname='.DB_NAME.' user='.DB_USER.' password='.DB_PASS, DB_USER, DB_PASS);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			//$this->endProfiling('dbConnect', 1);
		} catch (PDOException $e) {
			$this->showResultError($e->getMessage());
		}

		// Call method
		if (method_exists($this, $this->vo['method']))		
			$this->$post['method']();
		else 
			$this->showResultError("Method not exist!");
	}

	// Check banned
	private function checkBan() {
		try 
		{
			// Code
			if(1 == 2) {
				$this->ban = array("message" => 'You are banned!');
				$this->showResult();
				die("");
			}
		} catch(PDOException $e) {
			$this->showResultError($e->getMessage());
	    }
	}

	// Get notify
	private function getNotices() {
		try {
			// Code
			if(1 == 2) { 
				$message = "Test message";	
				$this->note = array("message" => StripSlashes($message))	;
			}
		}
		catch(PDOException $e)
		{
		    $this->showResultError($e->getMessage());
	    }
	}
	
	// Example private method with actions
	private function getAction() {

		$arr = array();
	
		try {
			// Delete
			$dl = $this->db->prepare("
				delete from dataTable WHERE foo = :foo
			");
			$res = $dl->execute(array(':foo' => (string)$this->vo["vars"]["foo"]));

			// Insert
			$in = $this->db->prepare("
				insert into dataTable (foo) values (:foo)
			");
			$res = $in->execute(array(':foo' => (string)$this->vo["vars"]["foo"]));

			// Update/SoftDelete
			$up = $this->db->prepare("
				update dataTable SET del = 1 WHERE id = :id
			");
			$res = $up->execute(array(':id' => (int)$this->vo["id"]));

			// Single select
			$st = $this->db->prepare("
				select * from dataTable WHERE id = :id and del = 0
			");
			$res = $st->execute(array(':user_id_int' => (int)$this->user_id_int, ':phone' => (string)$this->vo["data"]["favorite_phone"]));
			$row = $st->fetch(PDO::FETCH_ASSOC);

			if (!isset($row["del"])) {
				// Code
			}

			// Select rows
			$st = $this->db->prepare("
				select * from dataTable WHERE id = :id and del = 0
			");
			$res = $st->execute(array(':id' => (int)$this->vo["id"]));
			
			$count = $st->rowCount();

			while ($row = $st->fetch(PDO::FETCH_ASSOC))
			{
				settype($row["name"], "string");
				$arr[] = $row["name"];
			}

			return $arr;

		} catch(PDOException $e) {
		    $this->showResultError($e->getMessage());
	    }
	}

	// Example
	public function testMethod() { 
		$this->startProfiling("testMethod");

		$this->checkBan(); // Check ban
		$this->getNotices(); // Set notify

		$this->data["foo"] = "response";
		$this->data["action"] = $this->getAction();

		$this->showResult();

		$this->endProfiling("testMethod", 5);
	}

	// Result
	private function showResult() {
		$result = array( "method" => htmlspecialchars($this->vo['method']) );
		
		if (!empty($this->data)) {
			$result["data"] = $this->data;
		}
		if (!empty($this->ban)) 
			$result["ban"] = $this->ban;

		if (!empty($this->error)) 
			$result["error"] = $this->error;
			
		if (!empty($this->note)) 
			$result["note"] = $this->note;
			
		echo json_encode($result);
	}
	
	private function showResultError($data) {
		$result = array( "method" => htmlspecialchars($this->vo['method']) );
		if (!empty($data)) 
			$result["error"] = array("message" => $data);
		die(json_encode($result));
	}

	///////////////
	// Utilities //
	///////////////

	// Log method
	private function WriteToLog($filename,$content) {
		if(LOG == false) return
		$fp = fopen($filename, 'a');
		$content = date("d/m/Y H:i:s") . "\r\n" . ' ' . $content . "\r\n"; 
		$result = fwrite($fp, $content);
		fclose($fp);
		return $result;
	}

	// $key - a string, with no spaces. Should coincide with the same in endProfiing
	private function startProfiling($key) { 
		if(LOG == false) return
		$start_time = microtime();
		$start_array = explode(" ",$start_time);
		$this->start_time[$key] = $start_array[1] + $start_array[0];
	}

	// $key - a string, with no spaces. Should coincide with the same in endProfiing
	// $t - the threshold in seconds. If the above is written to the log. Below - no. If not specified, all written.
	private function endProfiling($key, $t = 0) {
		if(LOG == false) return
		$filename = 'tmp/prf_' . $key . '.txt';
		
		$end_time = microtime();
		$end_array = explode(" ",$end_time);
		$end_time = $end_array[1] + $end_array[0];
		$time = round(($end_time - $this->start_time[$key]), 2);
		
		if ((float)$time>=(float)$t) {
			$fp = fopen($filename, 'a');
			$content = $key . " " . date("d/m H:i:s") . ' - ' . $time . "\r\n"; 
			$result = fwrite($fp, $content);
			fclose($fp);
		}
	}

	// Counter
	private function counter() {
		$fh = fopen("counter/counter_".date("d_m_Y").".txt", "a");
		flock($fh);
		$res = fputs($fh, 1);
		fclose($fh);	
	}
}

$post = $_POST;
$api = new Api($post);

// Structure of response
/*
	method
	data
		ban
			message
		error
			message
		note
			message
		[other-data]
			[data]
*/
?>