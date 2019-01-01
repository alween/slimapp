<?php

class db{

	//properties
	private $dbhost = 'localhost';
	private $dbuser = 'root';
	private $dbpass = '';
	private $dbname = 'slimapp';

	#private $dbhost = 'myballercity.c3spvlbd7sgm.us-east-2.rds.amazonaws.com';
	#private $dbuser = 'Adm1n_Ball3r';
	#private $dbpass = 'Vt&423CG-57ZxsLNSdHz}HX';
	#private $dbname = 'Adm1n_Ball3r';


	//connect
	public function connect(){
		$mysql_connect_str = "mysql:host=$this->dbhost;dbname=$this->dbname;";
		$dbConnection = new PDO($mysql_connect_str, $this->dbuser,$this->dbpass);

		$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $dbConnection;
	}
}