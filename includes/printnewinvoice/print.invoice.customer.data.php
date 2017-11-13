<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
if($customer) {
	// Print addresses
	$results .= '<div class="address">
	<div class="billto">
	<strong>Bill to</strong>: '. htmlspecialchars($customer['b_contact']) .'<br />
	'. htmlspecialchars($customer['b_comp_name']);
if($customer['b_address']) {
	$results .= '<br />
	'.htmlspecialchars($customer['b_address']);
}
if($customer['b_suite']) {
	$results .= '
	<br /># ' . htmlspecialchars($customer['b_suite']);
}
if($customer['b_city']) {
	$results .= '	<br />
	' . htmlspecialchars($customer['b_city']);
}
if($customer['b_province']) {
	$results .= ", " . htmlspecialchars($customer['b_province']);
}

if($customer['b_country']) {
	$results .= '<br />
' . htmlspecialchars($customer['b_country']) . ' ';
}
if($customer['b_postal']) {
	$results .= ' ' . htmlspecialchars($customer['b_postal']);
}
$results .= '	</div>
	<div class="shipto">
	<strong>Ship to</strong>: ';
if($customer['s_contact']) {
	$results .= htmlspecialchars($customer['s_contact']);	
}
else {
	$results .= "Same";
}
if($customer['s_comp_name']) {
	$results .= '<br />
'. htmlspecialchars($customer['s_comp_name']) .'';
}
if($customer['s_address']) {
	$results .= '<br />' . htmlspecialchars($customer['s_address']);
}
if($customer['s_suite']) {
	$results .= '<br /># ' . htmlspecialchars($customer['s_suite']);
}
if($customer['s_city']) {
	$results .= '<br />' . htmlspecialchars($customer['s_city']);
}
if($customer['s_province']) {
	$results .= ", " . htmlspecialchars($customer['s_province']);
}

if($customer['s_country']) {
	$results .= '<br />
' . htmlspecialchars($customer['s_country']) . ' ';
}
if($customer['s_postal']) {
	$results .= ' '.htmlspecialchars($customer['s_postal']);
}
$results .= '
	</div>
</div>';
}
else {
	$results .= '
	<div class="address">Invalid customer address</div>
';			
}
$results .= '
<div class="clear-all">&nbsp;</div>
<div class="inv-po">Purchase order number: '.htmlspecialchars($assoc['purchase_ord']).'</div>';
?>