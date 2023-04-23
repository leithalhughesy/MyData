<?php
	include('config.php');

	//This is for check user has login into system by using Google account, if  not then redirect to login.php
	if(!isset($_SESSION['access_token'])) {
		echo "It appears you're not logged in, refresh the page please.";
	} else {
		include('mysql.php');

		$link = OpenDB();
		if(isset($_GET['itemaccountfrom'])) {
			$query = "UPDATE `itemparents` SET `itemaccountfrom` = " . $_GET['itemaccountfrom'] . " WHERE `itemparents`.`itemparentid` = " . $_GET['itemparentid'];
		} else {
			$query = "UPDATE `itemparents` SET `itemaccountto` = " . $_GET['itemaccountto'] . " WHERE `itemparents`.`itemparentid` = " . $_GET['itemparentid'];
		}
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
