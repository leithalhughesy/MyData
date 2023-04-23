<?php
	include('config.php');

	//This is for check user has login into system by using Google account, if  not then redirect to login.php
	if(!isset($_SESSION['access_token'])) {
		echo "It appears you're not logged in, refresh the page please.";
	} else {
		include('mysql.php');

		$link = OpenDB();
		
		if ($_GET['favs'] == '0') {
			$pref = "prefShowFavs";
		}else{
			$pref = "prefShowFavsAssets";
		}

		$query = "UPDATE `users` SET `" . $pref . "` = " . $_GET['checked'] . " WHERE `users`.`UID` = " . $_SESSION['uid'];
		if (mysqli_query($link, $query)) {
			if ($_GET['favs'] == '0') {
				$_SESSION['prefShowFavs'] = $_GET['checked'];
			}else{
				$_SESSION['prefShowFavsAssets'] = $_GET['checked'];
			}
			
			echo "Success";
			#echo $query;
		} else {
			echo "MySQL error was: " . mysqli_error($link);
			#echo $query;
		}
		
		mysqli_close($link);
	}

?>
