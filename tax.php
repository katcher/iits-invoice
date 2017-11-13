<?php
session_start();
require("lib/invoicelib.php");
if(!( isset($INVOICE['user']['access']) and is_numeric($INVOICE['user']['access']))) {	
	$INVOICE['error'] = "This application is restricted";
	$_SESSION['INVOICE'] = $INVOICE;
	header("Location: ".$conf['access']['loginUrl'] );
	exit;
}
$f = returnVar('f');
$inv_no = returnVar('inv_no');

$app = "tax";
$results = '';
$res = '';
$sql = '';
if($f == "detail") {	
	if(!is_numeric($inv_no)){
		$mytitle = "IITS Invoice application - [Error - No invoice number specified]";		
		$results .= "<h2>$mytitle</h2>\n";
		include("includes/tax_form1.php");	
	}
	else {
		$mytitle = "IITS Invoice application - Invoice detail";	
		$db = db_connect();
		$res = execute_sql($db,"SELECT ALL * FROM invoice_assoc WHERE inv_no = ".pg_escape_string($inv_no));
		if(pg_num_rows($res) == 1) { 			
			// Invoice Found on file
			$assoc = pg_fetch_array($res,0); // Assign all invoice assoc data to array			
			$INVOICE['tax']['inv_no'] = '';
			$INVOICE['tax']['inv_no'] = $assoc['inv_no'];
			$res1 = execute_sql($db,"SELECT ALL * FROM customers WHERE cust_no = '".pg_escape_string($assoc['cust_no'])."'");
			$customer = '';
			if(pg_num_rows($res1) == 1) { 
				$customer = pg_fetch_array($res1,0); 
			}
			include("includes/tax_form2.php");
		}
		else {
			$INVOICE['tax']['inv_no'] = '';
			unset($INVOICE['tax']['inv_no']);
			$mytitle = "IITS Invoice application - [No invoice found]";
			$results .= "<h2>$mytitle</h2>\n";
			include("includes/tax_form1.php");			
		}		
	}	
}
elseif($f == "change") {
	$db = db_connect();
	$tmp = updateTaxstatus( $_POST, $INVOICE['tax']['inv_no'], $db );
	if($tmp['error']) {
		$mytitle = "Errors encountered";
	}
	else {
		$mytitle = "Status change";
	}
	$results .= '
<h2>'.$mytitle.'</h2>
<p>'.$tmp['msg'].'</p>
';
	$INVOICE['tax']['inv_no'] = '';
	unset($INVOICE['tax']['inv_no']);
	include("includes/tax_form1.php");
}
else {
	$db = db_connect();
	$INVOICE['tax']['inv_no'] = '';
	$mytitle = "IITS Invoice application - [Tax Exempt change]";	
	$results .= "<h2>$mytitle</h2>\n";
	include("includes/tax_form1.php");		
}
$_SESSION['INVOICE'] = $INVOICE;
include("includes/template.php");
?>