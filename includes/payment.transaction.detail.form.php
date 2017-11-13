<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$form = '
<h2>Invoice '.htmlspecialchars(pg_result($res,0,'inv_no')).'</h2>
<p><strong>'.htmlspecialchars(pg_result($res,0,'b_comp_name')).'</strong></p>
<form method="post" action="'.$_SERVER['PHP_SELF'].'?f=updatetransaction">
<input type="hidden" name="trans_no" value="'.htmlspecialchars(pg_result($res,0,'trans_no')).'" />
<p>
<strong>Method of payment</strong><br />
'. makeStatus( $conf['mop'],'mop',pg_result($res,0,'mop') ).'<br /><br />
<strong>Payment type</strong><br />
'. makeStatus( $conf['payment_type'],'payment_type',pg_result($res,0,'payment_type') ).'<br /><br />
<strong>Cheque number</strong><br />
<input type="text" name="cheque_no" value="'.htmlspecialchars(pg_result($res,0,'cheque_no')).'" size="10" maxlength="50" /><br /><br />
<strong>Amount of payment</strong><br />
'.htmlspecialchars(number_format(pg_result($res,0,'amount'), 2,'.','')).'<br /><br />
<input type="submit" value="Modify payment" />
</p>
</form>
<p><a href="?f=detail&inv_no='.urlencode(pg_result($res,0,'inv_no')).'">Back to payment</a></p>
';
?>