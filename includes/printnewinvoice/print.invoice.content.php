<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
if(pg_num_rows($res2)) { // Invoice Details found
	// Initialize Varibales
	$linesprinted = 1; // Pagenation Control
	$inv_total = 0; // Invoice subtotal variable
	$pages = 0;
	$numberOfpages = ceil(pg_num_rows($res2)/$conf['print']['lines']);
	// Print Setup
	$results .= '
<div class="invoice-details">
<table border="0">
	<tr valign="top">
		<th width="10%" align="left">Qty</th>
		<th width="60%" align="left">Description</th>
		<th width="15%" align="right">Unit</th>
		<th width="15%" align="right">Extended</th>
	</tr><!--  Content table setup End  -->';
	// Add line contents
	for($i = 0; $i < pg_num_rows($res2);$i++) {
		$record = pg_fetch_array($res2,$i);
		if(is_numeric($record['qty']) && is_numeric($record['unit_price']) && ($record['qty'] != 0)) {
			$extend = 0; // initialize extend var
			$inv_total = $inv_total + ($record['qty'] * $record['unit_price']);
			$extend = ($record['qty'] * $record['unit_price']);
		}
		else {
			$extend = '';
		}				
		if($linesprinted > $conf['print']['lines']) {
			$pages++; // Increment pages
			$linesprinted = 1; // Rest line counter
			// New page to print 
			$results .= '<!-- ===== Content lines finished ===== -->
	<tr valign="top">
		<td colspan="4" class="divider">&nbsp;</td>
	</tr><!-- ===== Begin totals ===== -->
	<tr valign="top">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td class="detail" align="right">&nbsp;</td>
		<td align="right" class="detail">&nbsp;</td>
	</tr>
	<tr valign="top">
		<td class="detail" colspan="2">&nbsp;</td>
		<td class="detail" align="right">&nbsp;</td>
		<td align="right" class="detail">&nbsp;</td>
	</tr>
	<tr valign="top">
		<td class="detail" colspan="2">&nbsp;</td>
		<td class="detail" align="right">&nbsp;</td>
		<td align="right" class="detail">&nbsp;</td>
	</tr>
	<tr valign="top">
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr valign="top">
		<td>&nbsp;</td>
		<td class="detail">&nbsp;</td>
		<td class="detail" align="right">&nbsp;</td>
		<td align="right" class="detail">&nbsp;</td>
	</tr><!-- ===== End Totals ===== -->
</table>
</div>';
		if($assoc['revision_num'] > 0) {
				$results .= '
<p class="rev">Rev '.$assoc['revision_num'].' - ' . date("F d, Y", $assoc['updated_on']).'</p>';
		}
		include("includes/printnewinvoice/print.invoice.paddress.php");
		
		/*
		$results .= '
<p style="page-break-after: always"></p>
';
*/
				include("includes/printnewinvoice/print.invoice.header.php");
				include("includes/printnewinvoice/print.invoice.customer.data.php");
				$results .= '
<div class="invoice-details">
<table border="0">
	<tr valign="top">
		<th width="10%" align="left">Qty</th>
		<th width="60%" align="left">Description</th>
		<th width="15%" align="right">Unit</th>
		<th width="15%" align="right">Extended</th>
	</tr><!--  Content table setup End  -->';
				// Print existing line
					$results .= '
	<tr valign="top"><!--  Content line '.$i.' -->
		<td width="10%" class="detail">' . htmlspecialchars(number_format($record['qty'],2)) . '</td>
		<td width="60%" class="detail">' . htmlspecialchars($record['item_description']) . '</td>
		<td width="15%" class="detail" align="right">' . htmlspecialchars( number_format( $record['unit_price'], 2, '.', '' )) . '</td>
		<td width="15%" class="detail" align="right">' . number_format($extend,2,'.','') . '</td>
	</tr>';
					$linesprinted++;
				}
				else {
					$results .= '
	<tr valign="top"><!-- Content line '.$i.' -->
		<td width="10%" class="detail">' .htmlspecialchars(number_format($record['qty'],2)).'</td>
		<td width="60%" class="detail">' . htmlspecialchars($record['item_description']).'</td>
		<td width="15%" class="detail" align="right">'.htmlspecialchars( number_format( $record['unit_price'], 2, '.', '' )).'</td>
		<td width="15%" class="detail" align="right">'.number_format($extend,2,'.','').'</td>
	</tr>';
					$linesprinted++;
				}
			}
			//echo $linesprinted;
			// Print Fillers if necessary
			if($linesprinted < $conf['print']['lines']) {
				for($i = 0; $i <= ($conf['print']['lines'] - $linesprinted); $i++) {
					$results .= '
	<tr valign="top">
		<td colspan="4">&nbsp;</td>
	</tr>';
				}
			}
			 // Increment pages variable
			$pages++;
			 // Print bottom horizontal rule
			$results .= '
	<!-- Content lines finished Just before totals printed on last page -->				
	<tr valign="top">
		<td colspan="4" class="divider">&nbsp;</td>
	</tr>';
			 // Begin totals and taxes
			$tots = calcTotal($inv_total, $assoc['gst_rate'], $assoc['pst_rate'], $assoc['ship_cost'], null, null, $assoc['inv_date']);
			$results .= '<!-- ===== Begin totals ===== -->
	<tr valign="top">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td class="detail" align="right"><b>Subtotal</b></td>
		<td align="right" class="detail">'.number_format($tots['before_tax'], 2,'.','').'</td>
	</tr>
	<tr valign="top">
		<td class="detail" colspan="2"><b>GST #</b> 106966591</td>
		<td class="detail" align="right"><b>GST @ </b>'.number_format((100*$assoc['gst_rate']),3,'.','').'%</td>
		<td align="right" class="detail">'.number_format($tots['gst_amt'], 2,'.','').'</td>
	</tr>
	<tr valign="top">
		<td class="detail" colspan="2"><b>PST #</b> 1006010110TQ0012</td>
		<td class="detail" align="right"><b>PST @ </b>'.number_format((100*$assoc['pst_rate']),3,'.','').'%</td>
		<td align="right" class="detail">'.number_format($tots['pst_amt'], 2,'.','').'</td>
	</tr>';
			if($assoc['ship_cost'] > 0)	{
				$results .= '
	<tr valign="top">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td class="detail" align="right"><b>Shipping</b></td>
		<td align="right" class="detail">'.number_format($tots['ship_cost'], 2,'.','').'</td>
	</tr>
	<tr valign="top">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td class="detail" align="right"><b>GST Shipping @ </b>'.number_format((100*$assoc['gst_rate']),3,'.','').'%</td>
		<td align="right" class="detail">'.number_format($tots['ship_gst'], 2,'.','').'</td>
	</tr>
	<tr valign="top">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td class="detail" align="right"><b>PST Shipping @ </b>'.number_format((100*$assoc['pst_rate']),3,'.','').'%</td>
		<td align="right" class="detail">'.number_format($tots['ship_pst'], 2,'.','').'</td>
	</tr>';
			}
		     // Print total
			$results .= '
	<tr valign="top">
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr valign="top">
		<td>&nbsp;</td>
		<td class="detail"><b>Canadian Funds</b></td>
		<td class="detail" align="right"><b>Total Due</b></td>
		<td align="right" class="detail">$ '.number_format($tots['invoice_total'], 2,'.','').'</td>
	</tr><!-- ===== End Totals ===== -->
</table>
</div>';
			 // Verify Last revision
			if($assoc['revision_num'] > 0) {
				$results .= '
<p class="rev">Rev '.$assoc['revision_num'].' - ' . date("F d, Y", $assoc['updated_on']).'</p>';
			}
			// Print payment to
			include("includes/printnewinvoice/print.invoice.paddress.php");
			include("script/jprint.php");
}
else {
	$results .= '
	<div class="noLines">No detail lines on invoice</div>';
	include("includes/printnewinvoice/print.invoice.paddress.php");
}
?>