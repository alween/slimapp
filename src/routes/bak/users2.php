<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

 
#$app = new \Slim\App;


// LOGIN USER
//http://slimapp/api/users/login/alween@gmail.com/adc
	$app->get('/api/users/login/{email}/{pass}', function (Request $request, Response $response) {
    $email = $request->getAttribute('email');
    $pass = $request->getAttribute('pass');
	$sql = "select * from users where email = '".$email ."' and password = '".md5($pass)."'";
 
	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->query($sql) or die($sql);
		$valid = $stmt->fetchAll(PDO::FETCH_OBJ);
		if (count($valid) > 0) 
		{ 	
			//returns user info
			echo json_encode($valid);
			#echo '{"notify": {"text":"User Found"},'.$valid.'}';
		}
		else
		{
			echo '{"notify": {"text":"Invalid"}}';
		}
		$db = null;
		#echo json_encode($customers);
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 



// SIGN UP USER
//http://slimapp/api/users/signup?email=aaden@yahoo.com&password=aaa&confirm=aaa
$app->post('/api/users/signup', function (Request $request, Response $response) {
 

	if ($request->getParam('password') <> $request->getParam('confirm')) 
	{
		echo '{"notice": {"text":"password not match"}}';
	}
	else
	{
	    $email = $request->getParam('email');
	    $username = $request->getParam('email');
	    $password = md5($request->getParam('password')); 
	    $date = date("Y-m-d h:i:s");
		$sql = "Insert into users (email, username, password, created_at, updated_at) values (:email, :username, :password, :created_at, :updated_at) ";

		try{
			//get db
			$db = new db();
			// connect to db
			$db = $db->connect();

			$stmt = $db->prepare($sql); 
			$stmt->bindParam(':email',$email);
			$stmt->bindParam(':username',$username); 
			$stmt->bindParam(':password',$password);
			$stmt->bindParam(':created_at',$date);
			$stmt->bindParam(':updated_at',$date);

			$stmt->execute();

			$db = null;
			echo '{"notice": {"text":"User Signup Completed"}}';
		}
		catch(PDOException $e){
			if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
			    echo '{"error": {"text":"Email already exists"}}';
			}
			else
			{
				echo '{"error": {"text":'.$e->getMessage().'}}';
			}
		} 
	}

}); 



// UPDATE or FORGOT or RESET Password
//http://slimapp/api/users/resetpass?oldpass=aaa&confirm=aaa&newpass=aaa&id=2
$app->post('/api/users/resetpass', function (Request $request, Response $response) {
 
	$oldpass = $request->getParam('oldpass');
	$newpass = $request->getParam('newpass');
	$confirm = $request->getParam('confirm');
	$id = $request->getParam('id');
	#echo "TEST"; die();
	$sql = "select * from users where id = $id";

	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->query($sql) or die($sql);
		$user = $stmt->fetchAll(PDO::FETCH_OBJ);
		if (count($user) > 0) 
		{ 
			if ($user[0]->password == (md5($oldpass)))
			{
				if ($request->getParam('newpass') <> $request->getParam('confirm')) 
				{
					echo '{"notice": {"text":"password not match"}}';
				}
				else
				{
				    $id = $request->getParam('id');
				    $password = md5($newpass);
				  	$date = date("Y-m-d h:i:s");

					$sql2 = "Update users SET 
							 	password = :password,
							 	updated_at = :updated_at
						 	where id = $id "; 
					try{
						
						$db2 = new db();
						// connect to db
						$db2 = $db2->connect();

						$stmt2 = $db2->prepare($sql2) ;
						$stmt2->bindParam(':password',$password);
						$stmt2->bindParam(':updated_at',$date);
						 
						$stmt2->execute();
						
						$db2 = null;
						
						echo '{"notice": {"text":"User Password Changed"}}';
					}
					catch(PDOException $e){
						echo '{"error": {"text":'.$e->getMessage().'}}';

					}


				}
			}
			else 
			{ 
				echo '{"error": {"text":"Old Password Does not Match"}}';
			}
		}
		else
		{
			echo '{"error": {"text":"ID not found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

	 

}); 


// UPDATE USER PROFILE
//http://slimapp/api/users/update/3
//{"first_name":"aaden altowon","last_name":"spongebob27","phone":"111-222-3333","address":"phase 1e","city":"malolos","state":"bulacan"}
$app->put('/api/users/update/{id}', function (Request $request, Response $response) {

    $id = $request->getAttribute('id');

    $first_name = $request->getParam('first_name');
    $last_name = $request->getParam('last_name');
    $phone = $request->getParam('phone');
    $email = $request->getParam('email');
    $username = $request->getParam('email');
    $address = $request->getParam('address');
    $city = $request->getParam('city');
    $state = $request->getParam('state');
    $zip = $request->getParam('zip');
    $country = $request->getParam('country');
    $date = date("Y-m-d h:i:s");

	$sql = "Update users SET 
			 	first_name = :first_name,
			 	last_name = :last_name, 
			 	phone = :phone, 
			 	address =  :address,
			 	city = :city, 
			 	state = :state , 
			 	zip = :zip, 
			 	state = :state, 
			 	country = :country, 
			 	updated_at = :updated_at
		 	where id = $id ";

	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		$stmt->bindParam(':first_name',$first_name);
		$stmt->bindParam(':last_name',$last_name);
		$stmt->bindParam(':phone',$phone);
		$stmt->bindParam(':address',$address);
		$stmt->bindParam(':city',$city);
		$stmt->bindParam(':state',$state);
		$stmt->bindParam(':password',$password);
		$stmt->bindParam(':zip',$zip);
		$stmt->bindParam(':country',$country);
		$stmt->bindParam(':updated_at',$date);

		$stmt->execute();

		$db = null;
		echo '{"notice": {"text":"User Profile Updated"}}';
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}


}); 




// ADD USER
//http://slimapp/api/users/add?first_name=aaden&last_name=altowon&phone=111-222-3333&email=aaden@yahoo.com&address=phase1e&city=malolos&state=bulacan&password=aaa&confirm=aaa
$app->post('/api/users/add', function (Request $request, Response $response) {
 

	if ($request->getParam('password') <> $request->getParam('confirm')) 
	{
		echo '{"notice": {"text":"password not match"}}';
	}
	else
	{
	    $first_name = $request->getParam('first_name');
	    $last_name = $request->getParam('last_name');
	    $phone = $request->getParam('phone');
	    $email = $request->getParam('email');
	    $username = $request->getParam('email');
	    $address = $request->getParam('address');
	    $city = $request->getParam('city');
	    $state = $request->getParam('state');
	    $zip = $request->getParam('zip');
	    $country = $request->getParam('country');

	    $password = md5($request->getParam('password'));
	    $date = date("Y-m-d h:i:s");
		$sql = "Insert into users (first_name, last_name, phone, email, username,address, city, state, password, zip, country, created_at, updated_at) values (:first_name, :last_name, :phone, :email, :username, :address, :city, :state, :password, :zip, :country, :created_at, :updated_at) ";

		try{
			//get db
			$db = new db();
			// connect to db
			$db = $db->connect();

			$stmt = $db->prepare($sql);
			$stmt->bindParam(':first_name',$first_name);
			$stmt->bindParam(':last_name',$last_name);
			$stmt->bindParam(':phone',$phone);
			$stmt->bindParam(':email',$email);
			$stmt->bindParam(':username',$username);
			$stmt->bindParam(':address',$address);
			$stmt->bindParam(':city',$city);
			$stmt->bindParam(':state',$state);
			$stmt->bindParam(':password',$password);
			$stmt->bindParam(':zip',$zip);
			$stmt->bindParam(':country',$country);
			$stmt->bindParam(':created_at',$date);
			$stmt->bindParam(':updated_at',$date);

			$stmt->execute();

			$db = null;
			echo '{"notice": {"text":"User Added"}}';
		}
		catch(PDOException $e){
			echo '{"error": {"text":'.$e->getMessage().'}}';

		} 
	}

}); 



// GET USER PROFILE 
# http://slimapp/api/users/1
	$app->get('/api/users/{id}', function (Request $request, Response $response) {
    #echo "CUSTOMERS";
    $id = $request->getAttribute('id');
	$sql = "select * from users where id = $id";

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
			echo '{"error": {"text":"ID not found"}}';
		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

}); 





// GET ALL USERS
$app->get('/api/users', function (Request $request, Response $response) {
    #echo "CUSTOMERS";
	$sql = "select * from users";

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
