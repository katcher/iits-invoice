<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$results .= '
<form method="post" action="'.htmlentities($_SERVER['PHP_SELF']).'?f=calculate">
<input type="hidden" name="inv_no" value="'.htmlspecialchars($customerdata['inv_no']).'" />
<input type="hidden" name="inv_date" value="'.htmlspecialchars($customerdata['inv_date']).'" />
<input type="hidden" name="action" value="'.htmlspecialchars($action).'" />
<input type="hidden" name="cust_no" value="'.htmlspecialchars($customerdata['cust_no']).'" />
<input type="hidden" name="pst" value="'.htmlspecialchars($customerdata['pst_rate']).'" />
<input type="hidden" name="gst" value="'.htmlspecialchars($customerdata['gst_rate']).'" />
<input type="hidden" name="display_cust_name" value="'.htmlspecialchars($customerdata['b_comp_name']).'" />
<table border="0" cellspacing="0" cellpadding="5" class="required">
	<tr valign="top">
		<td class="address"><b>Invoice Number</b></td>
		<td class="address">'.htmlspecialchars($customerdata['inv_no']).'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Client Type</b></td>
		<td class="address"><select name="c_type" size="1">
			<option value="">Select</option>
			<option value="Internal"';
			if($customerdata['client_type'] == "Internal") {
				$results .= ' selected="selected"';
			}
			$results .= '>Internal</option>
			<option value="External"';
			if($customerdata['client_type'] == "External") {
				$results .= ' selected="selected"';
			}
			$results .= '>External</option>
	</select></td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Created by</b></td>
		<td class="address">'.htmlspecialchars($customerdata['creator']).'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Tel. Extension</b></td>
		<td class="address"><input type="text" size="4" name="creator_ext" maxlength="10" value="'.htmlspecialchars($customerdata['creator_ext']).'" class="text" /></td>
	</tr>
	<tr valign="top">
		<td colspan="2" class="address"><b>Deposit Information</b></td>
	</tr>
	<tr valign="top">
		<td class="address"><b>ORG</b></td>
		<td class="address">'.$x.'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Status</b></td>
		<td class="address">'.$y.'</td>
	</tr>
</table> 
<hr noshade="noshade" class="sep" /> 
<h2>Customer Billing Data</h2>
<table border="0" cellspacing="0" cellpadding="2">
	<tr valign="top">
		<td class="address"><b>Contact</b>: '.htmlspecialchars($customerdata['b_contact']).'</td>
	</tr>
	<tr valign="top">
		<td class="address" colspan="2">'.htmlspecialchars($customerdata['b_comp_name']).'</td>
	</tr>
</table>
<hr noshade="noshade" class="sep" />
<h2>Invoice Detail</h2>
<table>
	<tr valign="top">
		<td><p class="address"><b>Customer PO#</b></p></td>
		<td><input type="text" class="text" size="10" name="purchase_ord" value="'.htmlspecialchars($customerdata['purchase_ord']).'" /></td>
	</tr>
	<tr valign="top">
		<td><p class="address"><b>Shipping Cost</b></p></td>
		<td><input type="text" class="text" size="10" name="ship_cost" value="'.htmlspecialchars(number_format($customerdata['ship_cost'],2,".","")).'" /></td>
	</tr>
</table>
</p>
 <!-- Begin Invoice detail lines -->
<h3>Invoice Details</h3>
'.$taxStatus.'
<table border="1" cellspacing="0" cellpadding="5">
	<tr valign="top">
		<td class="address"><b>Line</b></td>
		<td class="address"><b>Delete</b></td>
		<td class="address"><b>Service Date(dd/mm/yyyy)</b></td>
		<td class="address"><b>Coding Information</b></td>
		<td class="address"><b>Line Detail</b></td>
	</tr>
';
$counter = 0;
$inv_total = 0;
for($i = 0; $i < pg_num_rows($res); $i++) {
	$record = pg_fetch_array($res,$i);
	
	// Calc Totals
	if(is_numeric($record['qty']) && is_numeric($record['unit_price']) && ($record['qty'] != 0)) {
		$extend = 0;
		$inv_total = $inv_total + ($record['qty'] * $record['unit_price']);
		$extend = ($record['qty'] * $record['unit_price']);
	}
	else {
		$extend = '';
	}
	
	$counter++;
	$vname1 = "obj_code[$i]";
	$obj = makeVarselect($INVOICE['global-vars']['obj'], $vname1, $record['obj_code']);
	$vname2 = "activity_code[$i]";
	$act = makeVarselect($INVOICE['global-vars']['activity'], $vname2, $record['activity_code']);
	$results .= '
	<tr valign="top">
		<td class="address">'.$counter.'<input type="hidden" name="line['.$i.']" value="'.htmlspecialchars($record['line_no']).'" /></td>
		<td class="address"><input type="checkbox" name="delete['.$i.']" value="'.htmlspecialchars($record['line_no']).'" class="text" /></td>
		<td class="address"><input type="text" name="s_date['.$i.']" value="'.date("d/m/Y", $record['s_date']).'" class="text" /></td>
		<td class="address"><b>Object Code</b><br />'.$obj.'<br /><b>Activity Code</b><br />'.$act.'</td>
		<td class="address">
			<table>
				<tr valign="top">
					<td class="address"><b>Qty:</b></td>
					<td><input type="text" class="text" name="qty['.$i.']" value="'.htmlspecialchars($record['qty']).'" size="10" /></td>
				</tr>
				<tr valign="top">
					<td class="address"><b>Desc:</b></td>
					<td><input type="text" class="text" name="desc['.$i.']" value="'.htmlspecialchars($record['item_description']).'" size="50" /></td>
				</tr>
				<tr valign="top">
					<td class="address"><b>Unit $:</b></td>
					<td><input type="text" class="text" name="unit['.$i.']" value="'.htmlspecialchars($record['unit_price']).'" size="10" /></td>
				</tr>
				<tr valign="top">
					<td class="address"><b>Extended:</b></td>
					<td>'.htmlspecialchars(number_format($extend,2,".","")).'</td>
				</tr>
			</table></td>
	</tr>';
}

$results .= '</table>
<br />
';
 // Totals
$tots = calcTotal($inv_total, $customerdata['gst_rate'], $customerdata['pst_rate'], $shipping = $customerdata['ship_cost'],null,null,$customerdata['inv_date']);
$results .= '
<table>
	<tr valign="top">
		<td colspan="2"><b>Totals</b></td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Invoice Sub-total</b></td>
		<td class="address" align="right"><b>'.htmlspecialchars(number_format($tots['before_tax'],2,".","")).'</b></td>
	</tr>
	<tr valign="top">
		<td class="address"><b>GST</b></td>
		<td class="address" align="right"><b>'.htmlspecialchars(number_format($tots['gst_amt'],2,".","")).'</b></td>
	</tr>
	<tr valign="top">
		<td class="address"><b>PST</b></td>
		<td class="address" align="right"><b>'.htmlspecialchars(number_format($tots['pst_amt'],2,".","")).'</b></td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Shipping</b></td>
		<td class="address" align="right"><b>'.htmlspecialchars(number_format($tots['ship_cost'],2,".","")).'</b></td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Ship GST</b></td>
		<td class="address" align="right"><b>'.htmlspecialchars(number_format($tots['ship_gst'],2,".","")).'</b></td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Ship PST</b></td>
		<td class="address" align="right"><b>'.htmlspecialchars(number_format($tots['ship_pst'],2,".","")).'</b></td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Total</b></td>
		<td class="address" align="right"><b>'.htmlspecialchars(number_format($tots['invoice_total'],2,".","")).'</b></td>
	</tr>
</table>
<p><br /></p>
';
$results .= '
<input type="hidden" name="last_updated" value="'.htmlspecialchars($customerdata['last_updated']).'" />
<input type="submit" value="Calculate invoice" />
</form>
<p><br /></p>
';
?>