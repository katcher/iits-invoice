<?php
defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' );
$results .= '
<div class="footer"><p>
Please make cheques payable to <strong>Concordia University</strong> and indicate the <strong>invoice #</strong>.<br /><br />
Please send payment to:<br />
<strong>Concordia University, IITS Department</strong><br />
1455 de  Maisonneuve W, <strong>Suite LB-800</strong><br />
Montreal, Quebec H3G 1M8.<br />
For questions regarding this invoice contact '.htmlspecialchars($assoc['creator']).' at 514-848-2424 ext. '.htmlspecialchars($assoc['creator_ext']).'
</p>
<div class="thank-you">Thank you for your business</div>
<div class="page-no">Page: '.$pages.'/'.$numberOfpages.'</div>
<div class="clear-all">&nbsp;</div>
</div>
</div>
<!-- +++++ End Page Print +++++ -->
';
?>