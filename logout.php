<?php
include('config.php');

if(isset($_SESSION['redir'])) {
	$redir = $_SESSION['redir'];
}else {
	$redir = 'home.php';
}

//Reset OAuth access token
$google_client->revokeToken();

//Destroy entire session data.
session_destroy();

//start session on web page
session_start();
$_SESSION['redir'] = $redir;

//redirect page to login.php
header('location: login.php');
die();
?>
