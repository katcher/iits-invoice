<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
if(!isset($db)){
	$db = db_connect();
}
$selbox = makeCustomerselect($db);
$results .= '
<form method="get" action="'.$_SERVER['PHP_SELF'].'" onSubmit="return checkEmpty(this)">
<input type="hidden" name="f" value="step2" />
<p>
'.$selbox.' &nbsp;<br /><br />
<strong>Print by:</strong><br />
<input type="radio" name="statetype" value="invoice" checked="checked" /> By invoice<br />
<input type="radio" name="statetype" value="customer" /> By Customer<br /><br />
<input type="submit" value="Proceed" />
</p>
</form>
';
?>