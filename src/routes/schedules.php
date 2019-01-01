<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

 
#$app = new \Slim\App;


// checkin
//http://slimapp/api/schedule/checkin/?user_id=2&court_id=2
$app->post('/api/schedule/checkin/', function (Request $request, Response $response) {
    $user_id = $request->getParam('user_id');
    $court_id = $request->getParam('court_id');
    $created_at = date("Y-m-d H:i:s");
    $checkin = 1;
	$checkout = 0;
	$scheduled_date =  date("Y:m:d"); #$request->getParam('scheduled_date');
	$scheduled_time_from = date("H:i:s"); #$request->getParam('schedule_time_from');
	$scheduled_time_to = '00:00:00' ; #$request->getParam('schedule_time_to');

	$sql_checkuser = "select  * from schedules where user_id = '" .$user_id. "' and checkout = 0 and checkin = 1  	"; #and checkin <> 2
	$db = new db();
	// connect to db
	$db = $db->connect();

	$stmt1 = $db->query($sql_checkuser) or die($sql_checkuser);
	$check = $stmt1->fetchAll(PDO::FETCH_OBJ);
	if (count($check) > 0) 
	{ 

		foreach ($check as $row => $val) {
			if ($val->checkin == 1)
			{
			$result = array("result"=> "missing checkout", 
							"id"=>$val->id,
							"user_id" => $val->user_id,  
							"court_id" => $val->court_id,  
							"checkin" => $val->checkin,  
							"checkout" => $val->checkout,  
							"scheduled_date" => $val->scheduled_date,  
							"scheduled_time_from" => $val->scheduled_time_from,  
							"scheduled_time_to" => $val->scheduled_time_to,   
							"created_at" => $val->created_at);
						
/*
						$sql2 = "Insert into logs (user_id, log, created_at)  
								values (:user_id, :log, :created_at)";

						try{
							//get db
							$db2 = new db();
							// connect to db
							$db2 = $db2->connect();
							$log = "Missing Checkout " . $val->id . " ||| user_id => " . $val->$user_id . " |||  result => ". $result. "  ||| ";
							$stmt = $db2->prepare($sql2);
							$stmt->bindParam(':user_id',$user_id);
							$stmt->bindParam(':log',$log);
							$stmt->bindParam(':created_at',$date);
							$stmt->execute();

							$db2 = null;
						#	echo '{"notice": {"text":"Log Added"}}';
						}
						catch(PDOException $e){
						#s	echo '{"error": {"text":'.$e->getMessage().'}}';

						}
*/

			}
			else
			{
				$scheduled_time_from = date("H:i:s");
			 	$checkin = 1; 

				$sql = "Update schedules SET 
						 	checkin = :checkin, 
						 	scheduled_time_from = :scheduled_time_from
					 	where user_id = ".$val->user_id." and id = ".$val->id;

/*
						$sql2 = "Insert into logs (user_id, log, created_at)  
								values (:user_id, :log, :created_at)";

						try{
							//get db
							$db2 = new db();
							// connect to db
							$db2 = $db2->connect();
							$log = "Update schedules => " . $id . " ||| user_id => " . $user_id . " |||  sql => ". $sql. "  ||| ";
							$stmt = $db2->prepare($sql2);
							$stmt->bindParam(':user_id',$user_id);
							$stmt->bindParam(':log',$log);
							$stmt->bindParam(':created_at',$date);
							$stmt->execute();

							$db2 = null;
						#	echo '{"notice": {"text":"Log Added"}}';
						}
						catch(PDOException $e){
						#s	echo '{"error": {"text":'.$e->getMessage().'}}';

						}
*/

				try{
					//get db
					$db = new db();
					// connect to db
					$db = $db->connect();

					$stmt = $db->prepare($sql);
					$stmt->bindParam(':checkin',$checkin); 
					$stmt->bindParam(':scheduled_time_from',$scheduled_time_from); 
					$stmt->execute();

					$db = null;
					$result = '{"notice": {"text":"User Checkedin Success"}}';
				}
				catch(PDOException $e){
					$result = '{"error": {"text":'.$e->getMessage().'}}';

				}
			}

		 
		}
		echo json_encode($result);
	}
	else
	{
		

		$sql = "Insert into schedules (user_id, court_id, checkin, checkout, scheduled_date, scheduled_time_from, scheduled_time_to, created_at)  values (:user_id, :court_id, :checkin, :checkout, :scheduled_date, :scheduled_time_from, :scheduled_time_to, :created_at)";
		
		try{
			 

			$stmt = $db->prepare($sql);
			$stmt->bindParam(':user_id',$user_id);
			$stmt->bindParam(':court_id',$court_id);
			$stmt->bindParam(':checkin',$checkin);
			$stmt->bindParam(':checkout',$checkout);
			$stmt->bindParam(':scheduled_date',$scheduled_date);
			$stmt->bindParam(':scheduled_time_from',$scheduled_time_from);
			$stmt->bindParam(':scheduled_time_to',$scheduled_time_to);
			$stmt->bindParam(':created_at',$created_at);
			$stmt->execute();

			echo 		'{
						"result":"success",
						"id":"'.$db->lastInsertId().'",
						"user_id":"'.$user_id.'",  
						"court_id":"'.$court_id.'",  
						"checkin":"'.$checkin.'",  
						"checkout":"'.$checkout.'",  
						"scheduled_date":"'.$scheduled_date.'",  
						"scheduled_time_from":"'.$scheduled_time_from.'",  
						"scheduled_time_to":"'.$scheduled_time_to.'",   
						"created_at":"'.$created_at.'" 
						 }';
			$db = null;
		}
		catch(PDOException $e){
			echo '{"error": {"text":'.$e->getMessage().'}}';

		} 
	}	

}); 

//http://slimapp/api/schedule/checkout/?user_id=2&schedule_id=2
 $app->put('/api/schedule/checkout/', function (Request $request, Response $response) {
    $user_id = $request->getParam('user_id');
    $schedule_id = $request->getParam('schedule_id');
	$scheduled_time_to = date("H:i:s");
 	$checkout = 1; 

	$sql = "Update schedules SET 
			 	checkout = :checkout, 
			 	scheduled_time_to = :scheduled_time_to
		 	where user_id = $user_id and id = $schedule_id and checkout = 0 ";

	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		$stmt->bindParam(':checkout',$checkout); 
		$stmt->bindParam(':scheduled_time_to',$scheduled_time_to); 
		$stmt->execute();

		$db = null;
		echo '{"notice": {"text":"User Checkout Success"}}';
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 
# http://35.168.19.81/slimapp/public/api/schedule/forcecheckout/?user_id=50&schedule_id=1
//http://slimapp/api/schedule/forcecheckout/?user_id=50&schedule_id=1
 $app->put('/api/schedule/forcecheckout/', function (Request $request, Response $response) {
    $user_id = $request->getParam('user_id');
    $schedule_id = $request->getParam('schedule_id');
	#$scheduled_time_to = $request->getParam('scheduled_time_to');
	$scheduled_time_to = date("H:i:s");
 	$checkout = 2; 

	$sql = "Update schedules SET 
			 	checkout = :checkout, 
			 	scheduled_time_to = :scheduled_time_to
		 	where user_id = $user_id and checkout = 0 and id = $schedule_id ";


/*
						$sql2 = "Insert into logs (user_id, log, created_at)  
								values (:user_id, :log, :created_at)";

						try{
							//get db
							$db2 = new db();
							// connect to db
							$db2 = $db2->connect();
							$log = "LOGIN schedule_id => " . $schedule_id . " ||| user_id => " . $user_id . " |||  sql => ". $sql. "  ||| ";
							$stmt = $db2->prepare($sql2);
							$stmt->bindParam(':user_id',$user_id);
							$stmt->bindParam(':log',$log);
							$stmt->bindParam(':created_at',$date);
							$stmt->execute();

							$db2 = null;
						#	echo '{"notice": {"text":"Log Added"}}';
						}
						catch(PDOException $e){
						#s	echo '{"error": {"text":'.$e->getMessage().'}}';

						}
*/


	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		$stmt->bindParam(':checkout',$checkout); 
		$stmt->bindParam(':scheduled_time_to',$scheduled_time_to); 
		$stmt->execute();

		$db = null;
		echo '{"notice": {"text":"User Checkout Success"}}';
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 



// CHECK IF THE USER WAS PREVIOUSLY CHECKOUT 
# http://35.168.19.81/slimapp/public/api/schedule/check/?user_id=2
# http://slimapp/api/schedule/check/?user_id=2
	$app->get('/api/schedule/check/', function (Request $request, Response $response) {
    $user_id = $request->getParam('user_id');
	
	$sql = "select  * from schedules where user_id = '" .$user_id. "' and checkout = 0 and checkin = 1"; #and checkin <> 2 

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
			echo '{"error": {"text":"No Checkout needed"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 




// checkin later
//http://slimapp/api/schedule/checkinlater/?user_id=2&court_id=2&scheduled_date=2018-11-01&scheduled_time_from=04:00&scheduled_time_to=06:00
$app->post('/api/schedule/checkinlater/', function (Request $request, Response $response) {
    $user_id = $request->getParam('user_id');
    $court_id = $request->getParam('court_id');

    $created_at = date("Y-m-d H:i:s");
    $scheduled_date = $request->getParam('scheduled_date');
	$scheduled_time_from =$request->getParam('scheduled_time_from');
	$scheduled_time_to = $request->getParam('scheduled_time_to');

	$sql_checkuser = "select  * from schedules where user_id = '" .$user_id. "' and checkout = 0 
				and scheduled_date = '".$scheduled_date."' 
				and ( scheduled_time_from between '".$scheduled_time_from."' and '".$scheduled_time_to."' 
				or scheduled_time_to between '".$scheduled_time_from."' and '".$scheduled_time_to."' )"; 
	
	$sql_checkuser = "select  * from schedules where user_id = '" .$user_id. "' and checkout = 0 
				and scheduled_date = '".$scheduled_date."' ";
				#and checkin <> 2
	$db = new db(); 
	// connect to db
	$db = $db->connect();

	$stmt1 = $db->query($sql_checkuser) or die($sql_checkuser);
	$check = $stmt1->fetchAll(PDO::FETCH_OBJ);
	#var_dump($check);
	if (count($check) > 0) 
	{ 
		foreach ($check as $row => $val) {
 


			#$dateA = strtotime(date_format(date_create( $val->scheduled_date. " " .$val->scheduled_time_from),"Y-m-d H:i:s"));
			#$dateB = strtotime(date_format(date_create( $val->scheduled_date. " " .$val->scheduled_time_to),"Y-m-d H:i:s"));
 
			#$stf = strtotime(date_format(date_create( $scheduled_date. " " .$scheduled_time_from),"Y-m-d H:i:s"));
			#$stt = strtotime(date_format(date_create( $scheduled_date. " " .$scheduled_time_to),"Y-m-d H:i:s"));

			#if (($stf > $dateA && $stf < $dateB) || ($stt > $dateA && $stt < $dateB)){
			    #echo "is between";die();
	   			$result = array("result"=> "had existing schedule", 
					"id"=>$val->id,
					"user_id" => $val->user_id,  
					"court_id" => $val->court_id,  
					"checkin" => $val->checkin,  
					"checkout" => $val->checkout,  
					"scheduled_date" => $val->scheduled_date,  
					"scheduled_time_from" => $val->scheduled_time_from,  
					"scheduled_time_to" => $val->scheduled_time_to,   
					"created_at" => $val->created_at); 
			#} else {
 						 
			#}  
		}
		echo json_encode($result);		
	}
	else
	{
		

		$sql = "Insert into schedules (user_id, court_id, checkin, checkout, scheduled_date, scheduled_time_from, scheduled_time_to, created_at)  values (:user_id, :court_id, :checkin, :checkout, :scheduled_date, :scheduled_time_from, :scheduled_time_to, :created_at)";
		
		try{
			  
		    $checkin = 0;
			$checkout = 0; 

			$stmt = $db->prepare($sql);
			$stmt->bindParam(':user_id',$user_id);
			$stmt->bindParam(':court_id',$court_id);
			$stmt->bindParam(':checkin',$checkin);
			$stmt->bindParam(':checkout',$checkout);
			$stmt->bindParam(':scheduled_date',$scheduled_date);
			$stmt->bindParam(':scheduled_time_from',$scheduled_time_from);
			$stmt->bindParam(':scheduled_time_to',$scheduled_time_to);
			$stmt->bindParam(':created_at',$created_at);
			$stmt->execute();

			$result =  		'{
						"result":"success",
						"id":"'.$db->lastInsertId().'",
						"user_id":"'.$user_id.'",  
						"court_id":"'.$court_id.'",  
						"checkin":"'.$checkin.'",  
						"checkout":"'.$checkout.'",  
						"scheduled_date":"'.$scheduled_date.'",  
						"scheduled_time_from":"'.$scheduled_time_from.'",  
						"scheduled_time_to":"'.$scheduled_time_to.'",   
						"created_at":"'.$created_at.'" 
						 }';
			echo $result;
			$db = null;
		}
		catch(PDOException $e){
			echo '{"error": {"text":'.$e->getMessage().'}}';

		} 
	}	

}); 





// checkin2
//http://slimapp/api/schedule/checkin2/?user_id=2&schedule_id=16
$app->put('/api/schedule/checkin2/', function (Request $request, Response $response) {
    $user_id = $request->getParam('user_id');
    $schedule_id = $request->getParam('schedule_id');
    #$court_id = $request->getParam('court_id');
    #$created_at = date("Y-m-d H:i:s");
    $checkin = 1;
	#$checkout = 0;
	#$scheduled_date =  date("Y:m:d"); #$request->getParam('scheduled_date');
	#$scheduled_time_from = date("h:i:s"); #$request->getParam('schedule_time_from');
	#$scheduled_time_to = '00:00:00' ; #$request->getParam('schedule_time_to');

	#$sql_checkuser = "select  * from schedules where user_id = '" .$user_id. "' and schedule_id = '" . $schedule_id . "' and checkout = 0	";

	$sql_checkuser = "select  * from schedules where user_id = '" .$user_id. "' and checkin = 1 and checkout = 0";
	$db = new db();
	// connect to db
	$db = $db->connect();

	$stmt1 = $db->query($sql_checkuser) or die($sql_checkuser);
	$check = $stmt1->fetchAll(PDO::FETCH_OBJ);
	if (count($check) > 0) 
	{ 
		foreach ($check as $row => $val) {
			$result = array("result"=> "missing checkout", 
							"id"=>$val->id,
							"user_id" => $val->user_id,  
							"court_id" => $val->court_id,  
							"checkin" => $val->checkin,  
							"checkout" => $val->checkout,  
							"scheduled_date" => $val->scheduled_date,  
							"scheduled_time_from" => $val->scheduled_time_from,  
							"scheduled_time_to" => $val->scheduled_time_to,   
							"created_at" => $val->created_at);

		 
		}
		echo json_encode($result);

		#echo '{"error": {"text":"You have an exisiting checkin schedule"}}';	
		#echo json_encode($check);
	}
	else
	{
		$sql = "Update schedules SET 
				 	checkin = :checkin 
			 	where user_id = $user_id and id = $schedule_id"; 
		try{
			//get db
			$db = new db();
			// connect to db
			$db = $db->connect();

			$stmt = $db->prepare($sql);
			$stmt->bindParam(':checkin',$checkin); 
			$stmt->execute();

			$db = null;
			echo '{{"result":"Success"}}';
		}
		catch(PDOException $e){
			echo '{"error": {"text":'.$e->getMessage().'}}';

		}
	}
}); 


// GET CHECKIN LATER CURRENT LIST from the user
//http://slimapp/api/schedule/list_checkinlater/?user_id=2
$app->get('/api/schedule/list_checkinlater/', function (Request $request, Response $response) {
  
    $id = $request->getParam('user_id');
    $date = date("Y-m-d");
 

	$sql = "select * from schedules 
				where user_id = '".$id."' 
					and checkin = 0 
					and checkout = 0 
					and scheduled_date = '".$date."' 
					and scheduled_time_from >= '" . date("H:i:s"). "'
			UNION ALL
			select * from schedules 
				where user_id = '".$id."' 
					and checkin = 0 
					and checkout = 0 
					and scheduled_date > '".$date."'  
			order by scheduled_date, scheduled_time_from 		";
			#echo $sql;
	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->query($sql) or die($sql);
		$results = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($results);
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 

// cancel checkin / cancel schedule
//http://slimapp/api/schedule/cancelcheckin/?schedule_id=2
 $app->put('/api/schedule/cancelcheckin/', function (Request $request, Response $response) {
    $schedule_id = $request->getParam('schedule_id'); 
 	$checkin = 2; 

	$sql = "Update schedules SET 
			 	checkin = :checkin 
		 	where id = $schedule_id and checkout = 0 ";

	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		$stmt->bindParam(':checkin',$checkin);  
		$stmt->execute();

		$db = null;
		echo '{"notice": {"text":"User Checkin Cancelled"}}';
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 

# get total players per hour in the selected court
# http://slimapp/api/schedule/courtplayers_per_hour/?court_id=2&scheduled_date=2018-10-31 
# http://35.168.19.81/slimapp/public/api/schedule/courtplayers_per_hour/?court_id=2&scheduled_date=2018-10-31 
$app->get('/api/schedule/courtplayers_per_hour/', function (Request $request, Response $response) {
    #$user_id = $request->getParam('user_id');
    $court_id = $request->getParam('court_id');
    $date = $request->getParam('scheduled_date');
    

	$sql_checkuser = "select  * from schedules where court_id = '" .$court_id. "' and  scheduled_date = '".$date."' "; #and checkin <> 2
	$db = new db();
	// connect to db
	$db = $db->connect();

	$stmt1 = $db->query($sql_checkuser) or die($sql_checkuser);
	$check = $stmt1->fetchAll(PDO::FETCH_OBJ);
	if (count($check) > 0) 
	{ 
		$players_per_hour = array();
		for ($i=0; $i<24; $i++)
		{
			$players_per_hour[$i] = 0;
		}
		foreach ($check as $row => $val) {
			#echo $val->scheduled_time_from . " - " .  $val->scheduled_time_to . "<br>";
			$temp_from = explode(":",$val->scheduled_time_from);
			$x2 = $temp_to = explode(":",$val->scheduled_time_to);
			#echo $temp_from[0]. " - " . $temp_to[0]. "<br>"; $d = 0 ;
			for ($i=$temp_from[0]; $i < $temp_to[0];$i++)
			{
					$d = (int) $i ;
					#echo $d . ' - ' . strlen($d) . "<br>"; 
					$players_per_hour[$d]++; 
			}
		}
		 
		echo  json_encode($players_per_hour);
	}
	else
		echo '{0}';
 

}); 

# view all users in a court for today and tomorrow
# http://slimapp/api/schedule/list_of_players/?court_id=2&scheduled_date=2018-11-01 
$app->get('/api/schedule/list_of_players/', function (Request $request, Response $response) {
    #$user_id = $request->getParam('user_id');
    $court_id = $request->getParam('court_id');
    $date = $request->getParam('scheduled_date');
    

	$sql_checkuser = "SELECT
			schedules.id,
			schedules.user_id,
			schedules.court_id,
			schedules.scheduled_date, 
			TIME_FORMAT(schedules.scheduled_time_from,'%h %p') as scheduled_time_from,
			TIME_FORMAT(schedules.scheduled_time_to,'%h %p') as scheduled_time_to,  
			users.nickname,
			users.first_name,
			users.last_name, 
			users.photo,  
			sum(user_points.user_points) as user_points
		FROM
			schedules
			INNER JOIN users ON schedules.user_id = users.id
			LEFT JOIN user_points ON schedules.user_id = user_points.user_id
			where scheduled_date = '".$date."' and court_id = '" .$court_id. "' 
			group by users.id "; #and checkin <> 2
	#echo $sql_checkuser; 
	$db = new db();
	// connect to db
	$db = $db->connect();

	$stmt1 = $db->query($sql_checkuser) or die($sql_checkuser);
	$check = $stmt1->fetchAll(PDO::FETCH_OBJ);
	if (count($check) > 0) 
	{ 
 
		 
		echo  json_encode($check);
	}	
	else
	{
		#echo '{"error": {"text":"ID not found"}}';
		echo '[]';
	}

}); 

 
// View Upcomming Schedule of the User
// http://35.168.19.81/slimapp/public/api/schedule/2
	$app->get('/api/schedule/upcoming/{user_id}', function (Request $request, Response $response) {
    $date = date("Y-m-d H:i:s");
    $user_id = $request->getAttribute('user_id');
	#$sql = "select * from schedules where scheduled_date >= '" . $date . "' and user_id = $user_id";
	$sql = "select schedules.id,
				schedules.user_id,
				schedules.court_id,
				schedules.checkin,
				schedules.checkout,
				schedules.scheduled_date,
				schedules.scheduled_time_from,
				schedules.scheduled_time_to,
				schedules.created_at,
				courts.`name`,
				courts.address,
				courts.city,
				courts.state,
				courts.country
			from schedules 
		INNER JOIN courts ON schedules.court_id = courts.id
		where scheduled_date >= '" . $date . "' and user_id = $user_id
		order by schedules.scheduled_date ASC, schedules.scheduled_time_from ASC";
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
		#	echo '{"notice": {"text":"No Upcoming Schedules found"}}';
			echo '[]';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 



// View Upcomming Schedule of the User
// http://35.168.19.81/slimapp/public/api/schedule/recent/2
	$app->get('/api/schedule/recent/{user_id}', function (Request $request, Response $response) {
    $date = date("Y-m-d H:i:s");
    $user_id = $request->getAttribute('user_id');
	$sql = "select schedules.id,
				schedules.user_id,
				schedules.court_id,
				schedules.checkin,
				schedules.checkout,
				schedules.scheduled_date,
				schedules.scheduled_time_from,
				schedules.scheduled_time_to,
				schedules.created_at,
				courts.`name`,
				courts.address,
				courts.city,
				courts.state,
				courts.country
			from schedules 
		INNER JOIN courts ON schedules.court_id = courts.id
		where scheduled_date < '" . $date . "' and user_id = $user_id 
		order by schedules.scheduled_date DESC, schedules.scheduled_time_from DESC";
	#echo $sql;
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
			#echo '{"notice": {"text":"No Recent Schedules found"}}';
			echo '[]';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 
