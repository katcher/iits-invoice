<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
if(isset($INVOICE['error'])) {
		$results .= '<p class="error">';
		$results .= $INVOICE['error'];
		$results .= '</p>';
		$INVOICE['error'] = '';
}
$results .= '
<form method="post" action="'.$conf['access']['loginUrl'].'?f=verify">
<p>
<strong>Netname</strong><br />
<input type="text" name="netname" size="25" maxlength="75" /><br /><br />
<strong>Password</strong><br />
<input type="password" name="pwd" size="15" /><br /><br />
<input type="submit" value="Login" />
</p>
</form>
';
?>