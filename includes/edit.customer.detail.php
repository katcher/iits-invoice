<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$list .= '
<form method="post" action="'.$_SERVER['PHP_SELF'].'?f=manage">
<input type="hidden" name="cust_no" value="'.htmlspecialchars(pg_result($res,0,'cust_no')).'" />
<p class="error">
<strong>Billing - Company name:</strong> &amp; <strong>Billing - Contact: </strong> are required fields.
</p>
<h3>Billing Information</h3>
<p>
<strong>Company name:</strong><br />
<input name="b_comp_name" type="text" size=40 maxlength=50 value="'.htmlspecialchars(pg_result($res,0,'b_comp_name')).'" /><br />
<strong>Contact: </strong><br />
<input name="b_contact" type="text" size=40 maxlength=50 value="'.htmlspecialchars(pg_result($res,0,'b_contact')).'" /><br />
Address: <br />
<input name="b_address" type="text" size=40 maxlength=50 value="'.htmlspecialchars(pg_result($res,0,'b_address')).'" /><br />
Suite: <br />
<input name="b_suite" type="text" size=10 maxlength=10 value="'.htmlspecialchars(pg_result($res,0,'b_suite')).'" /><br />
City: <br />
<input name="b_city" type="text" size=30 maxlength=30 value="'.htmlspecialchars(pg_result($res,0,'b_city')).'" /><br />
Province: <br />
<input name="b_province" type="text" size=30 maxlength=30 value="'.htmlspecialchars(pg_result($res,0,'b_province')).'" /><br />
Country: <br />
<input name="b_country" type="text" size=30 maxlength=30 value="'.htmlspecialchars(pg_result($res,0,'b_country')).'" /><br />
Postal code: <br />
<input name="b_postal" type="text" size=20 maxlength=20 value="'.htmlspecialchars(pg_result($res,0,'b_postal')).'" /><br />
Telephone: <br />
<input name="b_tel" type="text" size=15 maxlength=15 value="'.htmlspecialchars(pg_result($res,0,'b_tel')).'" /><br />
Fax: <br />
<input name="b_fax" type="text" size=15 maxlength=15 value="'.htmlspecialchars(pg_result($res,0,'b_fax')).'" /><br />
Email: <br />
<input name="b_email" type="text" size=40 maxlength=50 value="'.htmlspecialchars(pg_result($res,0,'b_email')).'" /><br />
</p>
<h3>Ship to Address</h3>
<p>
<input name="s_same" type=checkbox value="y"';
if(pg_result($res,0,'s_same') == "t" ) { 
	$list .= ' checked="checked"';
}
$list .= ' />  Yes Same as above.<br /><br />
Company name: <br />
<input name="s_comp_name" type="text" size=40 maxlength=50 value="'.htmlspecialchars(pg_result($res,0,'s_comp_name')).'" /><br />
Contact: <br />
<input name="s_contact" type="text" size=40 maxlength=50 value="'.htmlspecialchars(pg_result($res,0,'s_contact')).'" /><br />
Address: <br />
<input name="s_address" type="text" size=40 maxlength=50 value="'.htmlspecialchars(pg_result($res,0,'s_address')).'" /><br />
Suite: <br />
<input name="s_suite" type="text" size=10 maxlength=10 value="'.htmlspecialchars(pg_result($res,0,'s_suite')).'" /><br />
City: <br />
<input name="s_city" type="text" size=30 maxlength=30 value="'.htmlspecialchars(pg_result($res,0,'s_city')).'" /><br />
Province: <br />
<input name="s_province" type="text" size=30 maxlength=30 value="'.htmlspecialchars(pg_result($res,0,'s_province')).'" /><br />
Country: <br />
<input name="s_country" type="text" size=30 maxlength=30 value="'.htmlspecialchars(pg_result($res,0,'s_country')).'" /><br />
Postal Code: <br />
<input name="s_postal" type="text" size=20 maxlength=20 value="'.htmlspecialchars(pg_result($res,0,'s_postal')).'" /><br />
Telephone:<br />
<input name="s_tel" type="text" size=15 maxlength=15 value="'.htmlspecialchars(pg_result($res,0,'s_tel')).'" /><br />
Fax: <br />
<input name="s_fax" type="text" size=15 maxlength=15 value="'.htmlspecialchars(pg_result($res,0,'s_fax')).'" /><br /><br />
Created by: ' . htmlspecialchars(pg_result($res,0,'created_by')).'<br />
Created on: ' . date("d-m-Y h:i a",pg_result($res,0,'created_on')).'<br />
Updated by: ' . htmlspecialchars(pg_result($res,0,'updated_by')).'<br />
Updated on: ';
if(is_numeric(pg_result($res,0,'updated_on'))) {
	$list .= date("d-m-Y h:i a",pg_result($res,0,'updated_on'));
}
$list .= '
</p>
<p><input type=submit name="update" value="Update customer" /> <input type=submit name="delete" value="Delete customer" /></p>
</form>
';
?>