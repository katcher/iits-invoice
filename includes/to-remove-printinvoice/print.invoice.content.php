<?php
if(pg_num_rows($res2)) { // Invoice Details found 
	$results .= '	<tr valign="top"><!--  Content table setup  -->
		<td colspan="2">		
			<table border="0" width="100%">
				<tr valign="top">					
					<td width="10%" class="thead">Qty</td>
					<td width="60%" class="thead">Description</td>
					<td width="15%" class="thead" align="right">Unit</td>
					<td width="15%" class="thead" align="right">Extended</td>
				</tr><!--  Content table setup End  -->';					
			 // Variables Used
	$linesprinted = 1; // Pagenation Control
	$inv_total = 0; // Invoice subtotal variable
	$pages = 0;			
	// Add line contents
	for($i = 0; $i < pg_num_rows($res2);$i++) {
		$record = pg_fetch_array($res2,$i);				
		if(is_numeric($record['qty']) && is_numeric($record['unit_price']) && ($record['qty'] != 0)) {
			$extend = 0;
			$inv_total = $inv_total + ($record['qty'] * $record['unit_price']);
			$extend = ($record['qty'] * $record['unit_price']);
		}
		else {
			$extend = '';
		}				
		if($linesprinted > $conf['print']['lines']) {
			// Increment pages
			$pages++; 
			// Rest line counter
			$linesprinted = 1;
			// New page to print 
			$results .= '<!-- ===== Content lines finished ===== -->
				<tr valign="top">
					<td colspan="4"><hr size="5" noshade="noshade" /></td>
				</tr>
				<!-- ===== Begin totals ===== -->
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
				</tr>
				<!-- ===== End Totals ===== -->
				<tr valign="top">
					<td colspan="4"><hr size="5" noshade="noshade" /><br /></td>
				</tr>';
				include("includes/to-remove-printinvoice/print.invoice.paddress.php");
				$results .= '
			</table>		
		</td>
	</tr>
</table>
<p style="page-break-after: always"></p>
<!-- Begin New pagenated content -->
';
				include("includes/to-remove-printinvoice/print.invoice.header.php");
				include("includes/to-remove-printinvoice/print.invoice.customer.data.php");
				$results .= '	
	<!-- Print invoice content -->
	<tr valign="top"><!-- ===== Invoice Detail Header begin ===== -->
		<td colspan="2">	
			<table border="0" width="100%">
				<tr valign="top">
					<td width="10%" class="thead">Qty</td>
					<td width="60%" class="thead">Description</td>
					<td width="15%" class="thead" align="right">Unit</td>
					<td width="15%" class="thead" align="right">Extended</td>
				</tr>
				<!-- ===== Invoice Detail Header End ===== -->';
// end add					
// Print existing line					
					$results .= '
				<tr valign="top"><!--  Content line '.$i.' -->
					<td width="10%" class="detail">' . htmlspecialchars(number_format($record['qty'],2)) . '</td>
					<td width="60%" class="detail">' . htmlspecialchars($record['item_description']) . '</td>
					<td width="15%" class="detail" align="right">' . htmlspecialchars( number_format( $record['unit_price'], 2, '.', '' )) . '</td>
					<td width="15%" class="detail" align="right">' . number_format($extend,2,'.','') . '</td>
				</tr>
				
';				
					$linesprinted++;
				}
				else {
					$results .= '
				<tr valign="top"><!--  Content line '.$i.' -->
					<td width="10%" class="detail">' .htmlspecialchars(number_format($record['qty'],2)).'</td>
					<td width="60%" class="detail">'.htmlspecialchars($record['item_description']).'</td>
					<td width="15%" class="detail" align="right">'.htmlspecialchars( number_format( $record['unit_price'], 2, '.', '' )).'</td>
					<td width="15%" class="detail" align="right">'.number_format($extend,2,'.','').'</td>
				</tr>
';			
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
				</tr>
';
				}
			}
			 // Increment pages variable
			$pages++;
			
			
			 // Print bottom horizontal rule
			$results .= '
				<!-- Content lines finished -->				
				<tr valign="top">
					<td colspan="4"><hr size="5" noshade="noshade" /></td>
				</tr>
';
			 // Begin totals and taxes
			$tots = calcTotal($inv_total, $assoc['gst_rate'], $assoc['pst_rate'], $assoc['ship_cost']);
			 
			 
			$results .= '
				<!-- ===== Begin totals ===== -->				
				<tr valign="top">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td class="detail" align="right"><b>Subtotal</b></td>
					<td align="right" class="detail">'.number_format($tots['before_tax'], 2,'.','').'</td>
				</tr>
				<tr valign="top">
					<td class="detail" colspan="2"><b>GST #</b> 106966591</td>
					<td class="detail" align="right"><b>GST @ </b>'.number_format((100*$assoc['gst_rate']),2,'.','').'%</td>
					<td align="right" class="detail">'.number_format($tots['gst_amt'], 2,'.','').'</td>
				</tr>
				<tr valign="top">
					<td class="detail" colspan="2"><b>PST #</b> 1006010110TQ0012</td>
					<td class="detail" align="right"><b>PST @ </b>'.number_format((100*$assoc['pst_rate']),2,'.','').'%</td>
					<td align="right" class="detail">'.number_format($tots['pst_amt'], 2,'.','').'</td>
				</tr>
			';
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
					<td class="detail" align="right"><b>GST Shipping</b></td>
					<td align="right" class="detail">'.number_format($tots['ship_gst'], 2,'.','').'</td>
				</tr>
				<tr valign="top">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td class="detail" align="right"><b>PST Shipping</b></td>
					<td align="right" class="detail">'.number_format($tots['ship_pst'], 2,'.','').'</td>
				</tr>
';
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
				</tr>
				<!-- ===== End Totals ===== -->
				<tr valign="top">
					<td colspan="4"><hr size="5" noshade="noshade" /></td>
				</tr>	
';			
			 // Verify Last revision
			if($assoc['revision_num'] > 0) {
				$results .= '
				<tr valign="top">
					<td align="right" colspan="4" class="address">Rev '.$assoc['revision_num'].' - ' . date("F d, Y", $assoc['updated_on']).'</td>
				</tr>
';
			}			
			// Print payment to
			include("includes/to-remove-printinvoice/print.invoice.paddress.php");
			$results .= '
			</table>		
		</td>
	</tr>
';
}
else {
	$results .= '
	<tr valign="top">
		<td colspan="2">No items on invoice</td>
	</tr>
';
	}
$results .= '</table>
';
?>