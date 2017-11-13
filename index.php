<?php
session_start();
require("lib/invoicelib.php");
if(!is_numeric($INVOICE['user']['access'])) {	
	$INVOICE['error'] = "This application is restricted";
	$_SESSION['INVOICE'] = $INVOICE;
	header("Location: ".$conf['access']['loginUrl']);
	exit;
}
?>