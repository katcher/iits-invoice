<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
// Print Invoice Form 1

if(!$db){
	$db = db_connect();
}
$results .= '
<p>
Select invoice from list below and click on <i>Update send invoice date</i>.<br /><br /><strong>Note</strong>:<br />This application will simply update the invoice sent date for 
reporting purposes only.
</p>
<form method=post action="'.$_SERVER['PHP_SELF'].'?f=send" onsubmit="return cInvoice(this)">
'.makeInvoiceselect($db,"inv_no",$inv_no).'
<br /><br />
<input type="submit" value="Update send invoice date" class="submit" />
</form>
';
?>