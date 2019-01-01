<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

  

// GET USER POINTS PROFILE 
# http://slimapp/api/user_points_history/2
	$app->get('/api/user_location/view/{id}', function (Request $request, Response $response) {
    #echo "CUSTOMERS";
    $id = $request->getAttribute('id');
	$sql = "select *  
			from user_location 
			where user_id = $id order by id desc limit 1";

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
			echo '{"error": {"text":"user has no points"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 


// ADD USER LOCATION  
// http://slimapp/api/user_location/add?latitude=14.8736424&longitude=120.79837110000001&user_id=2
# longitude latitude	
$app->post('/api/user_location/add', function (Request $request, Response $response) {
 
    $user_id = $request->getParam('user_id');
    $latd = $request->getParam('latitude');
    $long = $request->getParam('longitude');
    $date = date("Y-m-d H:i:s");
	$sql = "Insert into user_location (user_id, latd, long, created_at) values (:user_id, :latd, :long, :created_at)";
 	 
	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		$stmt->bindParam(':user_id',$user_id);
		$stmt->bindParam(':latd',$latd);
		$stmt->bindParam(':long',$long);
		$stmt->bindParam(':created_at',$date);
		$stmt->execute();

		$db = null;
		echo '{"notice": {"text":"User Location Inserted"}}';
	}
	catch(PDOException $e){

		echo '{"error": {"text":'.$e->getMessage().'}}';

	} 
 

}); 
