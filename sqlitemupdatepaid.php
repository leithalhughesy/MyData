<?php
	include('config.php');

	//This is for check user has login into system by using Google account, if  not then redirect to login.php
	if(!isset($_SESSION['access_token'])) {
		echo "It appears you're not logged in, refresh the page please.";
	} else {
		include('mysql.php');

		$link = OpenDB();
		#update paid status on item
		$query = "UPDATE `items` SET `itempaid` = 1 WHERE `itemid` = " . $_GET['itemid'];
		if (mysqli_query($link, $query)) {
			#update account balance of from account
			$query = "UPDATE `accounts` SET `accountbalance` = accountbalance - " . $_GET['itemamount'] . " WHERE `accountid` = " . $_GET['itemaccountfrom'];
			if (mysqli_query($link, $query)) {
				#update account balance of to account
				$query = "UPDATE `accounts` SET `accountbalance` = accountbalance + " . $_GET['itemamount'] . " WHERE `accountid` = " . $_GET['itemaccountto'];
				if (mysqli_query($link, $query)) {
					echo "Success";
					#echo $query;
				} else {
					echo "MySQL error was: " . mysqli_error($link);
					#echo $query;
				}
			} else {
				echo "MySQL error was: " . mysqli_error($link);
				#echo $query;
			}
		} else {
			echo "MySQL error was: " . mysqli_error($link);
			#echo $query;
		}
		
		
		

		mysqli_close($link);
	}

?>
