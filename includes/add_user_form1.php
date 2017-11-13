<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$results .= '
<form method="post" action="'.$_SERVER['PHP_SELF'].'?f=insert">
<p>
<strong>Name</strong><br />
<input type="text" name="user_name" size="30" maxlength="50" value="'; isset($INVOICE['vars']['user_name']) ? $results .= htmlspecialchars($INVOICE['vars']['user_name']): $results .= ''; $results .= '" /><br />
<strong>Netname</strong><br />
<input type="text" name="user_netname" size="20" value="'; isset($INVOICE['vars']['user_netname']) ? $results .= htmlspecialchars($INVOICE['vars']['user_netname']):$results .= ''; $results .='" /><br />
<strong>E-mail</strong><br />
<input type="text" name="user_email" size="30" maxlength="75" value="'; isset($INVOICE['vars']['user_email']) ? $results .= htmlspecialchars($INVOICE['vars']['user_email']): $results .= ''; $results .= '" /><br />
<strong>Telephone Extension</strong><br />
<input type="text" name="user_ext" size="4" maxlength="4" value="'; isset($INVOICE['vars']['user_ext']) ? $results .= htmlspecialchars($INVOICE['vars']['user_ext']): $results.= ''; $results .= '" /><br />
<strong>Access Level</strong><br />
<select name="access_level" size=1>
	<option value="2"';
	if(isset($INVOICE['vars']['access_level']) and $INVOICE['vars']['access_level'] == "2") {
		$results .= ' selected="selected"';
	}
	$results .= '>User - Create Invoices</option>
	<option value="1"';
	if(isset($INVOICE['vars']['access_level']) and $INVOICE['vars']['access_level'] == "1") {
		$results .= ' selected="selected"';
	}
	$results .= '>Admin - Administer payments</option>
	<option value="0"';
	if(isset($INVOICE['vars']['access_level']) and $INVOICE['vars']['access_level'] == "0") {
		$results .= ' selected="selected"';
	}
	$results .= '>Owner</option>
</select>
<br /><br />
<input type="submit" value="Add User" />
</p>
</form>
';
if(isset($INVOICE['vars'])) {
	$INVOICE['vars'] = array();
	unset($INVOICE['vars']);
}
?>