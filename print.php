<?php
session_start();
require("lib/invoicelib.php");
if(!(isset($INVOICE['user']['access']) and is_numeric($INVOICE['user']['access']))) {	
	$INVOICE['error'] = "This application is restricted";
	$_SESSION['INVOICE'] = $INVOICE;
	header("Location: ".$conf['access']['loginUrl'] );
	exit;
}
$app = "print";
$f = returnVar('f');
$inv_no = returnVar('inv_no');
$stat = "";
$results = '';
$res = '';
$sql = '';

if($f == "print") {	
	if(!is_numeric($inv_no)){
		$mytitle = "IITS Invoice application - [Error - No invoice number specified]";		
		$results .= "<h2>$mytitle</h2>\n";
		$results .= "<p>Close this window and select an invoice</p>\n";
		include("includes/print_form1.php");		
	}
	else {		
		$db = db_connect();
		$res = execute_sql($db,"SELECT ALL * FROM invoice_assoc WHERE inv_no = ".pg_escape_string($inv_no));
		if(pg_num_rows($res) == 1) { 
			// Invoice Found on file
			$assoc = pg_fetch_array($res,0); // Assign all invoice assoc data to array			
			$res1 = execute_sql($db,"SELECT ALL * FROM customers WHERE cust_no = ".pg_escape_string($assoc['cust_no']));
			$customer = '';
			if(pg_num_rows($res1) == 1) { 
				$customer = pg_fetch_array($res1,0); 
			}
			$res2 = execute_sql($db, "SELECT ALL * FROM invoice_detail WHERE inv_no = ".pg_escape_string($assoc['inv_no']));					
			include("includes/printnewinvoice/print.invoice.header.php");					
			include("includes/printnewinvoice/print.invoice.customer.data.php");			
			include("includes/printnewinvoice/print.invoice.content.php");
			/* $results .= '
<script type="text/javascript" language="javascript"> 
if (window.self) window.print();
</script>
';*/
			include("includes/printnewinvoice/invoice.print.template.php");
			exit;
		}
		else {
			$mytitle = "IITS Invoice application - [No invoice found]";
			$results .= "<h2>$mytitle</h2>\n";
			$results .= "<p>Close this window and select an invoice</p>\n";
			include("includes/print_form1.php");		
		}		
	}	
}
else {
	$mytitle = "IITS Invoice application - [Invoice Print Routine]";	
	$results .= "<h2>$mytitle</h2>\n";
	$db = db_connect();
	include("includes/print_form1.php");		
}
$_SESSION['INVOICE'] = $INVOICE;
include("includes/template.php");
?>