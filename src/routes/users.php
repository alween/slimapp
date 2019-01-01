<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\UploadedFileInterface as FileUpload;

 
#$app = new \Slim\App;


// LOGIN USER
//http://slimapp/api/users/login/alween@gmail.com/adc
 #$app->get('/api/users/login/{email}/{pass}', function (Request $request, Response $response) {
	//http://slimapp/api/users/login?email=alween@gmail.com&pass=adc
#http://35.168.19.81/slimapp/public/api/users/login?email=alween@gmail.com&pass=cedae86b 
#http://slimapp/api/users/login?email=alween@gmail.com&pass=c6a90eb31 
#http://35.168.19.81/slimapp/public/api/users/login?email=eat_that_day@yahoo.com&password=q1q1q1 
	$app->post('/api/users/login', function (Request $request, Response $response) {
    $email = $request->getParam('email');
    $pass = $request->getParam('password');
    $passX = $request->getAttribute('password');
   	$sql = "select * from users where email = '".$email ."'"; 
	
	
	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->query($sql) or die($sql);
		$valid = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		if (count($valid) > 0) 
		{ 		
			$user_id = $valid[0]->user_id;
			$result = password_verify($pass, $valid[0]->password);
/*
						$sql2 = "Insert into logs (user_id, log, created_at)  
								values (:user_id, :log, :created_at)";

						try{
							//get db
							$db2 = new db();
							// connect to db
							$db2 = $db2->connect();
							$log = "LOGIN Email => " . $valid[0]->email . " ||| Result => " . $result . " |||  Pass => ". $pass. "  ||| ". $valid[0]->password. " ";
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

			

			//returns user info
			if ($result == true ) 
				echo json_encode($valid);
			else 
				echo '{"error": {"text":"Invalid Details 2 => '.$result.' -> '.$pass.' = '.$valid[0]->password.'"}}';
		}
		else
		{
			echo '{"error": {"text":"Invalid Details 3"}}';
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
	    $password = $request->getParam('password');
	    $password = password_hash($password, PASSWORD_DEFAULT); #md5($request->getParam('password')); 
	    $date = date("Y-m-d H:i:s");
		$sql = "Insert into users (email, password, created_at, updated_at) values (:email, :password, :created_at, :updated_at) ";
/*
					try{
							//get db
							$db2 = new db();
							// connect to db
							$db2 = $db2->connect();
							$user_id = "";
							$log = "SIGNUP Email => " . $email . " ||| Password1 => " . $request->getParam('password') . " |||  Password2 => ". $password. "   ";
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
			$stmt->bindParam(':email',$email);
			#$stmt->bindParam(':username',$username); 
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




// SIGN UP USER
//http://slimapp/api/users/signup2?email=aaden@yahoo.com&first_name=alween&last_name=delacruz&password=aaa&confirm=aaa 
$app->post('/api/users/signup2', function (Request $request, Response $response) {
 

	if ($request->getParam('password') <> $request->getParam('confirm')) 
	{
		echo '{"notice": {"text":"password not match"}}';
	}
	else
	{
	    $email = $request->getParam('email');
	    $first_name = $request->getParam('first_name');
	    $last_name = $request->getParam('last_name');
	    $password = $request->getParam('password');
	    $password = password_hash($password, PASSWORD_DEFAULT); #md5($request->getParam('password')); 
	    $date = date("Y-m-d H:i:s");
		$sql = "Insert into users (email, password, first_name, last_name, created_at, updated_at) values (:email, :password, :first_name, :last_name, :created_at, :updated_at) ";
 
		try{
			//get db
			$db = new db();
			// connect to db
			$db = $db->connect();

			$stmt = $db->prepare($sql); 
			$stmt->bindParam(':email',$email);
			$stmt->bindParam(':first_name',$first_name); 
			$stmt->bindParam(':last_name',$last_name);
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
			if (password_verify($oldpass,$user[0]->password) == true)
			{
				if ($request->getParam('newpass') <> $request->getParam('confirm')) 
				{
					echo '{"notice": {"text":"password not match"}}';
				}
				else
				{
				    $id = $request->getParam('id');
				    $password = password_hash($newpass, PASSWORD_DEFAULT); #md5($newpass);
				  	$date = date("Y-m-d H:i:s");

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


// UPDATE USER PROFILE with PHOTO
//http://slimapp/api/users/update/?id=2&first_name=aaden&last_name=altowon&address=phase1e&address2=wala&city=malolos&state=bulacan&zip=12345&country=Philippines&nickname=aaa&phone=111-222-3333&cellphone=111-222-3333
//http://slimapp/api/users/update/?id=2&first_name=aaden123&last_name=altowon123&address=phase1e123&address2=wala123&city=malolos123&state=bulacan123&zip=123&country=Philippines123&nickname=aaa123&phone=123&cellphone=123

// {"error": {"text":"Sorry, only JPG, JPEG, PNG & GIF files are allowed. Sorry, your file was not uploaded."}}
// {"error": {"text":"Sorry, your file is too large. Sorry, your file was not uploaded."}}
// {"notice": {"text":"User Profile Updated. The file Males_of_RoyaleADC_headshot.jpg has been uploaded."}} 
// {"notice": {"text":"User Profile Updated. No Image Uploaded "}}
$app->post('/api/users/update/', function (Request $request, Response $response) {

	#var_dump($fileupload); 
	$photo = $request->getUploadedFiles(); 

	$target_dir = '../public/images/users/';
	#$target_dir = "uploads/";
	$file_upload_status = "";
	$temp = explode(".", $_FILES["photo"]["name"]);

	$target_file = $target_dir . $request->getParam('id') . '.' . end($temp);
	$filename = $request->getParam('id') . '.' . end($temp);
	#$target_file = $target_dir . basename($_FILES["photo"]["name"]);
	$uploadOk = 1;
	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	if (count($temp) == 2)
	{
			$check = getimagesize($_FILES["photo"]["tmp_name"]);
		    if($check !== false) {
		        $file_upload_status = "File is an image - " . $check["mime"] . ".";
		        $uploadOk = 1;
		    } else {
		        $file_upload_status = "File is not an image.";
		        $uploadOk = 0;
		    }

		// Check if file already exists
		#if (file_exists($target_file)) {
		 ##   echo "Sorry, file already exists.";
		 ##   $uploadOk = 0;
		#}
		// Check file size
		if ($_FILES["photo"]["size"] > 500000) {
		    $file_upload_status = "Sorry, your file is too large.";
		    $uploadOk = 0;
		}
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
		    $file_upload_status = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		    $uploadOk = 0;
		}
	}
	else
	{
		$filename = "";
		$file_upload_status = " No Image Uploaded ";
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
	    $file_upload_status .= " Sorry, your file was not uploaded.";
	// if everything is ok, try to upload file
	    echo '{"error": {"text":"'.$file_upload_status.'"}}';
	} else {
	    if ((move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) || (count($temp) == 1)) {
	    	if (count($temp) <> 1)
	        $file_upload_status = " The file ". basename( $_FILES["photo"]["name"]). " has been uploaded.";


		    $id = $request->getParam('id');
		    $first_name = $request->getParam('first_name');
		    $last_name = $request->getParam('last_name');
		    $phone = $request->getParam('phone');
		    $cellphone = $request->getParam('cellphone');
		    #$email = $request->getParam('email');
		    $nickname = $request->getParam('nickname');
		    $address = $request->getParam('address');
		    $address2 = $request->getParam('address2');
		    $city = $request->getParam('city');
		    $state = $request->getParam('state');
		    $zip = $request->getParam('zip');
		    $country = $request->getParam('country');
		    $gender = $request->getParam('gender');
		    $photo = $filename;
		    $date = date("Y-m-d H:i:s");

			$sql = "Update users SET 
					 	first_name = :first_name,
					 	last_name = :last_name, 
					 	nickname = :nickname, 
					 	phone = :phone, 
					 	cellphone = :cellphone, 
					 	address =  :address,
					 	address2 =  :address2,
					 	city = :city, 
					 	state = :state , 
					 	zip = :zip, 
					 	state = :state, 
					 	country = :country, 
					 	gender = :gender, 
					 	photo = :photo, 
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
				$stmt->bindParam(':nickname',$nickname);
				$stmt->bindParam(':phone',$phone);
				$stmt->bindParam(':cellphone',$cellphone);
				$stmt->bindParam(':address',$address);
				$stmt->bindParam(':address2',$address2);
				$stmt->bindParam(':city',$city);
				$stmt->bindParam(':state',$state);
				$stmt->bindParam(':password',$password);
				$stmt->bindParam(':zip',$zip);
				$stmt->bindParam(':country',$country);
				$stmt->bindParam(':gender',$gender);
				$stmt->bindParam(':photo',$photo);
				$stmt->bindParam(':updated_at',$date);

				$stmt->execute();

				$db = null;
				echo '{"notice": {"text":"User Profile Updated. '.$file_upload_status.'"}}';
			}
			catch(PDOException $e){
				echo '{"error": {"text":'.$e->getMessage().'}}';

			}

	    } else {
	        $file_upload_status = "Sorry, there was an error uploading your file.";
	        echo '{"error": {"text":"'.$file_upload_status.'"}}';
	    }
	}

	#if (isset($FileUpload['file']))
	#{
	#	echo "<br>ok";
	#}
	#die();



}); 




// ADD USER
//http://slimapp/api/users/add?first_name=aaden&last_name=altowon&phone=111-222-3333&email=aaden@yahoo.com&address=phase1e&city=malolos&state=bulacan&nickname=aaa
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
	    $nickname = $request->getParam('nickname');
	    $address = $request->getParam('address');
	    $city = $request->getParam('city');
	    $state = $request->getParam('state');
	    $zip = $request->getParam('zip');
	    $country = $request->getParam('country');
	    $gender = $request->getParam('gender');

	    $password = password_hash($newpass, PASSWORD_DEFAULT); #md5($request->getParam('password'));
	    $date = date("Y-m-d H:i:s");
		$sql = "Insert into users (first_name, last_name, phone, email, nickname,address, city, state, password, zip, country, created_at, updated_at) values (:first_name, :last_name, :phone, :email, :nickname, :address, :city, :state, :password, :zip, :country, :gender, :created_at, :updated_at) ";

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
			$stmt->bindParam(':nickname',$nickname);
			$stmt->bindParam(':address',$address);
			$stmt->bindParam(':city',$city);
			$stmt->bindParam(':state',$state);
			$stmt->bindParam(':password',$password);
			$stmt->bindParam(':zip',$zip);
			$stmt->bindParam(':country',$country);
			$stmt->bindParam(':gender',$gender);
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
	#$sql = "select * from users where id = $id";
	$sql = "SELECT users.id, users.first_name, users.last_name, users.email, users.nickname, users.`password`, users.address, users.address2, users.state, users.city, users.zip, users.country, users.phone, users.cellphone, users.photo, users.photo, users.gender, sum(user_points.user_points) as user_points
			FROM users
			LEFT JOIN schedules ON users.id = schedules.user_id
			LEFT JOIN user_points ON schedules.user_id = user_points.user_id
			where users.id = $id
			group by users.id";
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
