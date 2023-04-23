<?php
	include('config.php');

	//This is for check user has login into system by using Google account, if  not then redirect to login.php
	if(!isset($_SESSION['access_token'])) {
		echo "It appears you're not logged in, refresh the page please.";
	} else {
		include('mysql.php');

		$link = OpenDB();
		if (strpos($_GET['value'],'$') !== false) {
			$setvalue = StrtoInt($_GET['value']);
		} else {
			$setvalue = $_GET['value'];
		}

		$query = "UPDATE ".$_GET['table']." SET ".$_GET['setfield']." = '".$setvalue."' WHERE ".$_GET['idfield']." = '".$_GET['id']."'" ;
		#$result = mysqli_query($link, $query);
		if (mysqli_query($link, $query)) {
			echo "Success";
		} else {
			echo "MySQL error was: " . mysqli_error($link);
		}
		
		mysqli_close($link);
	}

?>
