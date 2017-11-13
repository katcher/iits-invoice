<?php
session_start();
require("lib/invoicelib.php");
if(!(isset($INVOICE['user']['access']) and is_numeric($INVOICE['user']['access']))) {	
	$INVOICE['error'] = "This application is restricted";
	$_SESSION['INVOICE'] = $INVOICE;
	header("Location: ".$conf['access']['loginUrl'] );
	exit;
}
$app = "send";
$results = '';
$res = '';
$sql = '';
$f = returnVar('f');
$inv_no = returnVar('inv_no');


if($f == "send") {	
	if(!is_numeric($inv_no)){
		$mytitle = "IITS Invoice application - [Error - No invoice number specified]";		
		$results .= "<h2>$mytitle</h2>\n";		
		include("includes/send.form1.php");			
	}
	else {		
		$db = db_connect();
		$tmp = updateSendinvoice($db,$inv_no);
		$mytitle = $tmp['title'];
		$results .= '
<h2>'.$mytitle.'</h2>
';
		$tmp['error'] == "1" ? $results .= '<p class="error">' : $results .= '<p>';
		$results .= $tmp['msg'];
		$results .= '</p>';		
	}			
}
else {
	$db = db_connect();
	$mytitle = "IITS Invoice application - [Send invoice]";	
	$results .= "<h2>$mytitle</h2>\n";
	include("includes/send.form1.php");	
}
$_SESSION['INVOICE'] = $INVOICE;
include("includes/template.php");
?>