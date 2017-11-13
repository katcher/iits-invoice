<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
if(isset($INVOICE['user']['access']) and strlen($INVOICE['user']['access'])) {
	echo '
<div class="noprint" id="nav">
<ul id="menu">';
if(strlen($INVOICE['user']['access'])) {
	echo '
	<li id="add-customer"><a href="customer.php">Add Cust</a></li>
	<li id="edit-customer"><a href="customer.php?f=edit">Edit Cust</a></li>	
	<li id="add-invoice"><a href="invoice.php">Invoice</a></li>	
	<li id="edit-invoice"><a href="edit_invoice.php">Edit Invoice</a></li>
	<li id="tax"><a href="tax.php">Taxes</a></li>
	<li id="print"><a href="print.php">Print</a></li>';
}
if($INVOICE['user']['access'] < 2 ) {
	echo '
	<li id="send"><a href="send.php">Send</a></li>
	<li id="payment"><a href="payment.php">Payment</a></li>
	<li id="statement"><a href="statement.php">Statement</a></li>';	
}
if($INVOICE['user']['access'] == 0) {
	echo '
	<li id="add-user"><a href="user.php">User</a></li>
	<li id="edit-user"><a href="user.php?f=edit">Edit user</a></li>';
}

echo '
	<li><a href="login.php?f=logout">Logout</a></li>
	<li id="loginame">'.$INVOICE['user']['name'].'</li>
</ul>
<div class="clear"></div>
</div>
';
}
?>
