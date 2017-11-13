<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
// Previous payment details table header
$text .= '
<table border="0" cellspacing="0" cellpadding="5" title="invoice payment summary" id="summary-detail">
	<tr valign="top">
		<td colspan="6"><strong>Payment history</strong></td>
	</tr>
	<tr valign="top">
		<td align="right" colspan="1"><strong>Payment Status</strong></td>
		<td align="left" colspan="5">'. htmlspecialchars($assoc['status']).'</td>
	</tr>';
if(pg_num_rows($res)) {
$text .= '
	<tr valign="top">		
		<td class="detail"><strong>Method</strong></td>
		<td class="detail"><strong>Payment Type</strong></td>
		<td class="detail"><strong>Cheque Number</strong></td>
		<td class="detail"><strong>Amount</strong></td>
		<td class="detail"><strong>Entered By</strong></td>
		<td class="detail"><strong>Entered Date</strong></td>
	</tr>';	
	$paidAmount = 0;
	for($k = 0; $k < pg_num_rows($res); $k++) {
		$record = pg_fetch_array($res,$k);
		if(is_numeric($record['amount'])) {
			$paidAmount = $paidAmount + $record['amount'];
		}
		$text .= '
	<tr valign="top">
		<td class="detail">'.htmlspecialchars($record['mop']).'</td>
		<td class="detail">'.htmlspecialchars($record['payment_type']).'</td>
		<td class="detail">'.htmlspecialchars($record['cheque_no']).'&nbsp;</td>
		<td class="detail" align="right">'.number_format($record['amount'], 2,'.','').'</td>
		<td class="detail">'.htmlspecialchars($record['entered_by']).'</td>
		<td>'.date("M. d Y \a\\t h:i a",$record['entered_on']).'</td>
	</tr>';
	}
	$text .= '
	<tr valign="top">
		<td class="detail">&nbsp;</td>		
		<td class="detail">&nbsp;</td>
		<td class="detail">&nbsp;</td>
		<td class="detail" align="right"><hr noshade="noshade" size="1" /></td>				
		<td class="detail">&nbsp;</td>
		<td class="detail">&nbsp;</td>
	</tr>
	<tr valign="top">
		<td class="detail">&nbsp;</td>
		<td class="detail">&nbsp;</td>
		<td class="detail">Amount Paid</td>
		<td class="detail" align="right">- $'.number_format($paidAmount, 2,'.','').'</td>				
		<td class="detail">&nbsp;</td>
		<td class="detail">&nbsp;</td>
	</tr>
	<tr valign="top">
		<td class="detail">&nbsp;</td>
		<td class="detail">&nbsp;</td>
		<td class="detail">Invoice total</td>
		<td class="detail" align="right">$ '.number_format($tots['invoice_total'], 2,'.',',').'</td>				
		<td class="detail">&nbsp;</td>
		<td class="detail">&nbsp;</td>
	</tr>
	<tr valign="top">
		<td class="detail">&nbsp;</td>
		<td class="detail">&nbsp;</td>
		<td class="detail">&nbsp;</td>
		<td class="detail" align="right"><hr noshade="noshade" size="1" /></td>				
		<td class="detail">&nbsp;</td>
		<td class="detail">&nbsp;</td>
	</tr>	
	<tr valign="top">
		<td class="detail">&nbsp;</td>
		<td class="detail">&nbsp;</td>
		<td class="detail">Balance</td>
		<td class="detail" align="right">$ '. number_format(($tots['invoice_total'] - $paidAmount),2,'.',',') .'</td>				
		<td class="detail">&nbsp;</td>
		<td class="detail">&nbsp;</td>
	</tr>';		
}
else {
	$text .= '
	<tr valign="top">
		<td class="detail" colspan="6"><strong>No payments on file</strong></td>
	</tr>';	
}
$text .= '
</table>
';
?>