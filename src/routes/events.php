<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

 
#$app = new \Slim\App;

  
// GET CLOSEST EVENTS DISTANCE 41.055431, -81.512559  longitude latitude
	#http://35.168.19.81/slimapp/public/api/events/get_closest/?longitude=-81.511972&latitude=41.055374
	#http://35.168.19.81/slimapp/public/api/events/get_closest/?longitude=-81.520616&latitude=41.051852
	$app->get('/api/events/get_closest/', function (Request $request, Response $response) {
 	$lon1 = $request->getParam('longitude');
 	$lat1 = $request->getParam('latitude');	
 	$date = date("Y-m-d H:i:s");

	$sql = "select * from events where event_to_datetime > '" . $date . "' and active = 1 ";
	try{
		#echo "<br>";
		#var_dump($lon1); 
		#echo "<br>";
		#var_dump($lat1);
		#echo "<br>";
		#$lon1 = "-81.5133376";		$lat1 = "41.0545975";
		#$lon1 = "-81.519142";		$lat1 = "41.053118";
		#41.055396, -81.513862		41.055374, -81.511972
		#41.052396, -81.520125		41.051852, -81.520616
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->query($sql) or die($sql);
		$user = $stmt->fetchAll(PDO::FETCH_OBJ);

		if (count($user) > 0) 
		{ 
			$court_arrays = array();
			$court_distance = array();

			# compute distance nearest to the user's locaiton
			foreach ($user as $val)
			{
				
				$lon2 =  $val->long; $lat2 =  $val->latd; 
				$x = deg2rad( $lon1 - $lon2 ) * cos( deg2rad( $lat1 ) );
			    $y = deg2rad( $lat1 - $lat2 ); 
			    $dist = 6371000.0 * sqrt( $x*$x + $y*$y ); 
			    $court_distance[$val->id] = $dist;
			    #echo $val->id.") ".$dist."<br>";
 
			}
			# sort distance nearest to the farthest
			#var_dump($court_distance); echo "<br>";
			asort($court_distance);
			#var_dump($court_distance); echo "<br>";
			# regenerate query 
			$str = " ( CASE id ";	
			$i = 1;
			$ids= "";
			foreach ($court_distance as $key => $cd)
			{	
				$ids .= $key .",";
				$str .= " when $key then $i ";
				$i++;
				if ($i == 6) break;
			} 
			$str.= " ELSE 100 END) ASC ";
			$ids = substr($ids,0,-1);		

			$sql2 = " select * from courts where id in ($ids) order by $str ";
			$stmt2 = $db->query($sql2) or die($sql2);
			$courts_nearby = $stmt2->fetchAll(PDO::FETCH_OBJ);
			$db = null;
			#echo "<pre>";
			echo json_encode($courts_nearby); 
		}
		else
		{
			echo '{"notice": {"text":"ID not found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

 
}); 


// ADD EVENTS  
//http://slimapp/api/events/add?event_name=Nike+sale&details=Free+shoes+for+every+purchase+of+jordan+air&url=www.nike.com&location=Colorado+gardens&court_id=&event_from_date=2018-10-11&event_to_date=2018-11-27&start_time=08:00am&end_time=05:00pm&latitude=41.0572624&longitude=-81.5190172
# longitude latitude
$app->post('/api/events/add', function (Request $request, Response $response) {
  
	    $event_name = $request->getParam('event_name');
	    $details = $request->getParam('details');
	    $url = $request->getParam('url');
	    $location = $request->getParam('location');
	    $court_id = $request->getParam('court_id');
	    $event_from_datetime = $request->getParam('event_from_datetime');
	    $event_to_datetime = $request->getParam('event_to_datetime');
	   # $start_time = $request->getParam('start_time');
	   # $end_time = $request->getParam('end_time');
	    $latd = $request->getParam('latitude');
    	$long = $request->getParam('longitude');
    	$active = 1;
	   # $user_id = $request->getParam('user_id');
 	    $date = date("Y-m-d H:i:s");

		$sql = "Insert into events (event_name, details, url, location, court_id, event_from_datetime, event_to_datetime, latd, long, active, created_at, updated_at) values (:event_name, :details, :url, :location, :court_id, :event_from_datetime, :event_to_datetime, :latd, :long, :active, :created_at, :updated_at) ";

		try{
			//get db
			$db = new db();
			// connect to db
			$db = $db->connect();

			$stmt = $db->prepare($sql);
			$stmt->bindParam(':event_name',$event_name);
			$stmt->bindParam(':details',$details);
			$stmt->bindParam(':url',$url);
			$stmt->bindParam(':location',$location);
			$stmt->bindParam(':court_id',$court_id);
			$stmt->bindParam(':event_from_datetime',$event_from_datetime);
			$stmt->bindParam(':event_to_datetime',$event_to_datetime); 
			$stmt->bindParam(':latd',$latd);
			$stmt->bindParam(':long',$long);
			$stmt->bindParam(':active',$active);
			#$stmt->bindParam(':user_id',$user_id); 
			$stmt->bindParam(':created_at',$date);
			$stmt->bindParam(':updated_at',$date);

			$stmt->execute();

			$db = null;
			echo '{"notice": {"text":"Event Added"}}';
		}
		catch(PDOException $e){
			echo '{"error": {"text":'.$e->getMessage().'}}';

		} 
	 

}); 

 


// GET AN EVENT 
# http://slimapp/api/events/1
	$app->get('/api/events/{id}', function (Request $request, Response $response) {
    #echo "CUSTOMERS";
    $id = $request->getAttribute('id');
	$sql = "select * from events where id = $id";

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
			echo '{"notice": {"text":"Event not found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 




// GET ALL EVENTS 
# http://slimapp/api/events/all
	$app->get('/api/events/all/', function (Request $request, Response $response) {
   
    $id = $request->getAttribute('id');
	$sql = "select * from events ";

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
			echo '{"notice": {"text":"Events not found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 




// View Upcomming Events
// http://slimapp/api/events/upcoming/
	$app->get('/api/events/upcoming/', function (Request $request, Response $response) {
    $date = date("Y-m-d H:i:s");
    $id = $request->getAttribute('id');
	$sql = "select * from events where event_to_datetime > '" . $date . "' and active = 1";

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
			echo '{"notice": {"text":"No Upcoming Events found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 




// View Previous Events / Historical 
// http://slimapp/api/events/previous/
	$app->get('/api/events/previous/', function (Request $request, Response $response) {
    $date = date("Y-m-d H:i:s");
    $id = $request->getAttribute('id');
	$sql = "select * from events where event_to_datetime < '" . $date . "' and active = 1";

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
			echo '{"notice": {"text":"No Previous Events found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 



// UPDATE EVENTS
//http://slimapp/api/events/update/1 
//{"event_name":"Nike sale","details":"Free Shoes for every purchase of Jordan 21","url":"www.nike.com","location":"Colorado gardens","court_id":"","event_from_date":"2018-10-11","event_to_date":"2018-11-27","start_time":"08:00AM","end_time":"05:00PM"}
$app->put('/api/events/update/{id}', function (Request $request, Response $response) {

    $id = $request->getAttribute('id');

	$event_name = $request->getParam('event_name');
    $details = $request->getParam('details');
    $url = $request->getParam('url');
    $location = $request->getParam('location');
    $court_id = $request->getParam('court_id');
    $event_from_datetime = $request->getParam('event_from_datetime');
    $event_to_datetime = $request->getParam('event_to_datetime');
   # $start_time = $request->getParam('start_time');
   # $end_time = $request->getParam('end_time');
    $latd = $request->getParam('latitude');
  	$long = $request->getParam('longitude');
    # $user_id = $request->getParam('user_id');
	$date = date("Y-m-d H:i:s");


	$sql = "Update events SET 
			 	event_name = :event_name,
			 	details = :details, 
			 	url = :url, 
			 	location =  :location,
			 	court_id = :court_id, 
			 	event_from_datetime = :event_from_datetime , 
			 	event_to_datetime = :event_to_datetime,  
			 	long = :long, 
			 	latd = :latd,  
			 	updated_at = :updated_at
		 	where id = $id ";

	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		$stmt->bindParam(':event_name',$event_name);
		$stmt->bindParam(':details',$details);
		$stmt->bindParam(':url',$url);
		$stmt->bindParam(':location',$location);
		$stmt->bindParam(':court_id',$court_id);
		$stmt->bindParam(':event_from_datetime',$event_from_datetime);
		$stmt->bindParam(':event_to_datetime',$event_to_datetime); 
		$stmt->bindParam(':long',$long);
		$stmt->bindParam(':latd',$latd);
		#$stmt->bindParam(':user_id',$user_id); 
		$stmt->bindParam(':updated_at',$date);

		$stmt->execute();

		$db = null;
		echo '{"notice": {"text":"Event Updated"}}';
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}


}); 




//DELTE EVENT
http://slimapp/api/events/delete/1 
$app->delete('/api/events/delete/{id}', function (Request $request, Response $response) {

    $id = $request->getAttribute('id');
 	$sql = "Delete from events where id = $id ";

	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->prepare($sql); 

		$stmt->execute();
		$db = null;
		echo '{"notice": {"text":"Event Deleted"}}';
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}


}); 