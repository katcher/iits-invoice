<?php
session_start();
require("lib/invoicelib.php");
if( !(isset($INVOICE['user']['access']) && is_numeric($INVOICE['user']['access']) && ($INVOICE['user']['access'] <= 2)) ) {	
	$INVOICE['error'] = "This application is restricted.";
	$_SESSION['INVOICE'] = $INVOICE;
	header("Location: ".$conf['access']['loginUrl'] );
	exit;
}
$f = returnVar('f');
$app = '';
$mytitle = "Basic View";
$results = '';
$res = '';
$sql = '';
$sql .= "SELECT all * FROM invoice_assoc ORDER BY inv_no asc";
$res = execute_sql(db_connect(),$sql);
$results .= '
<table>
	<tr>
		<td>Invoice Number</td>
		<td>Status</td>
	</tr>';
for($i = 0; $i < pg_num_rows($res); $i++) {
	$record = pg_fetch_array($res,$i);
	$results .= '
	<tr>
		<td>'.$record['inv_no'].'</td>
		<td>'.$record['status'].'</td>
	</tr>';
}
$results .= '
</table>
';
include("includes/template.php");
$_SESSION['INVOICE'] = $INVOICE;
?>