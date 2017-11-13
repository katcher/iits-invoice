<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$list .= '
<table cellspacing="0" cellpadding="5" border="1" id="company-list" title="Company list">
	<tr valign="top">
		<td><strong>Edit</strong></td>
		<td><strong>Name</strong></td>
	</tr>';
		for($i = 0; $i < pg_num_rows($res); $i++){
			$record = pg_fetch_array($res,$i);
			$list .= '
	<tr valign="top">
		<td><a href="'.$_SERVER['PHP_SELF'].'?f=detail&amp;cust_no='.urlencode($record['cust_no']).'">Select</a></td>
		<td>'.htmlspecialchars($record['b_comp_name'], ENT_QUOTES, 'ISO-8859-1').'</td>
	</tr>';
			
		}
		$list .= '
</table>
';
?>