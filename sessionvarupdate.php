<?php
session_start();

if (isset($_GET['prefcashflowview'])) {
	$_SESSION['prefcashflowview'] = $_GET['prefcashflowview'];
	echo "Success";
} else {
	echo "Error, couldn't update session preference prefcashflowview";
}
?>