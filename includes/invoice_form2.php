<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$results .= '
<form method="post" action="'.htmlentities($_SERVER['PHP_SELF']).'?f=calculate">
<input type="hidden" name="cust_no" value="'.htmlspecialchars($cust_no).'" />
<input type="hidden" name="display_cust_name" value="'.htmlspecialchars($customerdata['b_comp_name']).'" />
<table border="0" cellspacing="0" cellpadding="5" class="required">
	<tr valign="top">
		<td class="address"><b>Client Type</b></td>
		<td class="address"><select name="c_type" size="1">
			<option value="">Select</option>
			<option value="Internal"';
			if($c_type == "Internal") {
				$results .= ' selected="selected"';
			}
			$results .= '>Internal</option>
			<option value="External"';
			if($c_type == "External") {
				$results .= ' selected="selected"';
			}
			$results .= '>External</option>
	</select></td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Created by</b></td>
		<td class="address">'.htmlspecialchars($INVOICE['user']['name']).'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Tel. Extension</b></td>
		<td class="address"><input type="text" size="4" name="creator_ext" maxlength="10" value="'.htmlspecialchars($creator_ext).'" class="text" /></td>
	</tr>
	<tr valign="top">
		<td colspan="2" class="address"><b>Deposit Information</b></td>
	</tr>
	<tr valign="top">
		<td class="address"><b>ORG</b></td>
		<td class="address">'.$x.'</td>
	</tr>	
</table> 
<hr noshade="noshade" class="sep" /> 
<h3>Customer Billing Data</h3>
<table border="0" cellspacing="0" cellpadding="2">
	<tr valign="top">
		<td class="address"><b>Contact</b>: ';
		if(printIfexist($customerdata['b_contact'])){ 
			$results .= htmlspecialchars($customerdata['b_contact']); 
		}
		$results .= '</td>
	</tr>
	<tr valign="top">
		<td class="address" colspan="2">';
		if(printIfexist($customerdata['b_comp_name'])) {
			$results .= htmlspecialchars($customerdata['b_comp_name']); 
		}
		$results .= '</td>
	</tr>
	<tr valign="top">
		<td class="address" colspan="2">';
		if(printIfexist($customerdata['b_address'])) {
			$results .= htmlspecialchars($customerdata['b_address']); 
		}
		if(printIfexist($customerdata['b_suite'])) {
			$results .= ' # ' . htmlspecialchars($customerdata['b_suite']); 
		}
		$results .= '</td>
	</tr>
	<tr valign="top">
		<td class="address">';
		if(printIfexist($customerdata['b_city'])) {
			$results .= htmlspecialchars($customerdata['b_city']); 
		}
		if(printIfexist($customerdata['b_province'])) {
			$results .= ', ' . htmlspecialchars($customerdata['b_province']); 
		}
		$results .= '</td>
	</tr>
	<tr valign="top">
		<td class="address">';
		if(printIfexist($customerdata['b_country'])) {
			$results .= htmlspecialchars($customerdata['b_country']); 
		}
		if(printIfexist($customerdata['b_postal'])) {
			$results .= ' ' . htmlspecialchars($customerdata['b_postal']); 
		}
		$results .= '</td>
	</tr>
</table>
<h3>Ship To Address</h3>
<table border="0" cellspacing="0" cellpadding="2">';
if($customerdata['s_same'] == "t") {
	$results .= '
	<tr valign="top">
		<td class="address"><b>Same</b></td>
	</tr>';
}
else {
	if(printIfexist($customerdata['s_contact'])) { 
		$results .= '
	<tr valign="top">
		<td class="address"><b>Contact</b>:  ' . htmlspecialchars($customerdata['s_contact']).'</td>
	</tr>';
	}
	if(printIfexist($customerdata['s_comp_name'])) { 
		$results .= '
	<tr valign="top">
		<td class="address">' . htmlspecialchars($customerdata['s_comp_name']).'</td>
	</tr>';
	}
	if(printIfexist($customerdata['s_address'])) { 
		$results .= '
	<tr valign="top">
		<td class="address">' . htmlspecialchars($customerdata['s_address']);
		if(printIfexist($customerdata['s_suite'])){
			$results .= ' # '.htmlspecialchars($customerdata['s_suite']);
		}
		$results .= '</td>
	</tr>';
	}
	if(printIfexist($customerdata['s_city'])) {
		$results .= '	
	<tr valign="top">
		<td class="address">'.htmlspecialchars($customerdata['s_city']);
		if(printIfexist($customerdata['s_province'])) {
			$results .= ', ' . htmlspecialchars($customerdata['s_province']);
		}
		$results .= '</td>
	</tr>';
	}
	if(printIfexist($customerdata['s_country'])) {
		$results .= '	
	<tr valign="top">
		<td class="address">'.htmlspecialchars($customerdata['s_country']);
		if(printIfexist($customerdata['s_postal'])) {
			$results .= ', ' . htmlspecialchars($customerdata['s_postal']);
		}
		$results .= '</td>
	</tr>';
	}	
}
$results .= '
</table>
<hr noshade="noshade" class="sep" />
<h2>Invoice Detail</h2>
<table>
	<tr valign="top">
		<td><p class="address"><b>Customer PO#</b></p></td>
		<td><input type="text" class="text" size="10" name="purchase_ord" value="'.htmlspecialchars($purchase_ord).'" /></td>
	</tr>
	<tr valign="top">
		<td><p class="address"><b>Shipping Cost</b></p></td>
		<td><input type="text" class="text" size="10" name="ship_cost" value="'.htmlspecialchars($ship_cost).'" /></td>
	</tr>
</table>
';
$results .= '
<p style="border: thin solid black;">
New feature<br />
<strong>Tax exempt status</strong><br />
<input type="checkbox" name="pst_exempt" value="true" /> PST Exempt<br />
<input type="checkbox" name="gst_exempt" value="true" /> GST Exempt<br />
</p>

 <!-- Begin Invoice detail lines -->
<table border="1" cellspacing="0" cellpadding="5">
	<tr valign="top">
		<td class="address"><b>Line</b></td>
		<td class="address"><b>Service Date(dd/mm/yyyy)</b></td>
		<td class="address"><b>Coding Information</b></td>
		<td class="address"><b>Line Detail</b></td>
	</tr>
';
$counter = 0;
for($i = 0; $i < 12; $i++) {
	$counter++;
	$vname1 = "obj_code[$i]";
	$obj = makeVarselect($INVOICE['global-vars']['obj'], $vname1, $obj_code[$i]);
	$vname2 = "activity_code[$i]";
	$act = makeVarselect($INVOICE['global-vars']['activity'], $vname2, $activity_code[$i]);
	$results .= '
	<tr valign="top">
		<td class="address">'.$counter.'<input type="hidden" name="line['.$i.']" value="y" /></td>
		<td class="address"><input type="text" name="s_date['.$i.']" value="'.$s_date[$i].'" class="text" /></td>
		<td class="address"><b>Object Code</b><br />'.$obj.'<br /><b>Activity Code</b><br />'.$act.'</td>
		<td class="address">
			<table>
				<tr valign="top">
					<td class="address"><b>Qty:</b></td>
					<td><input type="text" class="text" name="qty['.$i.']" value="'.$qty[$i].'" size="10" /></td>
				</tr>
				<tr valign="top">
					<td class="address"><b>Desc:</b></td>
					<td><input type="text" class="text" name="desc['.$i.']" value="'.$desc[$i].'" size="50" /></td>
				</tr>
				<tr valign="top">
					<td class="address"><b>Unit $:</b></td>
					<td><input type="text" class="text" name="unit['.$i.']" value="'.$unit[$i].'" size="10" /></td>
				</tr>
			</table></td>
	</tr>';
}

$results .= '</table>
<br />
<input type="submit" value="Calculate invoice" name="calculate" />
</form>
<p><br /></p>
';
?>