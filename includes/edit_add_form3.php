<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$results .= '
<form method="post" action="'.$_SERVER['PHP_SELF'].'?f=re_calculate">
<input type="hidden" name="inv_no" value="'.htmlspecialchars($customerdata['inv_no']).'" />
<input type="hidden" name="inv_date" value="'.htmlspecialchars($customerdata['inv_date']).'" />
<input type="hidden" name="action" value="'.htmlspecialchars($myVars['action']).'" />
<input type="hidden" name="cust_no" value="'.htmlspecialchars($customerdata['cust_no']).'" />
<input type="hidden" name="display_cust_name" value="'.htmlspecialchars($customerdata['b_comp_name']).'" />
<input type="hidden" name="gst" value="'.htmlspecialchars($customerdata['gst_rate']).'" />
<input type="hidden" name="pst" value="'.htmlspecialchars($customerdata['pst_rate']).'" />
<table border="0" cellspacing="0" cellpadding="5" class="required">
	<tr valign="top">
		<td class="address"><b>Invoice Number</b></td>
		<td class="address">'.htmlspecialchars($customerdata['inv_no']).'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Client Type</b></td>
		<td class="address">'.htmlspecialchars($customerdata['client_type']).'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Created by</b></td>
		<td class="address">'.htmlspecialchars($customerdata['creator']).'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Tel. Extension</b></td>
		<td class="address">'.htmlspecialchars($customerdata['creator_ext']).'</td>
	</tr>
	<tr valign="top">
		<td colspan="2" class="address"><b>Deposit Information</b></td>
	</tr>
	<tr valign="top">
		<td class="address"><b>ORG</b></td>
		<td class="address">'.$org['text'].'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Invoice Status</b></td>
		<td class="address">'.htmlspecialchars($customerdata['status']).'</td>
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
		<td class="address">'.htmlspecialchars($customerdata['purchase_ord']).'</td>
	</tr>
	<tr valign="top">
		<td><p class="address"><b>Shipping Cost</b></p></td>
		<td class="address"><input type="text" name="ship_cost" value="'.htmlspecialchars($customerdata['ship_cost']).'" /></td>
	</tr>
</table>

';
$results .= '
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
$inv_total = 0;
if(!pg_num_rows($res)){
	$results .= '
	<tr valign="top">
		<td colspan="4">No existing detail lines</td>
	</tr>';
}
/*
else {
	
for($i = 0; $i < pg_num_rows($res); $i++) {
	$record = pg_fetch_array($res,$i);
	
	// Calc Totals
	if(is_numeric($record['qty']) && is_numeric($record['unit_price']) && ($record['qty'] > 0)) {
		$extend = 0;
		$inv_total = $inv_total + ($record['qty'] * $record['unit_price']);
		$extend = ($record['qty'] * $record['unit_price']);
	}
	else {
		$extend = '';
	}
	
	$counter++;	
	$obj1 = codeReturn($INVOICE['global-vars']['obj'], $record['obj_code']);
	$act1 = codeReturn($INVOICE['global-vars']['activity'], $record['activity_code']);
	
	$results .= '
	<tr valign="top">
		<td class="address">sss'.$counter.'</td>
		<td class="address">'.date("d/m/Y", $record['s_date']).'</td>
		<td class="address"><b>Object Code</b><br />'.$obj1['text'].'<br /><b>Activity Code</b><br />'.$act1['text'].'</td>
		<td class="address">
			<table>
				<tr valign="top">
					<td class="address"><b>Qty:</b></td>
					<td class="address">'.htmlspecialchars($record['qty']).'</td>
				</tr>
				<tr valign="top">
					<td class="address"><b>Desc:</b></td>
					<td class="address">'.htmlspecialchars($record['item_description']).'</td>
				</tr>
				<tr valign="top">
					<td class="address"><b>Unit $:</b></td>
					<td class="address">'.htmlspecialchars($record['unit_price']).'</td>
				</tr>
				<tr valign="top">
					<td class="address"><b>Extended:</b></td>
					<td class="address">'.htmlspecialchars(number_format($extend,2,".","")).'</td>
				</tr>
			</table></td>
	</tr>';
	}

}
*/
$results .= '</table>
<br />
';
 // Totals
$tots = calcTotal($inv_total, $customerdata['gst_rate'], $customerdata['pst_rate'], $shipping = $customerdata['ship_cost']);
$results .= '
<table border="0">
	<tr valign="top">
		<td colspan="2" class="address"><b>Current totals</b></td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Invoice Sub-total</b></td>
		<td class="address" align="right">'.htmlspecialchars(number_format($tots['before_tax'],2,".","")).'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>GST</b></td>
		<td class="address" align="right">'.htmlspecialchars(number_format($tots['gst_amt'],2,".","")).'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>PST</b></td>
		<td class="address" align="right">'.htmlspecialchars(number_format($tots['pst_amt'],2,".","")).'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Shipping</b></td>
		<td class="address" align="right">'.htmlspecialchars(number_format($tots['ship_cost'],2,".","")).'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Ship GST</b></td>
		<td class="address" align="right">'.htmlspecialchars(number_format($tots['ship_gst'],2,".","")).'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Ship PST</b></td>
		<td class="address" align="right">'.htmlspecialchars(number_format($tots['ship_pst'],2,".","")).'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Total</b></td>
		<td class="address" align="right">'.htmlspecialchars(number_format($tots['invoice_total'],2,".","")).'</td>
	</tr>
</table>
<p><br /></p>
';
$results .= '
<h3>Add new lines</h3>
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
	$obj = makeVarselect($INVOICE['global-vars']['obj'], $vname1, $INVOICE['subvars']['obj_code'][$i]);
	$vname2 = "activity_code[$i]";
	$act = makeVarselect($INVOICE['global-vars']['activity'], $vname2, $INVOICE['subvars']['activity_code'][$i]);
	$results .= '
	<tr valign="top">
		<td class="address">'.$counter.'<input type="hidden" name="line['.$i.']" value="y" /></td>
		<td class="address"><input type="text" name="s_date['.$i.']" value="'.$INVOICE['subvars']['s_date'][$i].'" class="text" /></td>
		<td class="address"><b>Object Code</b><br />'.$obj.'<br /><b>Activity Code</b><br />'.$act.'</td>
		<td class="address">
			<table>
				<tr valign="top">
					<td class="address"><b>Qty:</b></td>
					<td><input type="text" class="text" name="qty['.$i.']" value="'.$INVOICE['subvars']['qty'][$i].'" size="10" /></td>
				</tr>
				<tr valign="top">
					<td class="address"><b>Desc:</b></td>
					<td><input type="text" class="text" name="desc['.$i.']" value="'.$INVOICE['subvars']['desc'][$i].'" size="50" /></td>
				</tr>
				<tr valign="top">
					<td class="address"><b>Unit $:</b></td>
					<td><input type="text" class="text" name="unit['.$i.']" value="'.$INVOICE['subvars']['unit'][$i].'" size="10" /></td>
				</tr>
			</table></td>
	</tr>';
}
$results .= '
</table>	
<input type="hidden" name="last_updated" value="'.htmlspecialchars($customerdata['last_updated']).'" />
<p><br /></p>
<input type="submit" value="Calculate invoice" />
</form>
<p><br /></p>
';
$INVOICE['subvars'] = array();
unset($INVOICE['subvars']);
?>