<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$org = codeReturn($INVOICE['global-vars']['org'], $org_code);
$results .= '
<h2>'.htmlspecialchars($display_cust_name).'</h2>

<h2>Invoice Detail</h2>
<table border="1" cellspacing="0" cellpadding="4">
	<tr valign="top">
		<td class="address"><b>Date</b></td>
		<td class="address"><b>Coding</b></td>
		<td class="address"><b>Qty</b></td>
		<td class="address"><b>Description</b></td>
		<td class="address"><b>Unit $</b></td>
		<td class="address"><b>Extended</b></td>
	</tr>';
	$results .= $existdata; // Add old lines to items
	
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
		if($tmpresults[$j]['delete']){
			$results .= ' class="deleted"';
		}
		$results .= '>
		<td class="address">'.date("d/m/Y", $tmpresults[$j]['date']).'&nbsp;</td>
		<td class="address">'.htmlspecialchars($objtext).' - ' . htmlspecialchars($actitext).'&nbsp;</td>
		<td class="address" align="right">'.number_format($tmpresults[$j]['qty'],2, '.', '').'&nbsp;</td>
		<td class="address">'.$tmpresults[$j]['desc'].'&nbsp;</td>
		<td class="address" align="right">'.number_format($tmpresults[$j]['unit'],2, '.', '').'&nbsp;</td>
		<td class="address" align="right">'.htmlentities($tmpresults[$j]['extended']).'&nbsp;</td>
	</tr>';
	}
	
	 // Begin total print
	$results .= '
		<tr valign=top>
			<td colspan="3" class="address">&nbsp;</td>
			<td class="address"><b>SubTotal</b></td>
			<td class="address">&nbsp;</td>
			<td class="address" align="right">$'.number_format($overtotal['before_tax'], 2, '.', '').'</td>
		</tr>
		<tr valign=top>
			<td colspan="3" class="address">&nbsp;</td>
			<td class="address"><b>GST</b></td>
			<td class="address"><b>'. number_format((100 * $gst ),3,'.','') .' %</b></td>
			<td class="address" align="right">$'.number_format($overtotal['gst_amt'], 2, '.', '').'</td>
		</tr>
		<tr valign=top>
			<td colspan="3" class="address">&nbsp;</td>
			<td class="address"><b>PST</b></td>
			<td class="address"><b>'. number_format((100 * $pst ),3,'.','') .' %</b></td>
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
			<td class="address"><b>'. number_format((100 * $gst ),3,'.','') .' %</b></td>
			<td class="address" align="right">$'.number_format($overtotal['ship_gst'], 2, '.', '').'</td>
		</tr>
		<tr valign=top>
			<td colspan="3" class="address">&nbsp;</td>
			<td class="address"><b>Shipping PST</b></td>
			<td class="address"><b>'. number_format((100 * $pst ),3,'.','') .' %</b></td>
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