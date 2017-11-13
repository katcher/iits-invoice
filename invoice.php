<?php
session_start();
require("lib/invoicelib.php");
if( !(isset($INVOICE['user']['access']) and is_numeric($INVOICE['user']['access']) && ($INVOICE['user']['access'] <= 2)) ) {	
	$INVOICE['error'] = "This application is restricted";
	$_SESSION['INVOICE'] = $INVOICE;
	header("Location: ".$conf['access']['loginUrl'] );
	exit;
}
$app = "add-invoice";
$f = returnVar('f');
$cust_no = returnVar('cust_no');
$c_type = returnVar('c_type');
$creator_ext = returnVar('creator_ext');
$org_code = returnVar('org_code');
$ship_cost = returnVar('ship_cost');
$purchase_ord = returnVar('purchase_ord');
$pst_exempt = returnVar('pst_exempt');
$gst_exempt = returnVar('gst_exempt');
$obj_code = returnVar('obj_code');
$activity_code = returnVar('activity_code');
$qty = returnVar('qty');
$unit = returnVar('unit');
$s_date = returnVar('s_date');
$desc = returnVar('desc');
$calculate = returnVar('calculate');      
$line = returnVar('line');
$display_cust_name = returnVar('display_cust_name');
$client_type = returnVar('client_type');
$inv_no = returnVar('inv_no');

$results = '';
$res = '';
$sql = '';
$myVars = array_merge($_POST,$_GET);

if($f == "step2"){	
	if(!is_numeric($cust_no)){
		$mytitle = "IITS Invoice application - [Create Invoice - Error]";	
		$results .= '
		<h2>'.$mytitle.'</h2>
<p class="error">No customer selected</p>
';
	include("includes/invoice_form1.php");
	$results .= '
';
	}
	else {
		$db = db_connect();
		$mytitle = "IITS Invoice application - [Create Invoice - Detail]";	
		$results .= '
		<h2>'.$mytitle.'</h2>
';
		include("includes/invoice_recreate_form2.php");		
	}	
}
elseif($f == "calculate") {
	 // Begin Required field eval
	$error = (string) null;
	 // Variables to insert into association table
	$assoc_vars = '';
	$detail_vars = '';
	
	if(!is_numeric($cust_no)){
		$INVOICE['error'] .= "Customer Invalid or not selected<br />";
		$_SESSION['INVOICE'] = $INVOICE;
		header("Location: https://".$_SERVER['HTTP_HOST'] .$_SERVER['PHP_SELF']);
		exit;
	}
	else {		
		 // +++++  Begin Minimum General validation  +++++
		if(!strlen(trim(chop($c_type)))){
			$error .= "Client type not selected<br />\n";
		}
		else {
			$assoc_vars .= '
<input type="hidden" name="cust_no" value="'.htmlspecialchars(trim(chop($cust_no))).'" />
<input type="hidden" name="client_type" value="'.htmlspecialchars(trim(chop($c_type))).'" />';
		}
		if( strlen(trim(chop($creator_ext))) != 4 && !is_numeric($creator_ext)){
			$error .= "Extension must be 4 numbers<br />\n";
		}
		else {
			$assoc_vars .= '
<input type="hidden" name="creator_ext" value="'.htmlspecialchars(trim(chop($creator_ext))).'" />';
		}
		if(!strlen(trim(chop($org_code)))){
			$error .= "Invalid ORG code selected<br />\n";
		}
		else {
			$assoc_vars .= '
<input type="hidden" name="org_code" value="'.htmlspecialchars(trim(chop($org_code))).'" />';
		}
				
		if(strlen($ship_cost)){
			if(!is_numeric($ship_cost)){
				$error .= "Shipping cost must be a monetary value (Numeric).<br />\n";
			}
			else {
				$assoc_vars .= '
<input type="hidden" name="ship_cost" value="'.htmlspecialchars(trim(chop($ship_cost))).'" />';
			}
		}
		if(strlen($purchase_ord)){
				$assoc_vars .= '
<input type="hidden" name="purchase_ord" value="'. htmlspecialchars(trim(chop($purchase_ord))) . '" />';
		}
		if(strlen($pst_exempt)){
				$assoc_vars .= '
<input type="hidden" name="pst_exempt" value="true" />';
		}
		if(strlen($gst_exempt)){
				$assoc_vars .= '
<input type="hidden" name="gst_exempt" value="true" />';
		}
		 // Valid first line Obj, Activty, qty unit
		if(!( strlen($obj_code[0]) && strlen($activity_code[0]) && is_numeric($qty[0]) && is_numeric($unit[0]))) {
			$error .= "<strong>Note</strong>: Comments are not permitted on the first line of an invoice<br /><br />\n";
			if(!strlen($obj_code[0])){
				$error .= "Object code ommitted<br />\n";
			}
			if(!strlen($activity_code[0])){
				$error .= "Activity code ommitted<br />\n";
			}
			if(!is_numeric($qty[0])){
				$error .= "Quantity ordered must be numeric<br />\n";
			}
			if(!is_numeric($unit[0])){
				$error .= "Unit price must be numeric<br />\n";
			}
		}
		if(!verifyDate($s_date[0])){
			$error .= "Invalid date specified on Line 1 of invoice detail<br />\n";
		}
		 // First error check
		if($error) {
			$mytitle = "IITS Invoice application - [Create Invoice - Error evaluation step 1]";
			$results .= '
<h2>'.$mytitle.'</h2>
';
			include("includes/invoice_recreate_form2.php");					
		}
		// +++++  End Minimum General validation  +++++
		else {			
			/*
				Calculate Invoice
				Line by line verification
				Initalize totals
				Display Array
			*/
			 
			$tmpresults = array();
			$valid = 0;
			$total = 0;
			$counter = 0;
			
			for($i = 0; $i < 12; $i++) {							
				$counter++; // Display Line counter
				$extended = 0;				
				if( strlen($s_date[$i]) || strlen($obj_code[$i]) || strlen($activity_code[$i]) || strlen($unit[$i]) || strlen($desc[$i]) || strlen($qty[$i])) {
					
					 // Verify if date is it valid
					if($x = verifyDate($s_date[$i])) {
						$servicedate = $x;
					}
					else {
						$error .= "Invalid date specified on invoice line $counter<br />\n";
					}
					if(verifyDescriptionlength($desc[$i])) {
						$error .= "Item description greater than 50 characters on line $counter<br />";
					}
					if(strlen($qty[$i])) {
						if(!is_numeric($qty[$i])){
							$error .= "Quantity entered on line $counter must be numeric<br />\n";
						}								
					}
					if(strlen($unit[$i])) {
						if(!is_numeric($unit[$i])){
							$error .= "Unit Price entered on line $counter must be numeric<br />\n";
						}								
					}
					if(strlen($unit[$i]) && !strlen($qty[$i])) {
						$error .= "Unit Price entered on line $counter, No quantity entered<br />\n";
					}
					
					if( (strlen($obj_code[$i]) || strlen($activity_code[$i])) &&  (!strlen($unit[$i]) && !strlen($qty[$i]) )) {
						$error .= "Unit price or Quantity ommitted on line $counter<br />\n";
					}					
								
					if( ($qty[$i] != 0) && ($unit[$i] != 0.00 ) ) {					
						if( !strlen(trim(chop($obj_code[$i]))) ) {
							$error .= "Object code not entered on line $counter<br />\n";
						}
						if( !strlen(trim(chop($activity_code[$i]))) ) {
							$error .= "Activity code not entered on line $counter<br />\n";
						}					
						$extended = $qty[$i] * $unit[$i];
						$total = $total + $extended;
					}
					
					/*
					if( strlen($qty[$i]) && ($qty[$i] <  0 ) && ($unit[$i] != 0.00 ) ) {
						$extended = $qty[$i] * $unit[$i];
						$total = $total + $extended;
					}
					*/
					
					
					if(!$error) {
						$detail_vars .= '
<!-- Invoice line '.$i.' -->
<input type="hidden" name="s_date['.$valid.']" value="'.$servicedate.'" />
<input type="hidden" name="obj_code['.$valid.']" value="'.htmlspecialchars($obj_code[$i]).'" />
<input type="hidden" name="activity_code['.$valid.']" value="'.htmlspecialchars($activity_code[$i]).'" />
<input type="hidden" name="qty['.$valid.']" value="'.htmlspecialchars($qty[$i]).'" />
<input type="hidden" name="desc['.$valid.']" value="'.htmlspecialchars($desc[$i]).'" />
<input type="hidden" name="unit['.$valid.']" value="'.htmlspecialchars($unit[$i]).'" />';					
						$valid++;
					}
					$tmpresults[] = (array("date" => $servicedate, "obj_code" => $obj_code[$i], "activity_code" => $activity_code[$i], "qty" => $qty[$i], "desc" => $desc[$i], "unit" => $unit[$i], "extended" => $extended));					
				}				
			}			
			if($error) {
				$mytitle = "IITS Invoice application - [Create Invoice - Error evaluation step 2]";
				$results .= '
<h2>'.$mytitle.'</h2>
';
				include("includes/invoice_recreate_form2.php");
			}
			else {
				 // Add formatting / totals for confirmation screen of new invoice here				 
				$overtotal = calcTotal($total, $conf['rate']['gst'], $conf['rate']['pst'], $ship_cost, $pst_exempt, $gst_exempt, mktime() );							
				$mytitle = "IITS Invoice application - [Create Invoice - Invoice confirmation, Total verification]";			
				$results .= "
<h2>$mytitle</h2>\n";
				include("includes/invoice_verify_form3.php");				
				$results .= '
<form method="post" action="'.htmlentities($_SERVER['PHP_SELF']).'?f=insert">
'. $assoc_vars.'
'.$detail_vars.'
<br />
<input type="submit" value="Create Invoice" />
</form>
';				
			}		
		}			
	}	
}
elseif($f == "insert") {
	if(!is_numeric($cust_no)){
		$INVOICE['error'] .= "Customer Invalid or not selected<br />";
		$_SESSION['INVOICE'] = $INVOICE;
		header("Location: https://".$_SERVER['HTTP_HOST'] .$_SERVER['PHP_SELF']);		
		exit;
	}
	else {		
		$tmpsql = array();
		 // Verify required fields in Association Table
		if(!strlen(trim(chop($org_code)))){
			$INVOICE['error'] .= "Org Code ommitted<br />";
		}
		if(!strlen(trim(chop($client_type)))){
			$INVOICE['error'] .= "Client Type ommitted<br />";
		}
		if(!strlen(trim(chop($creator_ext)))){
			$INVOICE['error'] .= "Creator Telephone extension ommitted<br />";
		}
		
		// Tax exempt status
		if($pst_exempt) {
			$prate = '0.00';
		}
		else {
			$prate = $conf['rate']['pst'];
		}
		if($gst_exempt) {
			$grate = '0.00';
		}
		else {
			$grate = $conf['rate']['gst'];
		}		
		if(!(isset($INVOICE['error']))) {
			$assoc_tmp_sql = '';
			$assoc_tmp_sql .= "INSERT INTO invoice_assoc ( inv_no, cust_no, inv_date, org_code, client_type, status, purchase_ord, gst_rate, pst_rate, ship_cost, creator, creator_ext, created_ip, created_by) VALUES ( ";
			$assoc_tmp_sql .= "[HOLDER], ";
			$assoc_tmp_sql .= pg_escape_string($cust_no) .", ";
			$assoc_tmp_sql .= "'".mktime() . "', ";
			$assoc_tmp_sql .= "'".pg_escape_string($org_code)."', ";
			$assoc_tmp_sql .= "'".pg_escape_string($client_type)."', ";
			$assoc_tmp_sql .= "'Outstanding', ";
			$assoc_tmp_sql .= "'".pg_escape_string($purchase_ord)."', ";
			$assoc_tmp_sql .= "'".$grate."', ";
			$assoc_tmp_sql .= "'".$prate."', ";
			( $ship_cost > 0.00 ) ? $assoc_tmp_sql .= "'".pg_escape_string($ship_cost)."', " : $assoc_tmp_sql .= "'0.00', ";
			$assoc_tmp_sql .= "'".$INVOICE['user']['name']."', ";
			$assoc_tmp_sql .= "'".pg_escape_string($creator_ext)."', ";
			$assoc_tmp_sql .= "'".returnRemoteaddr()."', ";
			$assoc_tmp_sql .= "'".$INVOICE['user']['name']."'";
			$assoc_tmp_sql .= ")";
			//echo $assoc_tmp_sql."<br />\n";			
		}
				
		 // Verify required data format for detail		 
		$counter = 0;
		for($i = 0; $i < count($s_date); $i++) {
			$counter++;			
			if(!is_numeric($s_date[$i])){
				$INVOICE['error'] .= "Illegal Date format on line $counter<br />";
			}
			if(verifyDescriptionlength($desc[$i])) {
				$INVOICE['error'] .= "Item description greater than 50 characters on line $counter<br />";
			}
				
			if(isset($qty[$i]) and strlen($qty[$i])){
				if(!is_numeric($qty[$i])){
					$INVOICE['error'] .= "Quantity must be numeric on line $counter<br />";
				}
			}
			if(isset($unit[$i]) and strlen($unit[$i])){
				if(!is_numeric($unit[$i])){
					$INVOICE['error'] .= "Unit price must be numeric on line $counter<br />";
				}
			}
			// Verify if necessary codes entered
			
			
			if( ( is_numeric($qty[$i]) && is_numeric($unit[$i]) ) && $unit[$i] != 0 ) {
				if( !strlen($obj_code[$i]) || !strlen($activity_code[$i]) ) {
					$INVOICE['error'] .= "Object or Activity Code ommitted on line $counter<br />";
				}
			}
			if(!isset($INVOICE['error'])) {
				$tmpdetail = '';
				$tmpdetail .= "INSERT INTO invoice_detail (inv_no, s_date, obj_code, activity_code, qty, unit_price, item_description) VALUES (";
				$tmpdetail .= "[HOLDER], ";
				$tmpdetail .= pg_escape_string($s_date[$i]) . ", ";
				if(!strlen($obj_code[$i])) {
					$tmpdetail .= "'00000', ";
				}
				else {
					$tmpdetail .= "'" . pg_escape_string($obj_code[$i]) . "', ";
				}
				if(!strlen($activity_code[$i])){
					$tmpdetail .= "'NOTAPP', ";
				}
				else {
					$tmpdetail .= "'" . pg_escape_string($activity_code[$i]) . "', ";
				}
				
				if(!strlen($qty[$i])){
					$tmpdetail .= "'0.00', ";
				}
				else {
					$tmpdetail .= "'" . pg_escape_string($qty[$i]) . "', ";
				}
				if(!strlen($unit[$i])){
					$tmpdetail .= "'0.00', ";
				}
				else {
					$tmpdetail .= "'" . pg_escape_string($unit[$i]) . "', ";
				}				
				$tmpdetail .= "'" . pg_escape_string($desc[$i]) . "'";
				$tmpdetail .= ")";
				//echo $tmpdetail."<br>\n";
				$tmpsql[] = $tmpdetail;
				
			}			
		}
		
		if(isset($INVOICE['error'])) {
			$mytitle = "IITS Invoice application - [Create Invoice - Error evaluation Insert new invoice]";
			$results .= '
<h2>'.$mytitle.'</h2>
';	
			echo "<p>".$INVOICE['error']."</p>";
			echo "<p><a href=\"javascript:history.go(-1)\">Back</a></p>\n";
			// exit;			
		}
		else {			
			if( !isset($INVOICE['inserted']) ) {				
				$db = db_connect();
				$res = execute_sql($db,"BEGIN");
				$res = execute_sql($db,"SELECT nextval('invoice_assoc_inv_no_seq')");
				$inv_no = pg_result($res,0,0);						
				$test1 = str_replace("[HOLDER]", $inv_no, $assoc_tmp_sql);
			 	$res = execute_sql($db, $test1);
				//echo "Invoice assoc done";
			
				for($k = 0; $k < count($tmpsql); $k++) {
					$test2 = '';
					$test2 = str_replace("[HOLDER]", $inv_no, $tmpsql[$k]);
					//echo $test2."<br>\n";
					$res = execute_sql($db,$test2);
				}
				$res = execute_sql($db, "COMMIT");
// *****************************************  Modify below to true when testing completed		
				$INVOICE['inserted'] = true;
				$mytitle = "IITS Invoice application - [Invoice Inserted]";
				$results .= "
<h2>$mytitle</h2>
<p class=\"address\">
<a href=\"send.php?f=send&amp;inv_no=".urlencode($inv_no)."\" target=\"print\">Send &amp; Print Invoice?</a><p>\n
";		
			}
			else {
				$mytitle = "IITS Invoice application - [Duplicate Prevention Alert]";
				$results .= '
<h2>'.$mytitle.'</h2>
<p class="address">A duplicate invoice would be created</p>
';
			}			
		}		
	}	
}	
else {
	$mytitle = "IITS Invoice application - [Create Invoice]";		
	$results .= '
<h2>'.$mytitle.'</h2>
';
	if(isset($INVOICE['error'])) {
		$results .= '<p class="error">';
		$results .= $INVOICE['error'];
		$results .= '</p>';
		$INVOICE['error'] = '';
		unset($INVOICE['error']);
	}
	include("includes/invoice_form1.php");
	$INVOICE['inserted'] = false;
	unset($INVOICE['inserted']);
}
$_SESSION['INVOICE'] = $INVOICE;
include("includes/template.php");
?>