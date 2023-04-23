<?php
	include('config.php');

	//This is for check user has login into system by using Google account, if  not then redirect to login.php
	if(!isset($_SESSION['access_token'])) {
		echo "It appears you're not logged in, refresh the page please.";
	} else {
		include('mysql.php');

		$link = OpenDB();

		$query = "INSERT INTO `accounts` (`owneruid`,`accountname`,`accountbalance`,`accounttype`) VALUES ('".$_SESSION['uid']."','".$_GET['name']."','".$_GET['balance']."','".$_GET['type']."');" ;
		#$result = mysqli_query($link, $query);
		if (mysqli_query($link, $query)) {
			#echo "It worked?";
			#echo $query;
		} else {
			echo "MySQL error was: " . mysqli_error($link);
		}
		
		mysqli_close($link);
	}

?>
