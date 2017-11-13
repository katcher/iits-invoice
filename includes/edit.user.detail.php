<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$list .= '
<form method="post" action="'.$_SERVER['PHP_SELF'].'?f=manage">
<input type="hidden" name="user_no" value="'.htmlspecialchars(pg_result($res,0,'user_no')).'" />
<p>
<strong>Name</strong><br />
<input type="text" name="user_name" size="30" maxlength="50" value="'.htmlspecialchars(pg_result($res,0,'user_name')).'" /><br />
<strong>Netname</strong><br />
<input type="text" name="user_netname" size="20" value="'.htmlspecialchars(pg_result($res,0,'user_netname')).'" /><br />
<strong>E-mail</strong><br />
<input type="text" name="user_email" size="30" maxlength="75" value="'.htmlspecialchars(pg_result($res,0,'user_email')).'" /><br />
<strong>Telephone Extension</strong><br />
<input type="text" name="user_ext" size="4" maxlength="4" value="'.htmlspecialchars(pg_result($res,0,'user_ext')).'" /><br />
<strong>Access Level</strong><br />
<select name="access_level" size=1>
	<option value="2"';
	if(pg_result($res,0,'access_level') == "2") {
		$list .= ' selected="selected"';
	}
	$list .= '>User - Create Invoices</option>
	<option value="1"';
	if(pg_result($res,0,'access_level') == "1") {
		$list .= ' selected="selected"';
	}
	$list .= '>Admin - Administer payments</option>
	<option value="0"';
	if(pg_result($res,0,'access_level') == "0") {
		$list .= ' selected="selected"';
	}
	$list .= '>Owner</option>
</select>
<br /><br />
<input type="submit" name="update" value="Update" /> <input type="submit" name="delete" value="Remove User" /> 
</p>
</form>
';
?>