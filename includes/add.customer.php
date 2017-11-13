<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$results .= '
<form method="post" action="'. htmlentities($_SERVER['PHP_SELF']).'?f=insert">

<p class="error">
<strong>Billing - Company name:</strong> &amp; <strong>Billing - Contact: </strong> are required fields.
</p>
<h3>Billing Information</h3>
<p>
<strong>Company name:</strong><br />
<input name="b_comp_name" type="text" size="40" maxlength="50" value="'; isset($INVOICE['vars']['b_comp_name']) ? $results .= htmlspecialchars($INVOICE['vars']['b_comp_name']) : $results .= ''; $results .= '" /><br />
<strong>Contact: </strong><br />
<input name="b_contact" type="text" size="40" maxlength="50" value="'; isset($INVOICE['vars']['b_contact']) ? $results .=  htmlspecialchars($INVOICE['vars']['b_contact']) : $results .= ''; $results .= '" /><br />
Address: <br />
<input name="b_address" type="text" size="40" maxlength="50" value="'; isset($INVOICE['vars']['b_address']) ? $results .= htmlspecialchars($INVOICE['vars']['b_address']) : $results .= ''; $results .= '" /><br />
Suite: <br />
<input name="b_suite" type="text" size="10" maxlength="10" value="'; isset($INVOICE['vars']['b_suite']) ? $results .= htmlspecialchars($INVOICE['vars']['b_suite']) : $results .= ''; $results .= '" /><br />
City: <br />
<input name="b_city" type="text" size="30" maxlength="30" value="'; isset($INVOICE['vars']['b_city']) ? $results .= htmlspecialchars($INVOICE['vars']['b_city']) : $results .= ''; $results .= '" /><br />
Province: <br />
<input name="b_province" type="text" size="30" maxlength="30" value="'; isset($INVOICE['vars']['b_province']) ? $results .= htmlspecialchars($INVOICE['vars']['b_province']) : $results .= ''; $results .= '" /><br />
Country: <br />
<input name="b_country" type="text" size="30" maxlength="30" value="'; isset($INVOICE['vars']['b_country']) ? $results .= htmlspecialchars($INVOICE['vars']['b_country']) : $results .= ''; $results .= '" /><br />
Postal code: <br />
<input name="b_postal" type="text" size="20" maxlength="20" value="'; isset($INVOICE['vars']['b_postal']) ? $results .= htmlspecialchars($INVOICE['vars']['b_postal']) : $results .= ''; $results .= '" /><br />
Telephone: <br />
<input name="b_tel" type="text" size="15" maxlength="15" value="'; isset($INVOICE['vars']['b_tel']) ? $results .= htmlspecialchars($INVOICE['vars']['b_tel']) : $results .= ''; $results .= '" /><br />
Fax: <br />
<input name="b_fax" type="text" size="15" maxlength="15" value="'; isset($INVOICE['vars']['b_fax']) ? $results .= htmlspecialchars($INVOICE['vars']['b_fax']) : $results .= ''; $results .= '" /><br />
Email: <br />
<input name="b_email" type="text" size="40" maxlength="50" value="'; isset($INVOICE['vars']['b_email']) ? $results .= htmlspecialchars($INVOICE['vars']['b_email']) : $results .= ''; $results .= '" /><br />
</p>
<h3>Ship to Address</h3>
<p>
<input name="s_same" type="checkbox" value="y"'; isset($INVOICE['vars']['s_same']) and ($INVOICE['vars']['s_same'] == "y" )? $results .= ' checked="checked"' : $results .= ''; $results .= ' /> Yes Same as above.<br /><br />
Company name: <br />
<input name="s_comp_name" type="text" size="40" maxlength="50" value="'; isset($INVOICE['vars']['s_comp_name']) ? $results .= htmlspecialchars($INVOICE['vars']['s_comp_name']) : $results .= ''; $results .= '" /><br />
Contact: <br />
<input name="s_contact" type="text" size="40" maxlength="50" value="'; isset($INVOICE['vars']['s_contact']) ? $results .= htmlspecialchars($INVOICE['vars']['s_contact']) : $results .= ''; $results .= '" /><br />
Address: <br />
<input name="s_address" type="text" size="40" maxlength="50" value="'; isset($INVOICE['vars']['s_address']) ? $results .= htmlspecialchars($INVOICE['vars']['s_address']) : $results .= ''; $results .= '" /><br />
Suite: <br />
<input name="s_suite" type="text" size="10" maxlength="10" value="'; isset($INVOICE['vars']['s_suite']) ? $results .= htmlspecialchars($INVOICE['vars']['s_suite']) : $results .= ''; $results .= '" /><br />
City: <br />
<input name="s_city" type="text" size="30" maxlength="30" value="'; isset($INVOICE['vars']['s_city']) ? $results .= htmlspecialchars($INVOICE['vars']['s_city']) : $results .= ''; $results .= '" /><br />
Province: <br />
<input name="s_province" type="text" size="30" maxlength="30" value="'; isset($INVOICE['vars']['s_province']) ? $results .= htmlspecialchars($INVOICE['vars']['s_province']) : $results .= ''; $results .= '" /><br />
Country: <br />
<input name="s_country" type="text" size="30" maxlength="30" value="'; isset($INVOICE['vars']['s_country']) ? $results .= htmlspecialchars($INVOICE['vars']['s_country']) : $results .= ''; $results .= '" /><br />
Postal Code: <br />
<input name="s_postal" type="text" size="20" maxlength="20" value="'; isset($INVOICE['vars']['s_postal']) ? $results .= htmlspecialchars($INVOICE['vars']['s_postal']) : $results .= ''; $results .= '" /><br />
Telephone.: <br />
<input name="s_tel" type="text" size="15" maxlength="15" value="'; isset($INVOICE['vars']['s_tel']) ? $results .= htmlspecialchars($INVOICE['vars']['s_tel']) : $results .= ''; $results .= '" /><br />
Fax: <br />
<input name="s_fax" type="text" size="15" maxlength="15" value="'; isset($INVOICE['vars']['s_fax']) ? $results .= htmlspecialchars($INVOICE['vars']['s_fax']) : $results .= ''; $results .= '" /><br />
</p>
<input type="submit" value="Add customer">
</form>
';
$INVOICE['vars'] = array();
unset($INVOICE['vars']);
?>