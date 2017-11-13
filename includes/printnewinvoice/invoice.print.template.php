<?php defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/2000/REC-xhtml1-20000126/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
  <title>Invoice<?php echo $assoc['inv_no']; ?></title>
<link rel="stylesheet" href="css/printscreen.css" type="text/css" charset="iso-8859-1" media="screen" />
<link rel="stylesheet" href="css/invoiceprint.css" type="text/css" charset="iso-8859-1" media="print" />
</head>
<body>
<div class="invoice-page"<?php echo $stat; ?>><?php echo $results; ?>
</div>
</body>
</html>