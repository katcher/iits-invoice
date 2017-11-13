<?php
session_start();
require("lib/invoicelib.php");
if( !( isset($INVOICE['user']['access']) and is_numeric($INVOICE['user']['access']) && ($INVOICE['user']['access'] < 2)) ) {	
	$INVOICE['error'] = "This application is restricted";
	$_SESSION['INVOICE'] = $INVOICE;
	header("Location: ".$conf['access']['loginUrl'] );
	exit;
}
$app = "payment";
//$myVars = array_merge($_POST,$_GET);

$f = returnVar('f');
$inv_no = returnVar('inv_no');
$trans_no = returnVar('trans_no');

$results = '';
$res = '';
$sql = '';


if($f == "detail"){
	$tmp = adminPayment(db_connect(),$inv_no);
	
	if($tmp['error'] == "t") {
		$INVOICE['error'] .= $tmp['msg'];
		$_SESSION['INVOICE'] = $INVOICE;
		header("Location: ".$tmp['url']);
		exit;	
	}
	else {
		if(is_numeric($tmp['inv_no'])) {
			$INVOICE['payment']['inv_no'] = $tmp['inv_no'];
		}
		
		$mytitle = "IITS Invoice application - [Payment Detail]";
		$results .= '
<h2>'.$mytitle.'</h2>
';
		if(isset($INVOICE['error'])) {
			$results .= '<ol class="error">';
			$results .= $INVOICE['error'];
			$results .= '</ol>';
			$INVOICE['error'] = '';
			unset($INVOICE['error']);
		}		
		$results .= $tmp['msg'];		
	}	
}
elseif($f == "insert") {	
	$insert = insertPayment(db_connect(), $_POST, $INVOICE['payment']['inv_no'], $INVOICE['user']['name'], returnRemoteaddr());
	if($insert['error']) {
		$INVOICE['error'] .= $insert['msg'];
		$_SESSION['INVOICE'] = $INVOICE;
		
		$url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];		
		if(is_numeric($insert['inv_no'])) {
			$url .= "?f=detail&inv_no=".urlencode($insert['inv_no']);
		}	
		header("Location: $url");
		exit;		
	}
	else {
		
		$mytitle = "IITS Invoice application - [".$insert['title']."]";
		$results .= '
<h2>'.$mytitle.'</h2>
';
		$results .= "<p>".$insert['msg']."</p>\n";
	}	
}
elseif($f == "editpayment") {
	$tmp = editPaymentformdetail(db_connect(),$trans_no);
	if($tmp['error']) {
		$mytitle = "IITS Invoice application - [Payment Errors encountered]";
		$results .= '
<h2>'.$mytitle.'</h2>
<ol class="error">
'.$tmp['msg'].'
</ol>
';	
		$INVOICE['error'] = '';
		unset($INVOICE['error']);
	}
	else {
		$INVOICE['payment']['trans_no'] = $tmp['trans_no'];
		$mytitle = "Transaction detail ".$INVOICE['payment']['trans_no'];
		$results .= '
<h2>'.$mytitle.'</h2>
'.$tmp['msg'];
	}
}
elseif($f == "updatetransaction") {
	$tmp = applyeditpayment(db_connect(), $_POST, $INVOICE['payment']['trans_no'] );
	if($tmp['error']) {
		$mytitle = "IITS Invoice application - [Payment Errors encountered]";
		$results .= '
<h2>'.$mytitle.'</h2>
<ol class="error">
'.$tmp['msg'].'
</ol>
';	
		$INVOICE['error'] = '';
		unset($INVOICE['error']);
	}
	else {
		$mytitle = "IITS Invoice application - [Transaction detail]";
		$results .= '
<h2>'.$mytitle. ' ' . $INVOICE['payment']['trans_no']. '</h2>
'.$tmp['msg'];
		$INVOICE['payment']['trans_no'] = '';
		unset($INVOICE['payment']['trans_no']);
	}
	
}
else {
	$mytitle = "IITS Invoice application - [Payment Module]";
	$results .= '

<h2>'.$mytitle.'</h2>
';
	if(isset($INVOICE['error'])) {
		$results .= '<ol class="error">';
		$results .= $INVOICE['error'];
		$results .= '</ol>';
		$INVOICE['error'] = '';
		unset($INVOICE['error']);
	}
	include("includes/payment.form.1.php");
}
$_SESSION['INVOICE'] = $INVOICE;
include("includes/template.php");
?>