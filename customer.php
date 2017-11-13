<?php
session_start();
require("lib/invoicelib.php");
if(!(isset($INVOICE['user']['access']) and is_numeric($INVOICE['user']['access']) and $INVOICE['user']['access'] <= "2")) {	
	$INVOICE['error'] = "This application is restricted";
	$_SESSION['INVOICE'] = $INVOICE;
	header("Location: ".$conf['access']['loginUrl']);
	exit;
}
/* Variables */
$f = returnVar('f');
$cust_no = returnVar('cust_no');
$update = returnVar('update');
$delete = returnVar('delete');
$results = '';
$res = '';
$sql = '';
if($f == "insert"){
	$app = "add-customer";
	$myResults = addCustomer(db_connect(),$_POST);	
	if($myResults['error']) {
		$INVOICE['error'] = $myResults['error'];
		$INVOICE['vars'] = $_POST;
		$_SESSION['INVOICE'] = $INVOICE;
		//header("Location: $PHP_SELF");
		header("Location: https://".$_SERVER['HTTP_HOST'] .$_SERVER['PHP_SELF']);
		exit;
	}
	else {
		$mytitle = "IITS Invoice application - [".$myResults['title']."]";		
		$results .= '
<h2>'.$mytitle.'</h2>
';
	}		
}
elseif($f == "edit"){
	$app = "edit-customer";
	$mytitle = "IITS Invoice application - [Edit customer]";		
	$results .= '
<h2>'.$mytitle.'</h2>
';
	$results .= printCustomers(db_connect());
}
elseif($f == "detail"){
	$app = "edit-customer";
	$mytitle = "IITS Invoice application - [";
	if(isset($INVOICE['error'])) {
		$mytitle .= "Edit customer - Error";
	}
	else {
		$mytitle .= "Edit customer detail";
	}
	$mytitle .= "]";
	$results .= '
<h2>'.$mytitle.'</h2>
';		
		if(isset($INVOICE['error'])) {
			$results .= '
<ol class="error">
'.$INVOICE['error'].'
</ol>
';
		$INVOICE['error'] = '';
		unset($INVOICE['error']);
	}
	$results .= printCustomers(db_connect(),$cust_no);
}
elseif($f == "manage"){
	$app = "edit-customer";
	$myResults = manageCustomer(db_connect(),$_POST);
	if($myResults['url']) {
		$INVOICE['error'] = $myResults['error'];
		$_SESSION['INVOICE'] = $INVOICE;
		header("Location: ".$myResults['url']);
		exit;
	}
	else {
		$mytitle = "IITS Invoice application - [".$myResults['title']."]";
		$results .= '
<h2>'.$mytitle.'</h2>
';
	}	
}
else {
	// Add customer form
	$app = "add-customer";
	$mytitle = "IITS Invoice application - [Add New Customer]";
	$results .= '
<h2>'.$mytitle.'</h2>
';	
	if(isset($INVOICE['error'])) {
		$results .= '
<ol class="error">
'.$INVOICE['error'].'
</ol>
';
		$INVOICE['error'] = '';
		unset($INVOICE['error']);
	}
	include("includes/add.customer.php");
}
$_SESSION['INVOICE'] = $INVOICE;
include("includes/template.php");
?>