<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

 
#$app = new \Slim\App;

// GET ALL CUSTOMERS
$app->get('/api/customers', function (Request $request, Response $response) {
    #echo "CUSTOMERS";
	$sql = "select * from customers";

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

// GET SINGLE CUSTOMER
$app->get('/api/customer/{id}', function (Request $request, Response $response) {
    #echo "CUSTOMERS";
    $id = $request->getAttribute('id');
	$sql = "select * from customers where id = $id";

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



// ADD CUSTOMER
//http://slimapp/api/customer/add?first_name=aaden&last_name=altowon&phone=111-222-3333&email=aaden@yahoo.com&address=phase 1e&city=malolos&state=bulacan
$app->post('/api/customer/add', function (Request $request, Response $response) {
 

    $first_name = $request->getParam('first_name');
    $last_name = $request->getParam('last_name');
    $phone = $request->getParam('phone');
    $email = $request->getParam('email');
    $address = $request->getParam('address');
    $city = $request->getParam('city');
    $state = $request->getParam('state');

	$sql = "Insert into customers (first_name, last_name, phone, email, address, city, state) values 
	(:first_name, :last_name, :phone, :email, :address, :city, :state) ";

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
		$stmt->bindParam(':address',$address);
		$stmt->bindParam(':city',$city);
		$stmt->bindParam(':state',$state);

		$stmt->execute();

		$db = null;
		echo '{"notice": {"text":"Customers Added"}}';
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	} 

}); 


// UPDATE CUSTOMER
//http://slimapp/api/customer/update/3
//{"first_name":"aaden altowon","last_name":"spongebob27","phone":"111-222-3333","email":"aaden@yahoo.com","address":"phase 1e","city":"malolos","state":"bulacan"}
$app->put('/api/customer/update/{id}', function (Request $request, Response $response) {

    $id = $request->getAttribute('id');

    $first_name = $request->getParam('first_name');
    $last_name = $request->getParam('last_name');
    $phone = $request->getParam('phone');
    $email = $request->getParam('email');
    $address = $request->getParam('address');
    $city = $request->getParam('city');
    $state = $request->getParam('state');

	$sql = "Update customers SET 
			 	first_name = :first_name,
			 	last_name = :last_name, 
			 	phone = :phone, 
			 	email = :email,
			 	address =  :address,
			 	city = :city, 
			 	state = :state 
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
		$stmt->bindParam(':email',$email);
		$stmt->bindParam(':address',$address);
		$stmt->bindParam(':city',$city);
		$stmt->bindParam(':state',$state);

		$stmt->execute();

		$db = null;
		echo '{"notice": {"text":"Customers Updated"}}';
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}


}); 

//DELTE CUSTOMER
$app->delete('/api/customer/delete/{id}', function (Request $request, Response $response) {

    $id = $request->getAttribute('id');
 	$sql = "Delete from customers where id = $id ";

	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->prepare($sql); 

		$stmt->execute();
		$db = null;
		echo '{"notice": {"text":"Customer Deleted"}}';
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}


}); 