<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
 
#$app = new \Slim\App;
#http://35.168.19.81/slimapp/public/api/versions/latest/
  

// GET Latest Version
$app->get('/api/version/latest/', function (Request $request, Response $response) {
  
    $id = $request->getAttribute('id');
	$sql = "select * from versions  order by id desc limit 1";

	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->query($sql) or die($sql);
		$version = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($version);
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 


// GET Latest Version
$app->get('/api/version/all/', function (Request $request, Response $response) {
  
    $id = $request->getAttribute('id');
	$sql = "select * from versions  order by id desc ";

	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->query($sql) or die($sql);
		$version = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($version);
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 
