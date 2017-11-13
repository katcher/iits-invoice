<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
// Print Invoice Form 1

if(!isset($db)){
	$db = db_connect();
}
$results .= '
<p>This section allows you to print and send a copy of the invoice to the customer.  Simply select the invoice number from the list below and click on <i>Find Invoice</i>.</p>
<p>The next screen you see will display the Invoice. From the <b>File</b> menu select Print. the invoice will be printed and you can mail the client the invoice.</p>
<form method=post action="'.$_SERVER['PHP_SELF'].'?f=print" target="printinvoice" onsubmit="return cInvoice(this)">
'.makeInvoiceselect($db,"inv_no",$inv_no).'
<br /><br />
<input type="submit" value="Print Invoice" class="submit" />
</form>
';
?>