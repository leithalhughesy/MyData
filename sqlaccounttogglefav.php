<?php
	include('config.php');

	//This is for check user has login into system by using Google account, if  not then redirect to login.php
	if(!isset($_SESSION['access_token'])) {
		echo "It appears you're not logged in, refresh the page please.";
	} else {
		include('mysql.php');

		$link = OpenDB();

		$query = "UPDATE `accounts` SET `accounts`.`accountfav` = " . $_GET['state'] . " WHERE `accounts`.`accountid` = " . $_GET['accountid'];
		if (mysqli_query($link, $query)) {
			echo "Success";
			#echo $query;
		} else {
			echo "MySQL error was: " . mysqli_error($link);
			#echo $query;
		}
		
		mysqli_close($link);
	}

?>
