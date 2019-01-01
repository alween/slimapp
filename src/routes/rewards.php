<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

 
#$app = new \Slim\App;

  

// GET Reward Items 
# http://slimapp/api/rewards/get_items/
	$app->get('/api/rewards/get_items/', function (Request $request, Response $response) {
    #echo "CUSTOMERS";
    $id = $request->getAttribute('id');
	#$sql = "select * from court_ratings where court_id = $id";
	$sql = "select * from rewards ";
	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->query($sql) or die($sql);
		$user = $stmt->fetchAll(PDO::FETCH_OBJ);
		if (count($user) > 0) 
		{ 
			echo json_encode($user);
		}
		else
		{
			echo '{"error": {"text":"Reward items not found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 



// GET Eligble Reward Items for the user
# http://slimapp/api/rewards/eligble_items/2
	$app->get('/api/rewards/eligble_items/{id}', function (Request $request, Response $response) {
    #echo "CUSTOMERS";
    $id = $request->getAttribute('id');
	#$sql = "select * from court_ratings where court_id = $id";
	$sql = "select * from rewards where points_needed <= (select sum(user_points) from  user_points where user_id = ". $id ." ) ";
	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->query($sql) or die($sql);
		$user = $stmt->fetchAll(PDO::FETCH_OBJ);
		if (count($user) > 0) 
		{ 
			echo json_encode($user);
		}
		else
		{
			echo '{"error": {"text":"Reward items not found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 

 
 