<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
// Potentially redundant, set to false and then removed in main create invoice EK Feb.23, 2005
$INVOICE['inserted'] = false;
unset($INVOICE['inserted']);

if(!isset($db)){
	$db = db_connect();
}
$selbox = makeCustomerselect($db);
$results .= '
<form method="get" action="'.htmlentities($_SERVER['PHP_SELF']).'" onSubmit="return checkEmpty(this)">
<input type="hidden" name="f" value="step2" />
<p>
'.$selbox.' &nbsp;
<input type="submit" value="Proceed" />
</p>
</form>
';
?>