<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
if($customer) {
	// Print addresses
	$results .= '
<h2>Customer Information</h2>
<p>
<a href="print.php?f=print&amp;inv_no='.urlencode($assoc['inv_no']).'" target="_blank" rel="external">View Invoice - New window</a>
</p>
<p>
'. htmlspecialchars($customer['b_contact']) .'<br />
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
	$results .= '	</p>';
	
	if($assoc) {
		if(strtolower($assoc['status']) != "paid" && strtolower($assoc['status']) != "cancelled") {
			$results .= '
<form method="post" action="'.$_SERVER['PHP_SELF'].'?f=change">
<input type="hidden" name="inv_no" value="'.htmlspecialchars($assoc['inv_no']).'" />
<p><strong>Tax Status</strong><br />
GST EXEMPT?<br />Current rate for invoice '.(100*$assoc['gst_rate']).'% </br /><br />
<input type="radio" name="gst_exempt" value="true"';
		if($assoc['gst_rate'] == "0.00") {
			$results .= " checked=\"checked\"";
		}
		$results .= ' /> Yes<br />
<input type="radio" name="gst_exempt" value="false"';
		if($assoc['gst_rate'] != "0.00") {
			$results .= " checked=\"checked\"";
		}
		$results .= ' /> No<br /><br />
PST EXEMPT?<br />Current rate for invoice '.(100*$assoc['pst_rate']).'% </br /><br />
<input type="radio" name="pst_exempt" value="true"';
		if($assoc['pst_rate'] == "0.00") {
			$results .= " checked=\"checked\"";
		}
		$results .= ' /> Yes<br />
<input type="radio" name="pst_exempt" value="false"';
		if($assoc['pst_rate'] != "0.00") {
			$results .= " checked=\"checked\"";
		}
		$results .= ' /> No
</p>
';		
		$results .= '
<input type="submit" value="Update tax status" />
</form>
';
		}
		else {
			$results .= '
<h2>Invoice Tax status</h2>
<p>Unable to change as invoice is paid</p>
<p>Status<br />
GST EXEMPT? Current rate on invoice '.(100*$assoc['gst_rate']).'%<br />
PST EXEMPT? Current rate for invoice '.(100*$assoc['pst_rate']).'%<br />
';

		}	
	}
	else {
		$results .= '<p>No invoice details - ERROR</p>';
	}
}
else {
	$results .= '
	<p>Invalid customer address</</p>
';			
}


?>