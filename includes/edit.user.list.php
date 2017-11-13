<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$list .= '
<table cellspacing="0" cellpadding="5" border="1" id="user-list">
	<tr valign="top">
		<td><strong>Edit</strong></td>
		<td><strong>Name</strong></td>
		<td><strong>E-mail</strong></td>
		<td><strong>Access Level</strong></td>
		<td><strong>Delete</strong></td>
	</tr>';
		for($i = 0; $i < pg_num_rows($res); $i++){
			$record = pg_fetch_array($res,$i);
			$list .= '
	<tr valign="top">
		<td><a href="'.$_SERVER['PHP_SELF'].'?f=detail&amp;user_no='.urlencode($record['user_no']).'">Detail</a></td>
		<td>'.htmlspecialchars($record['user_name']).'</td>
		<td>'.htmlspecialchars($record['user_email']).'</td>
		<td>'.returnAccesstext($record['access_level']).'</td>
		<td><a href="?f=manage&amp;delete=delete&amp;user_no='.urlencode($record['user_no']).'">Remove</a></td>
	</tr>';
			
		}
		$list .= '
</table>
';
?>