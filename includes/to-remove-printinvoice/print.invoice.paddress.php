<?php
$results .= '
				<tr valign="top">
					<td colspan="4" class="address">
						Send payment care of :<br />
						<strong>Concordia University</strong><br />
						1400 de  Maisonneuve W.<br />
						Suite LB-800<br />
						Montreal, Quebec H3G 1M8.<br />
						Please Indicate the Invoice Number on Cheque.<br />
						For questions regarding this invoice contact '.htmlspecialchars($assoc['creator']).' at 514-848-2424 ext. '.htmlspecialchars($assoc['creator_ext']).'<br /><br />
						<center><b>Thank you for your business</b>.</center></td>
					</tr>
					<tr valign="top">
						<td colspan="4" align="right" class="address"><b>Page:</b> '.$pages.'</td>
					</tr>';
?>