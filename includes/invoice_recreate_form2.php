<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
if(!isset($db)) {
	$db = db_connect();
}	
$res = execute_sql($db,"SELECT ALL * FROM customers WHERE cust_no = ".pg_escape_string($cust_no));
if(pg_num_rows($res) != 1){
	$results .= '
<p class="error">Invalid customer selected</p>
';
	include("includes/invoice_form1.php");
}		
else {
	$customerdata = pg_fetch_array($res,0);
	$x = makeVarselect($INVOICE['global-vars']['org'], "org_code", $org_code); // select box creating org code
	 // Print errors if necessary
	if(isset($error)) {
		$results .= '<p class="error"><strong>Errors Encountered</strong><br />';
		$results .= $error;
		$results .= '</p>';
		$error = '';
	}
	include("includes/invoice_form2.php");			
}			
?>