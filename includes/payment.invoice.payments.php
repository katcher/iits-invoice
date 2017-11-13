<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
// Previous payment details table header
$text .= '
<form method="post" action="'.$_SERVER['PHP_SELF'].'?f=insert">
<table border="1" cellspacing="0" cellpadding="5" title="invoice payment summary" id="invoice-payment-summary-detail">
	<tr valign="top">
		<td class="detail" colspan="7"><strong>Payment history</strong></td>
	</tr>
	<tr valign="top">
		<td class="detail" align="right" colspan="2"><b>Payment Status</b></td>
		<td align="left" class="detail" colspan="5">'. htmlspecialchars($assoc['status']).'</td>
	</tr>';
if(pg_num_rows($res)) {
$text .= '
	<tr valign="top">
		<td class="detail"><strong>Edit</strong></td>
		<td class="detail"><strong>Method</strong></td>
		<td class="detail"><strong>Payment type</strong></td>
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
		<td class="detail"><a href="?f=editpayment&amp;trans_no='.urlencode($record['trans_no']).'">'.htmlspecialchars($record['trans_no']).'</a></td>
		<td class="detail">'.htmlspecialchars($record['mop']).'</td>
		<td class="detail">'.htmlspecialchars($record['payment_type']).'</td>
		<td class="detail">'.htmlspecialchars($record['cheque_no']).'</td>
		<td class="detail" align="right">'.number_format($record['amount'], 2,'.','').'</td>
		<td class="detail">'.htmlspecialchars($record['entered_by']).'</td>
		<td class="detail">'.date("M. d Y \a\\t h:i a",$record['entered_on']).'</td>
	</tr>';
	}
	$text .= '
	<tr valign="top">
		<td class="detail">&nbsp;</td>
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
		<td class="detail">&nbsp;</td>
		<td class="detail">Amount Paid</td>
		<td class="detail" align="right">- $'.number_format($paidAmount, 2,'.','').'</td>				
		<td class="detail">&nbsp;</td>
		<td class="detail">&nbsp;</td>
	</tr>
	<tr valign="top">
		<td class="detail">&nbsp;</td>
		<td class="detail">&nbsp;</td>
		<td class="detail">&nbsp;</td>
		<td class="detail">Invoice total</td>
		<td class="detail" align="right">$ '.number_format($tots['invoice_total'], 2,'.','').'</td>				
		<td class="detail">&nbsp;</td>
		<td class="detail">&nbsp;</td>
	</tr>
	<tr valign="top">
		<td class="detail">&nbsp;</td>
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
		<td class="detail">&nbsp;</td>
		<td class="detail">Balance</td>
		<td class="detail" align="right">$ '. number_format((number_format($tots['invoice_total'], 2,'.','') - number_format($paidAmount, 2,'.','')), 2,'.','') .'</td>				
		<td class="detail">&nbsp;</td>
		<td class="detail">&nbsp;</td>
	</tr>';		
}
else {
	$text .= '
	<tr valign="top">
		<td class="detail" colspan="7"><strong>No payments on file</strong></td>
	</tr>';	
}
$text .= '
</table>
<table border="1" cellspacing="0" cellpadding="5" title="New invoice payment" id="new-invoice-payment">
	<tr valign="top">
		<td colspan="2"><strong>New Payment</strong></td>
	</tr>
	<tr valign="top">
		<td class="detail"><b>Payment Status</b></td>
		<td class="detail">'. makeStatus( $INVOICE['global-vars']['status'],'status', $assoc['status']).'</td>
	</tr>
	<tr valign="top">
		<td><b>Method of payment</b></td>
		<td class="detail">'. makeStatus( $INVOICE['global-vars']['mop'],'mop').'</td>
	</tr>
	<tr valign="top">
		<td><b>Payment Type (Payment / Deposit / Adjustment)</b></td>
		<td class="detail">'. makeStatus( $INVOICE['global-vars']['payment_type'],'payment_type').'</td>
	</tr>
	<tr valign="top">
		<td><b>Amount Paid</b></td>
		<td><input type=text name="amount_paid" size=15 maxlength=15 value=""></td>
	</tr>
	<tr valign="top">
		<td><b>Check Number</b></td>
		<td><input type=text name="cheque_no" size=15 maxlength=15 value=""></td>
	</tr>
</table>
<input type="hidden" name="inv_no" value="'.htmlspecialchars($assoc['inv_no']).'" />
<p><input type=submit value="update">
</form>
';
?>