<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

 
#$app = new \Slim\App;


// GET USER POINTS PROFILE 
# http://slimapp/api/user_points_top10/
	$app->get('/api/user_points_top10/', function (Request $request, Response $response) {
    #echo "CUSTOMERS"; 
	$sql = "SELECT sum(user_points) AS total_points, users.id, user_points.user_id, users.first_name, users.last_name, users.email, users.username, users.photo FROM user_points INNER JOIN users ON users.id = user_points.user_id group by user_id order by 2 desc limit 10"; 

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
			echo '{"error": {"text":"No User Points"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 



// GET USER POINTS PROFILE 
# http://slimapp/api/user_points/2
	$app->get('/api/user_points/{id}', function (Request $request, Response $response) {
    #echo "CUSTOMERS";
    $id = $request->getAttribute('id');
	$sql = "select sum(user_points) as total_points 
			from user_points 
			where user_id = $id";

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


// GET USER POINTS PROFILE 
# http://slimapp/api/user_points_history/2
	$app->get('/api/user_points_history/{id}', function (Request $request, Response $response) {
    #echo "CUSTOMERS";
    $id = $request->getAttribute('id');
	$sql = "select *  
			from user_points 
			where user_id = $id";

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



// ADD USER POINTS  
// http://slimapp/api/user_points/add?accumulated_by=Test&user_points=2&user_id=2
$app->post('/api/user_points/add', function (Request $request, Response $response) {
 
    $user_id = $request->getParam('user_id');
    $user_points = $request->getParam('user_points');
    $accumulated_by = $request->getParam('accumulated_by');
    $date = date("Y-m-d H:i:s");
	$sql = "Insert into user_points (user_id, user_points, accumulated_by, created_at) values (:user_id, :user_points, :accumulated_by, :created_at)";
 	 
	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		$stmt->bindParam(':user_id',$user_id);
		$stmt->bindParam(':user_points',$user_points);
		$stmt->bindParam(':accumulated_by',$accumulated_by);
		$stmt->bindParam(':created_at',$date);
		$stmt->execute();

		$db = null;
		echo '{"notice": {"text":"User Points Added"}}';
	}
	catch(PDOException $e){

		echo '{"error": {"text":'.$e->getMessage().'}}';

	} 
 

}); 
