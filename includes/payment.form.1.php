<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
// Edit Invoice Form 1

if(!isset($db)){
	$db = db_connect();
}
$results .= '
<form method=post action="'.htmlentities($_SERVER['PHP_SELF']).'?f=detail">
<p>
'.makeInvoiceselect($db,"inv_no",$inv_no).'
</p><br /><br />
<input type="submit" value="Find invoice" class="submit" />
</form>
';
?>