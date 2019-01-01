<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
#use \Psr\Http\Message\UploadedFileInterface as FileUpload;

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';
 
#http://slimapp/api/send_mail/forgotpass?email=alween@gmail.com
#http://slimapp/api/users/login?email=alween@gmail.com&pass=3561ed52
 
$app->get('/api/send_mail/forgotpass', function (Request $request, Response $response)  
{
	$email1 = $request->getParam('email'); 
	$newpass = bin2hex(openssl_random_pseudo_bytes(4));
	$password = password_hash($newpass, PASSWORD_DEFAULT); #md5($newpass);
	$email = "";
	$name = "";
	$date = date("Y-m-d H:i:s");
	$result = "";


	$sql = "select * from users where email = '". $email1."'";
	#var_dump($email1);
	try{
		//get db
		$db = new db();
		// connect to db
		$db = $db->connect();

		$stmt = $db->query($sql) or die($sql);
		$user = $stmt->fetchAll(PDO::FETCH_OBJ);
		if (count($user) > 0) 
		{ 
		 	
		$email = $user[0]->email;
		$name = $user[0]->first_name . ' ' . $user[0]->last_name;
		if (trim($name) == '')
			$name = $email;

		$sql2 = "Update users SET 
				 	password = :password,
				 	updated_at = :updated_at
			 	where id = ".$user[0]->id; 
			try{

				$db2 = new db();
				// connect to db
				$db2 = $db2->connect();

				$stmt2 = $db2->prepare($sql2) ;
				$stmt2->bindParam(':password',$password);
				$stmt2->bindParam(':updated_at',$date);
				 
				$stmt2->execute();

				$db2 = null;


					$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
					try {
					    //Server settings
					    $mail->SMTPDebug = 0;                                 // Enable verbose debug output
					    $mail->isSMTP();                                      // Set mailer to use SMTP
					    $mail->Host = 'smtp.yandex.com';  					  // Specify main and backup SMTP servers
					    $mail->SMTPAuth = true;                               // Enable SMTP authentication
					    $mail->Username = 'mailer@baller-city.com';           // SMTP username
					    $mail->Password = 'B@llerCity123';                    // SMTP password
					    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
					    $mail->Port = 587;                                    // TCP port to connect to

					    //Recipients
					    $mail->setFrom('mailer@baller-city.com', 'Baller City');
					    $mail->addAddress($email, $name);     // Add a recipient
					    #$mail->addAddress('ellen@example.com');               // Name is optional
					    #$mail->addReplyTo('info@example.com', 'Information');
					    #$mail->addCC('cc@example.com');
					    #$mail->addBCC('bcc@example.com');

					    //Attachments
					    #$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
					    #$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

					    //Content
					    $mail->isHTML(true);                                  // Set email format to HTML
					    $mail->Subject = 'Baller City Account';
					    $mail->Body    = 'Please use this password to login to your baller city account '.$email.'.
					    		<br><br>
								Here is your code: <b>'.$newpass.'</b> 
								<br><br>
								This is an automatically generated email. Please do not reply to this email. 
								<br><br>
								Thanks,
								<br><br><br>
								Baller City'; 
						#<b>in bold!</b>';
					    $mail->AltBody = 'Please use this password to login to your baller city account al*****@gmail.com.

							Here is your code: '.$newpass.'

							This is an automatically generated email. Please do not reply to this email. 


							Thanks,

							Baller City'; 

					    $mail->send();
					    $result = "Message has been sent";
					} catch (Exception $e) {
					    $result = "Message could not be sent. Mailer Error: ". $mail->ErrorInfo;
					}



				echo '{"notice": {"text":"User Password Changed. '.$result.'"}}';

			}
			catch(PDOException $e){
				echo '{"error": {"text":'.$e->getMessage().'}}';

			}
			 			 
		}
		else
		{
			echo '{"error": {"text":"User not found"}}';

		}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}}';

	}

	
    
    #var_dump($pwd);
    #die();
	/*
	mail server address — smtp.yandex.com
	connection security — SSL
	port — 465
	user: mailer@baller-city.com
	password: B@llerCity123
	*/
	

}); 