<?php
if($customer) {
// Print ship to address also
		$results .= '
	<tr><!-- Customer Address -->
		<td colspan="2">
			<table border="0" width="100%">
				<tr valign="top">
					<td width="50%" class="head">Bill To: '.htmlspecialchars($customer['b_contact']).'</td>
					<td width="50%" class="head">Ship To: ';
					if($customer['s_contact']) {
						$results .= htmlspecialchars($customer['s_contact']);
					}
					else {
						$results .= "Same";
					}
					$results .= '</td>
				</tr>
				<tr valign="top">
					<td class="address"><span class="compname">'.htmlspecialchars($customer['b_comp_name']).'</span></td>
					<td class="address"><span class="compname">'.htmlspecialchars($customer['s_comp_name']).'</span></td>
				</tr>
				<tr valign="top">
					<td class="address">'.htmlspecialchars($customer['b_address']);
		if($customer['b_suite']) {
			$results .= ' # ' . htmlspecialchars($customer['b_suite']);
		}
		$results .= '</td>
					<td class="address">'.htmlspecialchars($customer['s_address']);
		if($customer['s_suite']) {
			$results .= ' # ' . htmlspecialchars($customer['s_suite']);
		}
		$results .= '</td>
				</tr>
				<tr valign="top">
					<td class="address">'.htmlspecialchars($customer['b_city']).', ' . htmlspecialchars($customer['b_province']) . '</td>
					<td class="address">'.htmlspecialchars($customer['s_city']).', ' . htmlspecialchars($customer['s_province']) . '</td>
				</tr>
				<tr valign="top">
					<td class="address">'.htmlspecialchars($customer['b_country']).' '. htmlspecialchars($customer['b_postal']) .'</td>
					<td class="address">'.htmlspecialchars($customer['s_country']).'</td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- PO Number if applicable -->
	<tr valign="top">
		<td class="po" colspan="2">Purchase Order Number: '.$assoc['purchase_ord'].'</td>
	</tr>
	<tr valign="top">
		<td colspan="2"><hr size="5" noshade="noshade" /></td>
	</tr>
';
}
else {
	$results .= '
	<tr>
		<td colspan="2">Invalid Customer Number</td>
	</tr>
';			
}
?>