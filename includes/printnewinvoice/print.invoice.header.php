<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
if($assoc['status'] == "Paid"){
	$stat = " id=\"paid\"";
}
$results .= '
<!-- +++++ Begin Page Print +++++ -->
<div class="print-invoice-page">
<div class="invoice-image-header">
	<div class="logo-address">
	<img src="./images/invoice_1a.gif" width="136" height="93" alt="iits image" title="IITS printable Header" />'._INVOICE_ADDR_.'
	</div>
	<div class="logo-invoice-number">Invoice # '. htmlspecialchars($assoc['inv_no']).'</div>
	<div class="clear-all">&nbsp;</div>
</div>
<div class="inv-date">Invoice date: '.date("F d, Y",$assoc['inv_date']).'</div>
<div class="clear-all">&nbsp;</div>
';
?>