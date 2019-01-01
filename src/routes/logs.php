<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
 
#$app = new \Slim\App;
#http://35.168.19.81/slimapp/public/api/logs/user/2
 

// INSERT LOG
//http://slimapp/api/logs/add?log=test&user_id=2
$app->post('/api/logs/add', function (Request $request, Response $response) {
 
    $user_id = $request->getParam('user_id');
    $log = $request->getParam('log');
    $date = date("Y-m-d H:i:s");
	$sql = "Insert into logs (user_id, log, created_at)  values (:user_id, :log, :created_at)";

	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		$stmt->bindParam(':user_id',$user_id);
		$stmt->bindParam(':log',$log);
		$stmt->bindParam(':created_at',$date);
		$stmt->execute();

		$db = null;
		echo '{"notice": {"text":"Log Added"}}';
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	} 
 

}); 

 

// GET ALL LOGS
$app->get('/api/logs/view/{limit}', function (Request $request, Response $response) {
    $limit = $request->getAttribute('limit');
 
	$sql = "select * from logs order by id desc limit $limit ";

	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->query($sql) or die($sql);
		$customers = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($customers);
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 


// GET ALL 5000 logs of the user sorted desc
$app->get('/api/logs/user/{id}', function (Request $request, Response $response) {
  
    $id = $request->getAttribute('id');
	$sql = "select * from logs where user_id = '".$id."' order by id desc limit 5000";

	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->query($sql) or die($sql);
		$customers = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($customers);
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 
