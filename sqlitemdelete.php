<?php
	include('config.php');

	//This is for check user has login into system by using Google account, if  not then redirect to login.php
	if(!isset($_SESSION['access_token'])) {
		echo "It appears you're not logged in, refresh the page please.";
	} else {
		include('mysql.php');

		$link = OpenDB();

		#Get item vars
		$mysqlquery = "SELECT * FROM items, itemparents WHERE itemid=".$_GET['itemid']." AND items.itemparentid = itemparents.itemparentid";
		$query = mysqli_query($link, $mysqlquery);
		while($array = mysqli_fetch_array($query)) {
			$itemdate = $array['itemdate'];
			$itemparentid = $array['itemparentid'];
			switch ($_GET['deletetype']) {
				case "1":
					#echo "Delete just this one";
					$delquery = "DELETE FROM `items` WHERE `items`.`itemid` = " . $_GET['itemid'];
				break;
				case "2":
					#echo "Delete all";
					$delquery = "DELETE FROM `itemparents` WHERE `itemparents`.`itemparentid` = " . $itemparentid;
				break;
				case "3":
					#echo "Delete from this date";
					$delquery = "DELETE FROM `items` WHERE `items`.`itemparentid` = " . $itemparentid . " AND itemdate >= '" . $itemdate . "'";
				break;
			}
			if (mysqli_query($link, $delquery)) {
				echo "Success";
			} else {
				echo "MySQL error was: " . mysqli_error($link);
				#echo $query;
			}
		}
		
		mysqli_close($link);
	}

?>
