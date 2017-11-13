<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$text .= '
<h2>Invoice number '.htmlspecialchars($assoc['inv_no']).'</h2>
<table border="1" cellspacing="0" cellpadding="5" title="invoice summary" id="invoice-summary-detail">
	<tr valign="top">
		<td class="detail"><strong>Subtotal:</strong></td>
		<td align="right" class="detail">'.number_format($tots['before_tax'], 2,'.','').'</td>
	</tr>
	<tr valign="top">
		<td class="detail" align="right"><b>GST @ </b>'.number_format((100*$assoc['gst_rate']),3,'.','').'%</td>
		<td align="right" class="detail">'.number_format($tots['gst_amt'], 2,'.','').'</td>
	</tr>
	<tr valign="top">
		<td class="detail" align="right"><b>PST @ </b>'.number_format((100*$assoc['pst_rate']),3,'.','').'%</td>
		<td align="right" class="detail">'.number_format($tots['pst_amt'], 2,'.','').'</td>
	</tr>';
if($assoc['ship_cost'] > 0)	{
	$text .= '
	<tr valign="top">
		<td class="detail" align="right"><b>Shipping</b></td>
		<td align="right" class="detail">'.number_format($tots['ship_cost'], 2,'.','').'</td>
	</tr>
	<tr valign="top">
		<td class="detail" align="right"><b>GST Shipping</b></td>
		<td align="right" class="detail">'.number_format($tots['ship_gst'], 2,'.','').'</td>
	</tr>
	<tr valign="top">
		<td class="detail" align="right"><b>PST Shipping</b></td>
		<td align="right" class="detail">'.number_format($tots['ship_pst'], 2,'.','').'</td>
	</tr>';
}
	$text .= '
	<tr valign="top">
		<td class="detail" align="right"><b>Total Due</b></td>
		<td align="right" class="detail">$ '.number_format($tots['invoice_total'], 2,'.','').'</td>
	</tr>
	<tr valign="top">
		<td class="detail" align="right"><b>Org code to debit</b></td>
		<td align="right" class="detail">'. htmlspecialchars($assoc['org_code']).'</td>
	</tr>
	<tr valign="top">
		<td class="detail" align="right"><b>Invoice sent on</b></td>
		<td align="right" class="detail">';
		if($assoc['inv_sent_on']) {
			$text .= date("M. d, Y \a\\t h:i a",$assoc['inv_sent_on']);
		}
		else {
			$text .= "Invoice sent date not available";
		}
		$text .= '</td>
	</tr>
</table>
';
?>