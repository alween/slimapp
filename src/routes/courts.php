<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

 
#$app = new \Slim\App;
#http://35.168.19.81/slimapp/public/api 


// GET All states in the courts table
	$app->get('/api/court/states/', function (Request $request, Response $response) {
    #$search = $request->getAttribute('search');
   # $param = " where courts.state like '%".$search."%' or courts.address like '%".$search."%' ";
	#$sql = "select * from court_ratings where court_id = $id";
	$sql = "SELECT state FROM courts where state <> ''  and active = 1  group by state order by state" ;
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
			echo '{"notice": {"text":"No courts found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 

// GET All states in the courts table
	#api/court/get_state/?state=OH
	$app->get('/api/court/get_state/', function (Request $request, Response $response) {
    $state = $request->getParam('state');
   # $param = " where courts.state like '%".$search."%' or courts.address like '%".$search."%' ";
	#$sql = "select * from court_ratings where court_id = $id";
	$sql = "SELECT * FROM courts where state = '".$state."'  and active = 1 order by state" ;
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
			echo '{"notice": {"text":"No courts found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 

// GET All cities from the states in the courts table
	#api/court/city/?state=OH
	$app->get('/api/court/city/', function (Request $request, Response $response) {
    $state = $request->getParam('state');
   # $param = " where courts.state like '%".$search."%' or courts.address like '%".$search."%' ";
	#$sql = "select * from court_ratings where court_id = $id";
	$sql = "SELECT city FROM courts where state = '".$state."' and city <> ''  and active = 1  group by city order by city" ;
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
			echo '{"notice": {"text":"No courts found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 


// GET All courts from the state and city selected
# /api/court/get_city/?state=OH&city=Akron
	$app->get('/api/court/get_city/', function (Request $request, Response $response) {
    $state = $request->getParam('state');
    $city = $request->getParam('city');
   # $param = " where courts.state like '%".$search."%' or courts.address like '%".$search."%' ";
	#$sql = "select * from court_ratings where court_id = $id";
	$sql = "SELECT * FROM courts where state = '".$state."' and city = '".$city."'  and active = 1  order by name" ;
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
			echo '{"notice": {"text":"No courts found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 


// GET CLOSEST COURT DISTANCE 41.055431, -81.512559
	#http://35.168.19.81/slimapp/public/api/court/get_closest/?longitude=-81.519382&latitude=41.0595357
#http://slimapp/api/court/get_closest/?longitude=-81.472360&latitude=41.106118
	$app->get('/api/court/get_closest/', function (Request $request, Response $response) {
 	$lon1 = $request->getParam('longitude');
 	$lat1 = $request->getParam('latitude');	
	$sql = "select * from courts where active = 1 "; #and id < 10
	try{
#		$lon1 = "-81.519382";		$lat1 = "41.0595357";
		#41.106118, -81.472360
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->query($sql) or die($sql);
		$user = $stmt->fetchAll(PDO::FETCH_OBJ);
		#echo count($user);
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
				if ($i == 11) break;
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

// ADD COURT RATINGS
//http://slimapp/api/court/add_ratings?court_id=1&user_id=2&rate=4
$app->post('/api/court/add_ratings', function (Request $request, Response $response) {
 
    $user_id = $request->getParam('user_id');
    $court_id = $request->getParam('court_id');
    $rate = $request->getParam('rate');
    $date = date("Y-m-d H:i:s");
	$sql = "Insert into court_ratings (court_id, user_id, rate, created_at)  values (:court_id, :user_id, :rate,:created_at)";

	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		$stmt->bindParam(':court_id',$court_id);
		$stmt->bindParam(':user_id',$user_id);
		$stmt->bindParam(':rate',$rate);
		$stmt->bindParam(':created_at',$date);
		$stmt->execute();

		$db = null;
		echo '{"notice": {"text":"Court Rating Added"}}';
	}
	catch(PDOException $e){
		if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
			    
				$sql = "Update court_ratings SET 
						 	rate = :rate, 
			 				created_at = :created_at
					 	where user_id = $user_id and court_id = $court_id";

				try{
					//get db
					$db = new db();
					// connect to db
					$db = $db->connect();

					$stmt = $db->prepare($sql);
					$stmt->bindParam(':rate',$rate);
					$stmt->bindParam(':created_at',$date);
					$stmt->execute();

					$db = null;
					echo '{"notice": {"text":"Court Rating Updated"}}';
				}
				catch(PDOException $e){
					echo '{"error": {"text":'.$e->getMessage().'}}';

				}
			}
			else
			{
				echo '{"error": {"text":'.$e->getMessage().'}}';
			}

	} 
 

}); 

 

// GET COURT RATINGS 
# http://slimapp/api/court/get_ratings/2
	$app->get('/api/court/get_ratings/{id}', function (Request $request, Response $response) {
    #echo "CUSTOMERS";
    $id = $request->getAttribute('id');
	#$sql = "select * from court_ratings where court_id = $id";
	$sql = "select court_id, sum(rate) as tot, count(rate) as cnt, round( sum(rate)  / (count(rate)),1) as stars from court_ratings where court_id = $id and active = 1";
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
			echo '{"notice": {"text":"ID not found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 




// GET COURTS INFO WITH RATINGS 
# http://slimapp/api/court/court_info_rating/?court_id=2
	$app->get('/api/court/court_info_rating/', function (Request $request, Response $response) {
    #echo "CUSTOMERS";
    $court_id = $request->getParam('court_id');
    $param = " where courts.id = '".$court_id."' and active = 1";
    $date = date("Y-m-d");
	#$sql = "select * from court_ratings where court_id = $id";
	$sql = "SELECT courts.*,  sum(rate) as tot, count(rate) as cnt, round( sum(rate)  / (count(rate)),1) as stars,
				(select count(user_id) from schedules where scheduled_date  = '".$date."'   and court_id = '".$court_id."') as number_of_players 
				FROM courts 
				LEFT JOIN court_ratings ON courts.id = court_ratings.court_id 
				".  $param ."
				group by courts.id " ;
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
			echo '{"notice": {"text":"No courts found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 



// SEARCH Courts by name or address 
#http://slimapp/api/court/search/Juilfs+Park
#http://slimapp/api/court/search/Greenwood
	$app->get('/api/court/search/{search}', function (Request $request, Response $response) {
    #echo "CUSTOMERS";
    $search = $request->getAttribute('search');
    $param = " where courts.name like '%".$search."%' or courts.address like '%".$search."%' and courts.active = 1";

	#$sql = "select * from court_ratings where court_id = $id";
	#$sql = 'SELECT courts.* FROM courts '.  $param .' order by id ' ;
	$sql = ' SELECT courts.id, courts.`name`, courts.address, courts.city, courts.state,	courts.country,	courts.`long`, courts.latd,	courts.active, 
			sum(court_ratings.rate) as tot, count(court_ratings.rate) as cnt, round( sum(court_ratings.rate)  / (count(court_ratings.rate)),1) as stars
			FROM courts
			LEFT JOIN court_ratings ON courts.id = court_ratings.court_id
			'.  $param .' 
			group by courts.id 
			order by tot desc ';
	#echo $sql;
	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

#$db->query("SET CHARACTER SET utf8;");
#$db->query("SET collation_connection = utf8_unicode_ci;");

		$stmt = $db->query($sql) or die($sql);
		$rec = $stmt->fetchAll(PDO::FETCH_OBJ);
		#var_dump($rec);
		if (count($rec) > 0) 
		{ 
			echo json_encode($rec);
		}
		else
		{
			echo '{"notice": {"text":"No courts found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 
 


// GET COURTS INFO NUMBER OF Players NOT COMPLETED
# http://slimapp/api/court/court_players/?court_id=2
	$app->get('/api/court/court_players/', function (Request $request, Response $response) {
    #echo "CUSTOMERS";
    $court_id = $request->getParam('court_id');
    $date = date("Y-m-d");
    $param = " where schedules.scheduled_date = '".$date."' and courts.id = $court_id and courts.active = 1 ";
    
	#$sql = "select * from court_ratings where court_id = $id";
	$sql = "SELECT
				courts.id,
				courts.`name`,
				courts.address,
				courts.city,
				courts.state,
				courts.country,
				courts.`long`,
				courts.latd, 
				schedules.id,
				schedules.user_id,
				schedules.court_id,
				schedules.checkin,
				schedules.checkout,
				schedules.scheduled_date,
				schedules.scheduled_time_from,
				schedules.scheduled_time_to,
				count(schedules.user_id)
				FROM
				courts
				INNER JOIN schedules ON courts.id = schedules.court_id
				".$param." 
				group by schedules.scheduled_date";
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
			echo '{"notice": {"text":"No courts found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 


// GET DISTNCE FROM COURT TO USERS CURRENT LOCATION  
	#http://35.168.19.81/slimapp/public/api/court/get_distance/?court_id=8&longitude=-81.501830&latitude=41.105803
#http://slimapp/api/court/get_distance/?court_id=8&longitude=-81.501830&latitude=41.105803
	$app->get('/api/court/get_distance/', function (Request $request, Response $response) {
 	$lon1 = $request->getParam('longitude');
 	$lat1 = $request->getParam('latitude');	
 	$court_id = $request->getParam('court_id');
	$sql = "select * from courts where active = 1 and id = '" . $court_id ."'";
	try{
  
		$db = new db(); 
		$db = $db->connect();

		$stmt = $db->query($sql) or die($sql);
		$user = $stmt->fetchAll(PDO::FETCH_OBJ); 
		if (count($user) > 0) 
		{ 
			$court_arrays = array();
			# compute distance from user's locaiton
			foreach ($user as $val)
			{
				
				$lon2 =  $val->long; $lat2 =  $val->latd; 
				$x = deg2rad( $lon1 - $lon2 ) * cos( deg2rad( $lat1 ) );
			    $y = deg2rad( $lat1 - $lat2 ); 
			    $dist = 6371000.0 * sqrt( $x*$x + $y*$y ); 
			    #$court_distance[$val->id] = $dist;
 
			}
			$court_distance = array('meters'=>$dist, 'km'=>($dist / 1000),'miles'=>($dist * 0.00062137), 'yards'=>($dist * 1.0936) );
		 
			echo json_encode($court_distance);  
		}
		else
		{
			echo '{"notice": {"text":"Court Not Found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}
 
}); 

