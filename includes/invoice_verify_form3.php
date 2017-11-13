<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$org = codeReturn($INVOICE['global-vars']['org'], $org_code);
$results .= '
<h3>'.htmlspecialchars($display_cust_name).'</h3>
<table border="0" cellspacing="0" cellpadding="3">
<tr valign="top">
		<td class="address"><b>Client Type</b></td>
		<td class="address">'.htmlspecialchars($c_type).'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Creator Extension</b></td>
		<td class="address">'.htmlspecialchars($creator_ext).'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Organization Code</b></td>
		<td class="address">'.htmlspecialchars($org['text']).'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>PO</b></td>
		<td class="address">'.htmlspecialchars($purchase_ord).'</td>
	</tr>
	<tr valign="top">
		<td class="address"><b>Shipping</b></td>
		<td class="address">';
		if($ship_cost) {
			$results .= htmlspecialchars($ship_cost);
		}
		else {
			$results .= "N/A";
		}
		$results .= '</td>
	</tr>
</table>
<h3>Invoice Detail</h3>

<table border="1" cellspacing="0" cellpadding="4">
	<tr valign="top">
		<td class="address"><b>Date</b></td>
		<td class="address"><b>Coding</b></td>
		<td class="address"><b>Qty</b></td>
		<td class="address"><b>Description</b></td>
		<td class="address"><b>Unit $</b></td>
		<td class="address"><b>Extended</b></td>
	</tr>';
	for($j = 0; $j < count($tmpresults); $j++) {
		$ob = '';
		$acti = '';
		$objtext = null;
		$actitext = null;
		if(strlen($tmpresults[$j]['obj_code'])) {
			$ob = codeReturn($INVOICE['global-vars']['obj'], $tmpresults[$j]['obj_code']);
			$objtext = $ob['text'];
		}
		if(strlen($tmpresults[$j]['activity_code'])) {
			$acti = codeReturn($INVOICE['global-vars']['activity'], $tmpresults[$j]['activity_code']);
			$actitext = $acti['text'];
		}
		
		$results .= '
	<tr valign="top"';
		if(isset($tmpresults[$j]['delete'])){
			$results .= ' class="deleted"';
		}
		$results .= '>
		<td class="address">'.date("d/m/Y", $tmpresults[$j]['date']).'&nbsp;</td>
		<td class="address">'.htmlspecialchars($objtext).' - ' . htmlspecialchars($actitext).'&nbsp;</td>
		<td class="address" align="right">'.number_format($tmpresults[$j]['qty'],2, '.', '').'&nbsp;</td>
		<td class="address">'.$tmpresults[$j]['desc'].'&nbsp;</td>
		<td class="address" align="right">'.number_format($tmpresults[$j]['unit'],2, '.', '').'&nbsp;</td>
		<td class="address" align="right">'.number_format($tmpresults[$j]['extended'],2, '.', '').'&nbsp;</td>
	</tr>';
	}
	
	 // Begin total print
	$results .= '<!-- ========  Begin Totals ========== -->
		<tr valign=top>
			<td colspan="3" class="address">&nbsp;</td>
			<td class="address"><b>SubTotal</b></td>
			<td class="address">&nbsp;</td>
			<td class="address" align="right">$'.number_format($overtotal['before_tax'], 2, '.', '').'</td>
		</tr>
		<tr valign=top>
			<td colspan="3" class="address">&nbsp;</td>
			<td class="address"><b>GST</b></td>
			<td class="address"><b>'. number_format((100 * $conf['rate']['gst'] ),3,'.','') .' %</b></td>
			<td class="address" align="right">$'.number_format($overtotal['gst_amt'], 2, '.', '').'</td>
		</tr>
		<tr valign=top>
			<td colspan="3" class="address">&nbsp;</td>
			<td class="address"><b>PST</b></td>
			<td class="address"><b>'. number_format((100 * $conf['rate']['pst'] ),3,'.','') .' %</b></td>
			<td class="address" align="right">$'.number_format($overtotal['pst_amt'], 2, '.', '').'</td>
		</tr>
		<tr valign=top>
			<td colspan="3" class="address">&nbsp;</td>
			<td class="address"><b>Shipping</b></td>
			<td class="address">&nbsp;</td>
			<td class="address" align="right">$'.number_format($overtotal['ship_cost'], 2, '.', '').'</td>
		</tr>
		<tr valign=top>
			<td colspan="3" class="address">&nbsp;</td>
			<td class="address"><b>Shipping GST</b></td>
			<td class="address"><b>'. number_format((100 * $conf['rate']['gst'] ),3,'.','') .' %</b></td>
			<td class="address" align="right">$'.number_format($overtotal['ship_gst'], 2, '.', '').'</td>
		</tr>
		<tr valign=top>
			<td colspan="3" class="address">&nbsp;</td>
			<td class="address"><b>Shipping PST</b></td>
			<td class="address"><b>'. number_format((100 * $conf['rate']['pst'] ),3,'.','') .' %</b></td>
			<td class="address" align="right">$'.number_format($overtotal['ship_pst'], 2, '.', '').'</td>
		</tr>
		<tr valign=top>
			<td colspan="3" class="address">&nbsp;</td>
			<td class="address"><b>Total Due</b></td>
			<td class="address">&nbsp;</td>
			<td class="address" align="right">$'.number_format($overtotal['invoice_total'], 2, '.', '').'</td>
		</tr>';	
	$results .= '
</table>

';
?>