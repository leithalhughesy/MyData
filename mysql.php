<?php
function OpenDB () {
	$link = mysqli_connect("127.0.0.1", "mydatauser", "Woxj38mcSwjf83nd", "mydata");
	
	if (mysqli_connect_errno()) {
		echo 'Failed to connect to MySQL: '.mysqli_connect_error();
		die();
	} else {
		return($link);
	}
}
function StrtoInt($str)
{
	return preg_replace("/[^-0-9.]/", '', $str);
}

?>