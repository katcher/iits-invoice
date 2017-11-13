<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$text = '
<p>
The following application allows you to create, store and print invoices. Use the menu above 
to select your options.<br />
Please note that a customer must first be entered into the system before an invoice can be created.
</p>
<p class="options">
<br /><strong>Customer</strong><br />
Use this feature to add a new customer record.<br />
<br /><strong>Edit Customer</strong><br />
Use this feature to Edit a  customer\'s Record.<br />
<br /><strong>Invoice </strong><br />
Use this feature to create a new invoice for an existing customer.<br />
<br /><strong>Edit Invoice</strong><br />
Use this feature to edit an invoice already created.<br />	
<br /><strong>Print</strong><br />
Use this feature to print an invoice.<br />
<br /><strong>Send</strong><br />
Send a customer an invoice.<br />';
if($INVOICE['user']['access'] < 2 ) {
	$text .= '
<br /><strong>Payments</strong><br />
Use this feature to enter a customer payment towards an invoice.<br />
<br /><strong>Statements</strong><br />
Use this feature to display a customer statement.<br />';
}
if($INVOICE['user']['access'] < 1 ) {
	$text .= '
<br /><strong>User</strong><br />
Use this feature to create a user for the Invoicing System.<br />
<br /><strong>Edit User</strong><br />';
}
$text .= '
Use this feature to Edit or Delete a user of the Invoicing System.<br />
<br /><strong>Logout</strong><br />
Use this feature to log off the system.<br />
</p>
';
?>