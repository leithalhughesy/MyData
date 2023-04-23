<?php
	include('config.php');

	//This is for check user has login into system by using Google account, if  not then redirect to login.php
	if(!isset($_SESSION['access_token'])) {
		echo "It appears you're not logged in, refresh the page please.";
	} else {
		include('mysql.php');

		$link = OpenDB();

		#Add item parent
		$query = "INSERT INTO `itemparents` (`owneruid`,`itemparentname`,`itemparentamount`,`itemparentdate`,`itemaccountfrom`,`itemaccountto`,`itemparentrepeattype`,`itemparentrepeatfreq`,`itemparentrepeatdate`) VALUES ('".$_SESSION['uid']."','".$_GET['itemparentname']."','".$_GET['itemparentamount']."','".$_GET['itemparentdate']."','".$_GET['itemaccountfrom']."','".$_GET['itemaccountto']."','".$_GET['itemparentrepeattype']."','".$_GET['itemparentrepeatfreq']."','".$_GET['itemparentrepeatdate']."');" ;
		if (mysqli_query($link, $query)) {
			#echo mysqli_insert_id($link);
			#Add items
			switch ($_GET['itemparentrepeattype']) {
				case "0": #No recursion
					$query = "INSERT INTO `items` (`itemparentid`,`itemname`,`itemamount`,`itemdate`) VALUES ('".mysqli_insert_id($link)."','".$_GET['itemparentname']."','".$_GET['itemparentamount']."','".$_GET['itemparentdate']."');" ;
					if (mysqli_query($link, $query)) {
						#echo mysqli_insert_id($link);
						echo "Success";
					} else {
						echo "MySQL error was: " . mysqli_error($link);
					}
				break;
				case "2": #Weekly
					#$query = ""; #"INSERT INTO `items` (`itemparentid`,`itemname`,`itemamount`,`itemdate`) VALUES ";
					#for($i=0; $i < 2080; $i++) { #2080 weeks = 40 years
					#	$date = date_create($_GET['itemparentdate']);
					#	$query .= "INSERT INTO `items` (`itemparentid`,`itemname`,`itemamount`,`itemdate`) VALUES #('".mysqli_insert_id($link)."','".$_GET['itemparentname']."','".$_GET['itemparentamount']."','".date_format(date_add($date, #date_interval_create_from_date_string($i.' weeks')),'Y-m-d'). "');" ;
					#}
					#if (mysqli_multi_query($link, rtrim($query, ";"))) {
					#	#echo mysqli_insert_id($link);
					#	echo "Success";
					#} else {
					#	echo "MySQL error was: " . mysqli_error($link);
					#	#echo rtrim($query, ",");
					#}
					
					$begin = new DateTime($_GET['itemparentdate']);
					$end = new DateTime($_GET['itemparentrepeatdate']);

					$daterange = new DatePeriod($begin, new DateInterval('P' . $_GET['itemparentrepeatfreq'] . 'W'), $end);
					
				break;
				case "3": #Monthly
				
					$begin = new DateTime($_GET['itemparentdate']);
					$end = new DateTime($_GET['itemparentrepeatdate']);

					$daterange = new DatePeriod($begin, new DateInterval('P' . $_GET['itemparentrepeatfreq'] . 'M'), $end);
					
				break;
				case "4": #Annually
					
					$begin = new DateTime($_GET['itemparentdate']);
					$end = new DateTime($_GET['itemparentrepeatdate']);

					$daterange = new DatePeriod($begin, new DateInterval('P' . $_GET['itemparentrepeatfreq'] . 'Y'), $end);

				break;
			}
			if ($_GET['itemparentrepeattype'] != "0") {

				$query = "INSERT INTO `items` (`itemparentid`,`itemname`,`itemamount`,`itemdate`) VALUES ";

				foreach($daterange as $date){
					$query .= "('".mysqli_insert_id($link)."','".$_GET['itemparentname']."','".$_GET['itemparentamount']."','". $date->format("Y-m-d") . "')," ;
				}
				
				if (mysqli_query($link, rtrim($query, ","))) {
					echo "Success";
					#echo mysqli_insert_id($link);
				} else {
					echo "MySQL error was: " . mysqli_error($link);
					#echo rtrim($query, ",");
				}
			}
			
		} else {
			echo "MySQL error was: " . mysqli_error($link);
		}
		
		mysqli_close($link);
	}

?>
