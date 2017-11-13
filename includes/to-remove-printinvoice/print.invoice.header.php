<?php
if($assoc['status'] == "Paid"){
	$stat = " class=\"paid\"";
}
$results .= '<!-- Invoice Header -->
<table border="0" width="100%"' . $stat . '>
	<tr>
		<td><!-- Print invoice header and Image -->
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td height="106" bgcolor="#FFFFFF" valign="middle"><img src="./images/invoice_1a.gif" width="136" height="93" align="right" alt="iits image" /></td>
					<td valign="middle" height="106">
						<p class="address">
						<span class="concordia">Concordia University</span><br /><br />
          				1455 de Maisonneuve Blvd. W.<br />
          				Montreal, Quebec, H3G 1M8<br />
         				Tel. (514) 848-7600<br />
          				Fax (514) 848-7622</p></td>
				</tr>
  			</table>
  			<!-- End Print invoice header and Image -->
		</td>
		<td valign="top"><p class="invname">Facture # '. htmlspecialchars($assoc['inv_no']).'</p></td>
	</tr>
	<tr>
		<td colspan="2"><p class="invdate">Invoice Date: '.date("F d, Y",$assoc['inv_date']).'</p></td>
	</tr>';
?>