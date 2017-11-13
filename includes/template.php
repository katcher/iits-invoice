<?php defined( '_VALIDINCLUDE_' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/2000/REC-xhtml1-20000126/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
  <title><?php echo $mytitle;?></title>
<style type="text/css" media="all">@import "css/navigation.css";</style>
<link rel="stylesheet" href="css/default.css" type="text/css" charset="iso-8859-1" media="all" />
<script src="script/checks.js" type="text/javascript" language="Javascript" charset="iso-8859-1"></script>
<style type="text/css" media="all">
/* <![CDATA[ */
ul#menu li#<?php echo $app; ?> a {
	color: #fff;
	background-color: #C0C0C0;	
}
/* ]]> */
</style>
</head>
<body>
<?php if($f != "print") { include("includes/navigation.inc"); } ?>
<div id="content">
<!-- Begin Dynamic Content -->
<?php echo $results; ?>
<!-- End Dynamic Content -->
<?php 
if($f != "print") {
    if($conf['debug']) {
	echo "
	<!-- 
<div class=\"noprint\">
	<p>Session Variables</p>
	<pre><b>User Variables</b>\n";
		print_r($_SESSION['INVOICE']);
		echo "
	</pre>
    <pre><strong>Conf:</strong>\n";
        print_r($conf);
        echo "
    </pre>
</div>
-->
";
	}
}
?>
</div>

</body>
</html>