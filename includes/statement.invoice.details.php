<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$text .= '
<h3>Invoice number '.htmlspecialchars($assoc['inv_no']).'</h3>
<p id="customer">
<strong>Customer Information</strong><br /><br />
<strong>' . htmlspecialchars($assoc['b_comp_name']) . '</strong><br /><br />
<strong>Contact:</strong> ' . htmlspecialchars($assoc['b_contact']) . '<br /><br />
' . htmlspecialchars($assoc['b_address']);
if(strlen($assoc['b_suite'])) {
	$text .= ' # ' . htmlspecialchars($assoc['b_suite']);
}
if(strlen($assoc['b_city'])) {
	$text .= '<br />' . htmlspecialchars($assoc['b_city']);
}
if(strlen($assoc['b_province'])) {
	$text .= ', ' . htmlspecialchars($assoc['b_province']);
}
if(strlen($assoc['b_country'])) {
	$text .= ', ' . htmlspecialchars($assoc['b_country']);
}
if(strlen($assoc['b_postal'])) {
	$text .= ', ' . htmlspecialchars($assoc['b_postal']);
}
if(strlen($assoc['b_tel'])) {
	$text .= '<br /><strong>Telephone</strong>: ' . htmlspecialchars($assoc['b_tel']);
}
if(strlen($assoc['b_fax'])) {
	$text .= '<br /><strong>Fax</strong>: ' . htmlspecialchars($assoc['b_fax']);
}
if(strlen($assoc['b_email'])) {
	$text .= '<br /><strong>E-mail</strong>: ' . htmlspecialchars($assoc['b_email']);
}
$text .= '
</p>

<table border="0" cellspacing="0" cellpadding="4" title="invoice summary" id="invoice-summary-detail">
	<tr valign="top">
		<td class="detail" colspan="2"><strong>Invoice Summary</strong></td>
	</tr>
	<tr valign="top">
		<td class="detail"><strong>Subtotal:</strong></td>
		<td align="right" class="detail">'.number_format($tots['before_tax'], 2,'.',',').'</td>
	</tr>
	<tr valign="top">
		<td class="detail"><b>GST @ </b>'.number_format((100*$assoc['gst_rate']),3,'.','').'%</td>
		<td align="right" class="detail">'.number_format($tots['gst_amt'], 2,'.','').'</td>
	</tr>
	<tr valign="top">
		<td class="detail"><b>PST @ </b>'.number_format((100*$assoc['pst_rate']),3,'.','').'%</td>
		<td align="right" class="detail">'.number_format($tots['pst_amt'], 2,'.','').'</td>
	</tr>';
if($assoc['ship_cost'] > 0)	{
	$text .= '
	<tr valign="top">
		<td class="detail"><b>Shipping</b></td>
		<td align="right" class="detail">'.number_format($tots['ship_cost'], 2,'.','').'</td>
	</tr>
	<tr valign="top">
		<td class="detail"><b>GST Shipping</b></td>
		<td align="right" class="detail">'.number_format($tots['ship_gst'], 2,'.','').'</td>
	</tr>
	<tr valign="top">
		<td class="detail"><b>PST Shipping</b></td>
		<td align="right" class="detail">'.number_format($tots['ship_pst'], 2,'.','').'</td>
	</tr>';
}
	$text .= '
	<tr valign="top">
		<td class="detail"><b>Total Due</b></td>
		<td align="right" class="detail">$ '.number_format($tots['invoice_total'], 2,'.',',').'</td>
	</tr>
	<tr valign="top" id="org-code">
		<td class="detail" align="right"><b>Org code to debit</b></td>
		<td align="right" class="detail">'. htmlspecialchars($assoc['org_code']).'</td>
	</tr>
	<tr valign="top">
		<td class="detail"><b>Invoice sent on</b>: </td>
		<td class="detail">';
		if($assoc['inv_sent_on']) {
			$text .= date("M. d, Y \a\\t h:i a",$assoc['inv_sent_on']);
		}
		else {
			$text .= "Sent on date not available";
		}
		$text .= '</td>
	</tr>
</table>
';
?>