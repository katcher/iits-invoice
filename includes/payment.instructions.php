<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$text = '

<p>
Enter the amount of payment to apply towards the invoice. Select the <strong>Method of Payment</strong>, and if 
you receive a cheque enter the cheque number in the <strong>Cheque Number</strong> field. <strong>REMEMBER</strong> to 
select the <strong>Invoice Status</strong> from the <strong>Invoice Status</strong> Field.  
Once the invoice has been paid in full, set the <strong>Invoice Status</strong> to <strong>Paid</strong>. This 
prevents people from modifying the invoice after it has been paid.
</p>
<p>If you notice an error in the Payment History, you can correct it by selecting <strong>Error Refund</strong> from 
the <strong>Method of Payment</strong> field and enter the dollar amount of the error preceeded by a -. For example:<br />
Error of 125.00<br />
enter -125.00 in the <strong>Amount Paid</strong> field<br />
<strong>Error Refund</strong> in the Method of payment field<br /> 
click on <i>Update</i>. <br />
Once the update is complete you can select Administer Payments from the menu and select the 
invoice number again.  The payment history will show a -125.00 transaction.
</p>
';
?>