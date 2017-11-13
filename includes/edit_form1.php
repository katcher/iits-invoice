<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
// Edit Invoice Form 1
$results .= '
<ol class="instructions">
	<li>Select action</li>
	<li>Select the invoice number from the list below and click on <i>Find Invoice</i>.</li>
</ol>
';
if(!$db){
	$db = db_connect();
}

$tmp = makeEditinvoiceselect($db,"inv_no",$inv_no);

if(!strstr($tmp,"No customers")) {
	$results .= '
<form method=post action="'.$_SERVER['PHP_SELF'].'?f=detail" onsubmit="return checkEditempty(this)">
<p>
<b>Action</b><br />
<input type="radio" name="action" value="edit" checked="checked" /> <b>Edit Invoice</b><br /><input type="radio" name="action" value="add" /> <b>Add items</b>
<br /><br />
'.$tmp.'
<br /><br />
<input type="submit" value="Find invoice" class="submit" />
</p>
</form>
';
}
else {
	$results .= $tmp;
}
?>