<?php
session_start();
require(dirname(__FILE__)."/lib/invoicelib.php");
$app = '';

$f = returnVar('f');
$netname = returnVar('netname');
$pwd = returnVar('pwd');
$results = '';
$sql = '';
$res = '';
if($f == "verify") {
	$validated = validateLDAP($netname,$pwd);	
	if($validated['valid'] === true) {
		// Nename PWD Valid
		$db = db_connect();
		$INVOICE = validateAccess($validated['netname'],$db);
		// Verify if person is an admin of invoice system
		if($INVOICE['user']['valid']) {
			if(session_regenerate_id()) {
				$results .= '<p>Extended Security</p>';
			}				
			$INVOICE['global-vars']['org'] = makeVariablearray($db,"org_code"); 
			$INVOICE['global-vars']['obj'] = makeVariablearray($db,"obj_code");
			$INVOICE['global-vars']['activity'] = makeVariablearray($db,"activity_code");
			$INVOICE['global-vars']['status'] = $conf['invoice_status'];
			$INVOICE['global-vars']['mop'] = $conf['mop'];
			$INVOICE['global-vars']['payment_type'] = $conf['payment_type'];
			$mytitle = "IITS Invoice application - [Login success]";				
			$results .= '
<h2>'.$mytitle.'</h2>
';		
			include("includes/login.message.php");
			$results .= $text;
		}
		else {
			$INVOICE['error'] = $INVOICE['user']['msg'];
			$INVOICE['user'] = array();
			unset($INVOICE['user']);	
			$_SESSION['INVOICE'] = $INVOICE;
			header("Location: login.php");
			exit;
		}
	}
	else {
		$INVOICE['error'] = $validated['msg'];
		$INVOICE['user'] = array();
		unset($INVOICE['user']);
		$INVOICE['vars'] = array();
		unset($INVOICE['vars']);
		$_SESSION['INVOICE'] = $INVOICE;
		header("Location: login.php");
		exit;		
	}	
}
elseif($f == "logout") {
	$INVOICE['user'] = array();
	$INVOICE['edit'] = array();
	$INVOICE['global-vars'] = array();
	unset($INVOICE['user']);
	unset($INVOICE['edit']);
	unset($INVOICE['global-vars']);
	$INVOICE['error'] = "Logged out success";
	session_unset();
	session_destroy();
	header("Location: login.php");
	exit;
}	
else {	
	// Set Variables
	$INVOICE['user'] = array();
	$INVOICE['vars'] = array();
	unset($INVOICE['user']);
	unset($INVOICE['vars']);
	$mytitle = "IITS Invoice application - [Login]";
	$results .= '
<h2>'.$mytitle.'</h2>
';
if(isset($INVOICE['error'])) {
	$results .= '
<ul class="error">
	<li>'.$INVOICE['error'].'</li>
</ul>
';
	$INVOICE['error'] = '';
	unset($INVOICE['error']);
}
	include("includes/login.form.php");
}
$_SESSION['INVOICE'] = $INVOICE;
include("includes/template.php");
?>