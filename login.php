<?php

//Include Configuration File
include('config.php');
include('mysql.php');

$login_button = '';

//This $_GET["code"] variable value received after user has login into their Google Account redirect to PHP script then this variable value has been received
if(isset($_GET["code"])) {
	//It will Attempt to exchange a code for an valid authentication token.
	$token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);

	//This condition will check there is any error occur during geting authentication token. If there is no any error occur then it will execute if block of code/
	if(!isset($token['error'])) {
		//Set the access token used for requests
		$google_client->setAccessToken($token['access_token']);

		//Store "access_token" value in $_SESSION variable for future use.
		$_SESSION['access_token'] = $token['access_token'];

		//Create Object of Google Service OAuth 2 class
		$google_service = new Google_Service_Oauth2($google_client);

		//Get user profile data from google
		$data = $google_service->userinfo->get();

		//Below you can find Get profile data and store into $_SESSION variable
		if(!empty($data['given_name'])) {
			$_SESSION['user_first_name'] = $data['given_name'];
		}

		if(!empty($data['family_name'])) {
			$_SESSION['user_last_name'] = $data['family_name'];
		}

		if(!empty($data['email'])) {
			$_SESSION['user_email_address'] = $data['email'];
		}

		if(!empty($data['gender'])) {
			$_SESSION['user_gender'] = $data['gender'];
		}

		if(!empty($data['picture'])){
			$_SESSION['user_image'] = $data['picture'];
		}
		
		$link = OpenDB();

		$query = mysqli_query($link, "SELECT * FROM users WHERE email='".$_SESSION['user_email_address']."'");
		$array = mysqli_fetch_row($query);
		$_SESSION['uid'] = $array[0];
		$_SESSION['prefShowFavs'] = $array[3];
		$_SESSION['prefShowFavsAssets'] = $array[4];
		$_SESSION['prefcashflowview'] = "0";
		//Set the session timeout for 2 seconds
		$timeout = 2592000;
		//Set the maxlifetime of the session
		ini_set( "session.gc_maxlifetime", $timeout );
		//Set the cookie lifetime of the session
		ini_set( "session.cookie_lifetime", $timeout );

	}
}

//This is for check user has login into system by using Google account, if User not login into system then it will execute if block of code and make code for display Login link for Login using Google account.
if(!isset($_SESSION['access_token'])) {
	//Create a URL to obtain user authorization
	$login_button = '<a class="btn btn-dark" role="button" href="'.$google_client->createAuthUrl().'"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/32px-Google_%22G%22_Logo.svg.png" />&nbsp;&nbsp; Login with Google</a>';
} else {
	if(isset($_SESSION['redir'])) {
		header("Location: ".$_SESSION['redir']);
	}else {
		header("Location: home.php");
	}
	die();
}
	include('inchtmlheader.php');

   if($login_button <> '') {
		echo '<div align="center"><h3>You are not logged in, please login to continue<br /><br />'.$login_button . '</h3></div>';
	}
	include('inchtmlfooter.php');
?>