<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
// Tax Exemption Status

if(!$db){
	$db = db_connect();
}
$results .= '
<p>Modify Tax Exempt status. Select invoice from list below and click <i>Find Invoice</i>.</p>
<p>You will only be able to modify unpaid invoices.</p>
<form method=post action="'.$_SERVER['PHP_SELF'].'?f=detail" onsubmit="return cInvoice(this)">
'.makeInvoiceselect($db,"inv_no",$inv_no).'
<br /><br />
<input type="submit" value="Find Invoice" class="submit" />
</form>
';
?>