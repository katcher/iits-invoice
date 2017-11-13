<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$text .= '
<h2>Invoice number '.htmlspecialchars($assoc['inv_no']).'</h2>
<p id="customer">
<strong>' . htmlspecialchars($assoc['b_comp_name']) . '</strong><br />
<strong>Contact:</strong> ' . htmlspecialchars($assoc['b_contact']) . '<br />
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
<p id="myinvoice">
<strong>Invoice Summary</strong><br /><br />
<strong class="heading">Invoice Subtotal:</strong> '.number_format($tots['before_tax'], 2,'.',',').'<br />
<strong class="heading-tx">GST '.number_format((100*$assoc['gst_rate']),2,'.','').'%</strong> '.number_format($tots['gst_amt'], 2,'.','').'<br />
<strong class="heading-tx">PST '.number_format((100*$assoc['pst_rate']),2,'.','').'%</strong> '.number_format($tots['pst_amt'], 2,'.','').'<br />';
if($assoc['ship_cost'] > 0 )	{
	$text .= '
<strong class="heading-ship">Shipping:</strong> ' . number_format($tots['ship_cost'], 2,'.','').'<br />
<strong class="heading-ship-tx">GST:</strong> ' . number_format($tots['ship_gst'], 2,'.','').'<br />
<strong class="heading-ship-tx">PST:</strong> ' . number_format($tots['ship_pst'], 2,'.','').'<br />';
}
	$text .= '
<strong class="heading-total">Total Due:</strong> $'.number_format($tots['invoice_total'], 2,'.',',').'<br />
</p>
<p id="myinvoice-misc">
<span id="org-code"><strong>Org Code:</strong> '. htmlspecialchars($assoc['org_code']).'<br /></span>
<strong>Invoice sent on:</strong> ';
if($assoc['inv_sent_on']) {
			$text .= date("M. d, Y \a\\t h:i a",$assoc['inv_sent_on']);
		}
		else {
			$text .= "Sent on date not available";
		}
		$text .= '
</p>
';
?>