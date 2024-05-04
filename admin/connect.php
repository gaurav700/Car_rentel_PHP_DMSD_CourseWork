<?php
	$dsn = 'mysql:host=127.0.0.1;port=3307;dbname=car_rental_project';
	$user = 'root';
	$pass = '';
	$option = array(
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
	);
	try
	{
		$con = new PDO($dsn,$user,$pass,$option);
		$con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		//echo 'Good Very Good !';
	}
	catch(PDOException $ex)
	{
		echo "Failed to connect with database ! ".$ex->getMessage();
		die();
	}
?>