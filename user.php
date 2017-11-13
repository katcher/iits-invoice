<?php
session_start();
require("lib/invoicelib.php");
if(!(isset($INVOICE['user']['access']) and is_numeric($INVOICE['user']['access']) && $INVOICE['user']['access'] == "0")) {	
	$INVOICE['error'] = "This application is restricted";
	$_SESSION['INVOICE'] = $INVOICE;
	header("Location: ".$conf['access']['loginUrl']);
	exit;
}
$f = returnVar('f');
$user_name = returnVar('user_name');
$user_netname = returnVar('user_netname');
$user_email = returnVar('user_email');
$user_ext = returnVar('user_ext');
$access_level = returnVar('access_level');
$user_no = returnVar('user_no');
$delete = returnVar('delete');
$update = returnVar('update');


$results = '';
$res = '';
$sql = '';
if($f == "insert"){
	$app = "add-user";
	// Verify empty fields
	$error = '';
	if(!strlen(trim(chop($user_name)))) {
		$error .= "<li>User name omitted</li>\n";
	}
	if(!strlen(trim(chop($user_netname)))) {
		$error .= "<li>Netname omitted</li>\n";
	}
	else {
		$db = db_connect();
		$res = execute_sql($db,"SELECT user_no from invoice_users WHERE lower(user_netname) = '".pg_escape_string(trim(chop(strtolower($user_netname))))."'");
		if(pg_num_rows($res)) {
			$error .= "<li>Netname already exists</li>\n";
		}
		else {
			$tmp = getADdata($user_netname);
			if($tmp['error']) {
				$error .= "<li>".$tmp['msg']."</li>\n";
			}
		}		
	}
	if(!strlen(trim(chop($user_email)))) {
		$error .= "<li>Email address omitted</li>\n";
	}
	if(! (is_numeric($user_ext) && (strlen($user_ext) == 4))) {
		$error .= "<li>Extension must be numeric (4 numbers)</li>\n";
	}
	if(!( is_numeric($access_level) && ( ($access_level >= 0) && ($access_level <= 2)) ) ) {
		$error .= "<li>Access level omitted</li>\n";
	}
	if($error) {
		$INVOICE['error'] = $error;
		$INVOICE['vars'] = $_POST;
		$_SESSION['INVOICE'] = $INVOICE;
		header("Location: https://".$_SERVER['HTTP_HOST'] .$_SERVER['PHP_SELF']);
		exit;
	}
	else {
		$sql .= "INSERT INTO invoice_users (user_name, user_email, user_ext, access_level, user_netname) VALUES (";
		$sql .= "'".pg_escape_string(trim(chop($user_name)))."', ";
		$sql .= "'".pg_escape_string(trim(chop($user_email)))."', ";
		$sql .= pg_escape_string(trim(chop($user_ext))).", ";
		$sql .= pg_escape_string(trim(chop($access_level))).", ";
		$sql .= "'".pg_escape_string(trim(chop($user_netname)))."'";
		$sql .= ")";
		$res = execute_sql($db,$sql);
		$mytitle = "IITS Invoice application - [User Added]";
		$results .= '
<h2>'.$mytitle.'</h2>
<p>Please contact  '.htmlspecialchars($user_name).' to inform them of the login url</p>
';		
	}
		
}
elseif($f == "edit"){
	$app = "edit-user";
	$mytitle = "IITS Invoice application - [Edit User Module]";
	$results .= '
<h2>'.$mytitle.'</h2>
';		
	$results .= printUser(db_connect());
}
elseif($f == "detail"){
	$app = "edit-user";
	if(isset($INVOICE['error'])) {
		$results .= '
<ol class="error">
'.$INVOICE['error'].'
</ol>
';
		$INVOICE['error'] = '';
		unset($INVOICE['error']);
	}
	$mytitle = "IITS Invoice application - [Edit User Module - User detail]";
	$results .= '
<h2>'.$mytitle.'</h2>
';	
	$results .= printUser(db_connect(),$user_no);
}
elseif($f == "manage"){	
	$app = "edit-user";
	$delete ? $sub = array("delete"=> $delete, "user_no" => $user_no) : $sub = $_POST;
	$myResults = manageUser(db_connect(),$sub);	
	if($myResults['url']) {
		$INVOICE['error'] = $myResults['error'];
		$_SESSION['INVOICE'] = $INVOICE;
		header("Location: ".$myResults['url']);
		exit;
	}
	else {
		$mytitle = $myResults['title'];
		$results .= '
<h2>'.$mytitle.'</h2>
';
	}	
}
else {
	$app = "add-user";
	// Add user form
	$mytitle = "IITS Invoice application - [Add User Module]";
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
	include("includes/add_user_form1.php");
}
$_SESSION['INVOICE'] = $INVOICE;
include("includes/template.php");
?>