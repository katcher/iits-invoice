<?php
session_start();
require("lib/invoicelib.php");
if( !( isset($INVOICE['user']['access']) and is_numeric($INVOICE['user']['access']) && ($INVOICE['user']['access'] < 2)) ) {
	$INVOICE['error'] = "This application is restricted";
	$_SESSION['INVOICE'] = $INVOICE;
	header("Location: ".$conf['access']['loginUrl'] );
	exit;
}
$app = "statement";
$f = returnVar('f');
$cust_no = returnVar('cust_no');
$inv_no = returnVar('inv_no');
$statetype = returnVar('statetype');


$results = '';
$res = '';
$sql = '';
if($f == "step2") {	
	if(!is_numeric($cust_no)) {
		$mytitle = "IITS Invoice application - [Customer statement Error - Customer omitted]";
		$results .= '
<h2>'.$mytitle.'</h2>
<p class="head">Select Customer From list below</p>
';
	}
	else {
		$sql = '';
		if($statetype == "customer") {
			$tmp = statementOfaccount(db_connect(),$cust_no);
			if($tmp['error']) {
				$mytitle = "IITS Invoice application - [Customer statement Error]";
			}
			else {
				$mytitle = "IITS Invoice application - [Customer statement ]";
			}
				$results .= '
<h2>'.$mytitle.'</h2>
';
				$results .= $tmp['msg'];
		}
		else {
			$sql  .= "SELECT invoice_assoc.inv_no, customers.b_comp_name FROM invoice_assoc, customers WHERE invoice_assoc.cust_no = ".pg_escape_string($cust_no)." AND (invoice_assoc.cust_no = customers.cust_no) ORDER BY invoice_assoc.inv_no ASC";
			//echo $sql;
			$res = execute_sql(db_connect(),$sql);
			if(pg_num_rows($res)) {
				$mytitle = "IITS Invoice application - [Select invoice]";			
				$results .= "
<h2>$mytitle</h2>
<p><strong>Invoices for ".htmlspecialchars(pg_result($res,0,'b_comp_name'))."</strong><br /><br />";
				for($i = 0; $i < pg_num_rows($res); $i++) {
					$record = pg_fetch_array($res,$i);
					$results .= '
<a href="'.htmlentities($_SERVER['PHP_SELF']).'?f=detail&inv_no='.urlencode($record['inv_no']).'">Invoice #'.$record['inv_no'].'</a><br />';
				}
				$results .= '
</p>';
			}
			else {
				$mytitle = "IITS Invoice application - [No invoices found]";
				$results .= '
<h2>'.$mytitle.'</h2>
';
			}		
		}					
	}
}
elseif($f == "detail") {
	$tmp = showStatement(db_connect(),$inv_no);	
	if(isset($tmp['error']) and $tmp['error'] == "t") {
		$INVOICE['error'] .= $tmp['msg'];
		$_SESSION['INVOICE'] = $INVOICE;
		header("Location: ".$tmp['url']);
		exit;	
	}
	else {
		if(is_numeric($tmp['inv_no'])) {
			$INVOICE['payment']['inv_no'] = $tmp['inv_no'];
		}
		
		$mytitle = "IITS Invoice application - [Payment Details by Invoice]";
		$results .= '
<h2 class="noprint">'.$mytitle.'</h2>
';		
		$results .= $tmp['msg'];
		$results .='<br />';	
	}	
}
else {
	$db = db_connect();
	$mytitle = "IITS Invoice application - [Customer Statement module]";	
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
	include("includes/statement.invoice.form.1.php");
}
$_SESSION['INVOICE'] = $INVOICE;
include("includes/template.php");
?>