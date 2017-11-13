<?php
session_start();
require("lib/invoicelib.php");
if( !(isset($INVOICE['user']['access']) and is_numeric($INVOICE['user']['access']) && ($INVOICE['user']['access'] <= 2)) ) {	
	$INVOICE['error'] = "This application is restricted";
	$_SESSION['INVOICE'] = $INVOICE;
	header("Location: ".$conf['access']['loginUrl'] );
	exit;
}
$f = returnVar('f');
$inv_no = returnVar('inv_no');
$inv_date = returnVar('inv_date');
$action = returnVar('action');
$cust_no = returnVar('cust_no');
$c_type = returnVar('c_type');
$client_type = returnVar('client_type');
$last_updated = returnVar('last_updated');
$status = returnVar('status');
$gst = returnVar('gst');
$pst = returnVar('pst');
$creator_ext = returnVar('creator_ext');
$org_code = returnVar('org_code');
$activity_code = returnVar('activity_code');
$obj_code = returnVar('obj_code');
$purchase_ord = returnVar('purchase_ord');
$ship_cost = returnVar('ship_cost');

$s_date = returnVar('s_date');
$qty = returnVar('qty');
$unit = returnVar('unit');
$desc = returnVar('desc');
$line = returnVar('line');
$line_no = returnVar('line_no');
$delete = returnVar('delete');
$update = returnVar('update');
$display_cust_name = returnVar('display_cust_name');


$results = '';
$res = '';
$sql = '';
$app = "edit-invoice";

if($f == "detail"){	
	if(! (is_numeric($inv_no) && ($action == "edit" || $action == "add") ) ) {
		$mytitle = "IITS Invoice application - [Missing required fields]";		
		$results .= "<h2>$mytitle</h2>\n";
		include("includes/edit_form1.php");		
	}
	else {
		// sql for invoice
		$db = db_connect();		
		$sql .= "SELECT A1.inv_no, A1.cust_no, A1.inv_date, A1.org_code, A1.client_type, A1.status, A1.purchase_ord, A1.gst_rate, A1.pst_rate, A1.ship_cost, A1.revision_num, A1.creator, A1.creator_ext, A1.last_updated, C1.b_comp_name, C1.b_contact  FROM invoice_assoc A1, customers C1 WHERE A1.inv_no = ".pg_escape_string($inv_no)." AND (A1.cust_no = C1.cust_no)";
		if($INVOICE['user']['access'] != "0") {
			$sql .= " AND (A1.creator = '".pg_escape_string($INVOICE['user']['name'])."')";
		}
		//echo $sql;
		// $sql .= "SELECT A1.inv_no, A1.cust_no, A1.inv_date, A1.org_code, A1.client_type, A1.status, A1.purchase_ord, A1.gst_rate, A1.pst_rate, A1.ship_cost, A1.revision_num, A1.creator, A1.creator_ext, A1.last_updated, C1.b_comp_name, C1.b_contact  FROM invoice_assoc A1, customers C1 WHERE A1.inv_no = ".pg_escape_string($inv_no)." AND A1.status != 'Paid' AND (A1.cust_no = C1.cust_no)";
		
		$res = execute_sql($db,$sql);
		
		if(pg_num_rows($res) != 1 ){
			$mytitle = "IITS Invoice application - [Invoice not found]";			
			$results .= "<h2>$mytitle</h2>\n";
			include("includes/edit_form1.php");
		}
		else {
			$customerdata = pg_fetch_array($res,0);				
			if($customerdata['gst_rate'] == "0.00") { $gstExempt = "GST exempt"; } else { $gstExempt = "GST payable"; }
			if($customerdata['pst_rate'] == "0.00") { $pstExempt = "PST exempt"; } else { $pstExempt = "PST payable"; }
			$taxStatus = '<p class="exempt"><u>Tax Status</u><br />'.$gstExempt.'<br />'.$pstExempt.'</p>';
			if($customerdata['status'] == "Paid") {
				// if paid no edit print only
				$_SESSION['INVOICE'] = $INVOICE;				
				header("Location: https://".$_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF'])."/print.php?f=print&inv_no=".urlencode($inv_no));
				exit;
			}
			
			$x = makeVarselect($INVOICE['global-vars']['org'], "org_code", $customerdata['org_code']); // select box creating org code
			$y = makeVarselect($INVOICE['global-vars']['status'], "status", $customerdata['status']); // select box creating Status
			$sql = '';
			$sql .= "SELECT ALL * FROM invoice_detail WHERE inv_no = " . pg_escape_string($inv_no);
			$res = execute_sql($db,$sql);
			if(!pg_num_rows($res)) { // Invoice found, no details
				// Add lines to existing invoice
				$org = codeReturn($INVOICE['global-vars']['org'], $customerdata['org_code']);
				$mytitle = "IITS Invoice application - [Add items to Invoice]";
				$results .= "<h2>$mytitle</h2>\n";								
				if(isset($INVOICE['error'])) {
					$results .= '<p class="error">';
					$results .= $INVOICE['error'];
					$results .= '</p>';
					$INVOICE['error'] = '';
					unset($INVOICE['error']);
				}				
				$results .= "<p>Invoice does not contain any detail lines.  You may add more lines here</p>";
				include("includes/edit_add_form3.php");
			}
			else {
				 if($action ==  "edit") {				 					
					$mytitle = "IITS Invoice application - [Edit invoice detail]";
					$results .= "<h2>$mytitle</h2>\n";
					if(isset($INVOICE['error'])) {
						$results .= '<p class="error"><b>Errors</b> <br />';
						$results .= $INVOICE['error'];
						$results .= '</p>';
						$INVOICE['error'] = '';
						unset($INVOICE['error']);
					}
					include("includes/edit_form2.php");					
				}
				elseif($action = "add") {
					 // Show old data plus 12 more text fields
					$org = codeReturn($INVOICE['global-vars']['org'], $customerdata['org_code']);					
					$mytitle = "IITS Invoice application - [Add to Invoice Detail]";
					$results .= "<h2>$mytitle</h2>\n";									
					if(isset($INVOICE['error'])) {
						$results .= '<p class="error">Errors<br />';
						$results .= $INVOICE['error'];
						$results .= '</p>';
						$INVOICE['error'] = '';
						unset($INVOICE['error']);
					}					
					
					include("includes/edit_add_form2.php");					
				}
				else {
					$mytitle = "IITS Invoice application - [Invalid Action]";					
					$results .= "<h2>$mytitle</h2>\n";
					include("includes/edit_form1.php");					
				}				
			}
		}
	}
}
elseif($f == "calculate") {
	// When edit flag selected from action
	$error = (string) null;
	
	 
	
	$assoc_vars = ''; // Variables to insert into association table	
	$detail_vars = '';
	if(!is_numeric($cust_no)){
		$error .= "Customer Invalid or not selected<br />";
		$_SESSION['INVOICE'] = $INVOICE;
		header("Location: https://".$_SERVER['HTTP_HOST'] .$_SERVER['PHP_SELF']);
		exit;
	}
	else {
		$redirect = 'https://'.$_SERVER['HTTP_HOST'] .$_SERVER['PHP_SELF'].'?f=detail&action='.urlencode($action).'&inv_no='.urlencode($inv_no);
		//$redirect = $PHP_SELF."?f=detail&action=".urlencode($action)."&inv_no=".urlencode($inv_no);
		
		 // General validation
		if(!strlen(trim(chop($c_type)))){
			$error .= "Client type not selected<br />\n";
		}
		else {
			$assoc_vars .= '
<!-- Main Table Update -->
<input type="hidden" name="inv_no" value="'.htmlspecialchars(trim(chop($inv_no))).'" />
<input type="hidden" name="inv_date" value="'.htmlspecialchars(trim(chop($inv_date))).'" />
<input type="hidden" name="action" value="'.htmlspecialchars(trim(chop($action))).'" />
<input type="hidden" name="last_updated" value="'.htmlspecialchars(trim(chop($last_updated))).'" />
<input type="hidden" name="status" value="'.htmlspecialchars(trim(chop($status))).'" />
<input type="hidden" name="cust_no" value="'.htmlspecialchars(trim(chop($cust_no))).'" />
<input type="hidden" name="client_type" value="'.htmlspecialchars(trim(chop($c_type))).'" />
<input type="hidden" name="gst" value="'.htmlspecialchars(trim(chop($gst))).'" />
<input type="hidden" name="pst" value="'.htmlspecialchars(trim(chop($pst))).'" />
';
		}
		if( strlen(trim(chop($creator_ext))) != 4 && !is_numeric($creator_ext)){
			$error .= "Extension must be 4 numbers<br />\n";
		}
		else {
			$assoc_vars .= '<input type="hidden" name="creator_ext" value="'.htmlspecialchars(trim(chop($creator_ext))).'" />
';
		}
		if(!strlen(trim(chop($org_code)))){
			$error .= "Invalid ORG code selected<br />\n";
		}
		else {
			$assoc_vars .= '<input type="hidden" name="org_code" value="'.htmlspecialchars(trim(chop($org_code))).'" />';
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
<input type="hidden" name="purchase_ord" value="'.htmlspecialchars(trim(chop($purchase_ord))).'" />';
		}
		 // Valid first line Obj, Activty, qty unit
		if(!( strlen($obj_code[0]) && strlen($activity_code[0]) && is_numeric($qty[0]) && is_numeric($unit[0]))){
			$error .= "Invoice must contain at least one line<br />\n";
		}
		if(!verifyDate($s_date[0])){
			$error .= "Invalid date specified on Line 1 of invoice detail<br />\n";
		}
		 // First error check
		if($error) {
			$INVOICE['error'] = $error;
			$_SESSION['INVOICE'] = $INVOICE;		
			header("Location: $redirect");
			exit;			
		}
		else {
			
			
			// Calculate Invoice		
			 // Line by line verification
			 // Initalize totals
			 // Display Array
			$tmpresults = array();
			$valid = 0;
			$total = 0;
			$counter = 0;			
			for($i = 0; $i < count($line); $i++) {			
				$counter++; // Display Line counter
				$extended = 0;
				$delflag[$i] = false;
				if(verifyDescriptionlength($desc[$i])) {
						$error .= "Item description greater than 50 characters on line $counter<br />";
				}		
				if(is_numeric($qty[$i])) {
					$printQty = $qty[$i];
				}
				else {
					$printQty = '0.00';
				}
				if(is_numeric($unit[$i])) {
					$printUnit = $unit[$i];
				}
				else {
					$printUnit = '0.00';
				}							
				if(strlen($s_date[$i]) || strlen($obj_code[$i]) || strlen($activity_code[$i]) || strlen($unit[$i]) || strlen($desc[$i]) || strlen($qty[$i]) || strlen($delete[$i])) {				
					
					 // Verify if date is it valid
					if($x = verifyDate($s_date[$i])) {
						$servicedate = $x;
					}
					else {
						$error .= "Invalid date specified on invoice line $counter<br />\n";
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
						$error .= "Unit Price entered on line $counter , No quantity entered<br />\n";
					}
					if(strlen($qty[$i]) && !strlen($unit[$i])) {
						$error .= "Quantity entered on line $counter , No unit price entered<br />\n";
					}
					
					if( (strlen($obj_code[$i]) || strlen($activity_code[$i])) &&  (!strlen($unit[$i]) && !strlen($qty[$i]) )) {
						$error .= "Unit price or Quantity omitted on line $counter<br />\n";
					}				
					if( ($qty[$i] != 0) && ($unit[$i] != 0.00 ) ) {							
						if( !strlen(trim(chop($obj_code[$i]))) ) {
							$error .= "Object code not entered on line $counter<br />\n";
						}
						if( !strlen(trim(chop($activity_code[$i]))) ) {
							$error .= "Activity code not entered on line $counter<br />\n";
						}
												
						if(!isset($delete[$i])) {
							$delflag[$i] = false;
							$extended = $qty[$i] * $unit[$i];
							$total = $total + $extended;														
						}
						else {
							$delflag[$i] = true;
						}
						
					}
					else {
															
						if(!isset($delete[$i])) {
							$delflag[$i] = false;
						}
						else {
							$delflag[$i] = true;
						}
											
					}
					
					if(!$error) {
						if(isset($delete[$i])){
							$detail_vars .= '
<!-- Invoice line '.$i.' -->
<input type="hidden" name="line_no['.$valid.']" value="'.htmlspecialchars($line[$i]).'" />
<input type="hidden" name="delete['.$valid.']" value="'.htmlspecialchars($line[$i]).'" />
';
						}
						else {
						
						$detail_vars .= '
<!-- Invoice line '.$i.' -->
<input type="hidden" name="line_no['.$valid.']" value="'.htmlspecialchars($line[$i]).'" />
<input type="hidden" name="s_date['.$valid.']" value="'.$servicedate.'" />
<input type="hidden" name="obj_code['.$valid.']" value="'.htmlspecialchars($obj_code[$i]).'" />
<input type="hidden" name="activity_code['.$valid.']" value="'.htmlspecialchars($activity_code[$i]).'" />
<input type="hidden" name="qty['.$valid.']" value="'.htmlspecialchars($printQty).'" />
<input type="hidden" name="desc['.$valid.']" value="'.htmlspecialchars($desc[$i]).'" />
<input type="hidden" name="unit['.$valid.']" value="'.htmlspecialchars($printUnit).'" />
';
						}			
						$valid++;
					}					
					 // Print variable array 
					$tmpresults[] = (array("date" => $servicedate, "obj_code" => $obj_code[$i], "activity_code" => $activity_code[$i], "qty" => $printQty, "desc" => $desc[$i], "unit" => $printUnit, "extended" => number_format($extended,2,'.',','), "delete" => $delflag[$i] ));					
				}
			}			
			if($error) {
				$INVOICE['error'] = $error;
				$_SESSION['INVOICE'] = $INVOICE;			
				header("Location: $redirect");
				exit;				
			}
			else {
				 // Add formatting / totals for confirmation screen of new invoice here
				$overtotal = calcTotal($total, $gst, $pst, $ship_cost,null,null,$inv_date);				
				$mytitle = "IITS Invoice application - [Invoice confirmation, Total verification - edit]";				
				$results .= "<h2>$mytitle</h2>\n";
				include("includes/edit_invoice_verify_form3.php");				
				$results .= '
<form method="post" action="'.$_SERVER['PHP_SELF'].'?f=update">
'. $assoc_vars.'
'.$detail_vars.'
<br />
<input type="submit" value="Update Invoice" />
</form>
';
			}		
		}			
	}	
}
elseif($f == "update") {
	
	$error = '';
	if(!is_numeric($inv_no)){
		$INVOICE['error'] .= "Invalid Invoice selected<br />";		
		$_SESSION['INVOICE'] = $INVOICE;
		header("Location: https://".$_SERVER['HTTP_HOST'] .$_SERVER['PHP_SELF']);
		exit;
	}
	else {
		$redirect = "https://".$_SERVER['HTTP_HOST'] .$_SERVER['PHP_SELF']."?f=detail&action=".urlencode($action)."&inv_no=".urlencode($inv_no);		
		$tmpsql = array();
		
		 // Verify required fields in Association Table
		if(!strlen(trim(chop($org_code)))){
			$error .= "Org Code omitted<br />";
		}
		if(!strlen(trim(chop($client_type)))){
			$error .= "Client Type omitted<br />";
		}
		if(!strlen(trim(chop($creator_ext)))){
			$error .= "Creator Telephone extension omitted<br />";
		}
		if(!strlen(trim(chop($gst)))){
			$error .= "Tax Value omitted (GST)<br />";
		}
		if(!strlen(trim(chop($pst)))){
			$error .= "Tax Value omitted (PST)<br />";
		}
		if(strlen($ship_cost)){
			if(!is_numeric($ship_cost)) {
			$error .= "Shipping Cost must be numeric<br />";
			}
		}		
		$db = db_connect();
		$res = execute_sql($db, "BEGIN");
		if(!$error) {						
			$res = execute_sql($db,"SELECT revision_num from invoice_assoc WHERE inv_no = ".pg_escape_string($inv_no));
			$rev_no = pg_fetch_result($res,0,'revision_num') + 1;			
			$assoc_tmp_sql = '';
			$assoc_tmp_sql .= "UPDATE invoice_assoc SET ";
			$assoc_tmp_sql .= "client_type = '".pg_escape_string(trim(chop($client_type))) . "', ";
			$assoc_tmp_sql .= "org_code = '".pg_escape_string($org_code)."', ";
			$assoc_tmp_sql .= "status = '".pg_escape_string($status)."', ";
			$assoc_tmp_sql .= "gst_rate = '".pg_escape_string($gst)."', ";
			$assoc_tmp_sql .= "pst_rate = '".pg_escape_string($pst)."', ";
			$assoc_tmp_sql .= "purchase_ord = '".pg_escape_string($purchase_ord)."', ";
			( $ship_cost > 0.00 ) ? $assoc_tmp_sql .= "ship_cost = '".pg_escape_string($ship_cost)."', " : $assoc_tmp_sql .= "ship_cost = '0.00', ";
			$assoc_tmp_sql .= "revision_num = ".pg_escape_string($rev_no).", ";
			$assoc_tmp_sql .= "creator_ext = '".pg_escape_string($creator_ext)."', ";
			$assoc_tmp_sql .= "updated_by = '".pg_escape_string($INVOICE['user']['name'])."', ";
			$assoc_tmp_sql .= "updated_on = '" . mktime() . "', ";
			$assoc_tmp_sql .= "updated_ip = '".returnRemoteaddr()."', ";
			$assoc_tmp_sql .= "last_updated = '".mktime() . "' ";			
			$assoc_tmp_sql .= "WHERE inv_no = '".pg_escape_string($inv_no)."' ";
			if(!strlen($last_updated)){
				$assoc_tmp_sql .= "AND last_updated IS NULL";
			}
			else {
				$assoc_tmp_sql .= "AND last_updated = '".pg_escape_string($last_updated)."'";
			}			
			$res = execute_sql($db, $assoc_tmp_sql);			
			if(pg_affected_rows($res) == 0 ) {
// Prevent illegal update of old invoice
				$res = execute_sql($db, "ROLLBACK");
				$error .= "Unable to update, Invoice in use<br />";
				$INVOICE['error'] = $error;				
				$_SESSION['INVOICE'] = $INVOICE;
				header("Location: $redirect");
				exit;				
			}			
		}		
		 // Verify required data format for detail
		$counter = 0;		
		for($i = 0; $i < count($line_no); $i++) {
			$counter++;
			$sql = '';
			if(isset($delete[$i])) {
				$sql .= "DELETE FROM invoice_detail WHERE line_no = ".pg_escape_string($delete[$i]);
				$res = execute_sql($db,$sql);
				 // echo $sql . "<br />\n";	
			}
			else {				
				if(!is_numeric($s_date[$i])){
					$error .= "Illegal Date format on line $counter<br />";
				}
				if(verifyDescriptionlength($desc[$i])) {
						$error .= "Item description greater than 50 characters on line $counter<br />";
				}
				if(strlen($qty[$i])){
					if(!is_numeric($qty[$i])){
						$error .= "Quantity must be numeric on line $counter<br />";
					}
				}
				if(strlen($unit[$i])){
					if(!is_numeric($unit[$i])){
						$error .= "Unit price must be numeric on line $counter<br />";
					}
				}
				if( ( is_numeric($qty[$i]) && is_numeric($unit[$i]) ) && $unit[$i] != 0 ){
					if( !strlen($obj_code[$i]) || !strlen($activity_code[$i]) ) {
						$error .= "Object or Activity Code omitted on line $counter<br />";
					}
				}				
				if(!$error) {
					$sql = '';
					$sql .= "UPDATE invoice_detail set ";
					$sql .= "s_date = " . pg_escape_string($s_date[$i]).", ";					
					strlen($obj_code[$i]) ? $sql .= "obj_code = '". pg_escape_string($obj_code[$i])."', " : $sql .= "obj_code = '00000', ";
					strlen($activity_code[$i]) ? $sql .= "activity_code = '". pg_escape_string($activity_code[$i])."', " : $sql .= "activity_code = 'NOTAPP', ";
					$sql .= "item_description = '".pg_escape_string(trim(chop($desc[$i]))) . "', ";
					if(!strlen($qty[$i])){
						$sql .= "qty = '0.00', ";
					}
					else {
						$sql .= "qty = '" . pg_escape_string($qty[$i]) . "', ";
					}
					if(!strlen($unit[$i])){
						$sql .= "unit_price = '0.00' ";
					}
					else {
						$sql .= "unit_price = '" . pg_escape_string($unit[$i]) . "' ";
					}
					$sql .= "WHERE line_no = " . pg_escape_string($line_no[$i]);
					$res = execute_sql($db,$sql);
					//echo $sql . "<br />\n";					
				}
				else {
					$res = execute_sql($db,"ROLLBACK");
					
					$error .= "Update errors encountered, invoice not updated<br />\n";
					$INVOICE['error'] = $error;
					$_SESSION['INVOICE'] = $INVOICE;
					header("Location: $redirect");
					exit;
				}				
			}
		}		
		if($error) {
			$res = execute_sql($db,"ROLLBACK");
			$error .= "Update errors encountereds, invoice not updated<br />\n";
			$INVOICE['error'] = $error;			
			$_SESSION['INVOICE'] = $INVOICE;
			header("Location: $redirect");
			exit;
		}
		else {
			$res = execute_sql($db, "COMMIT");				
			$mytitle = "IITS Invoice application - [Invoice Updated]";
				$results .= "
<h2>$mytitle</h2>
<p class=\"address\"><a href=\"send.php?f=send&amp;inv_no=".urlencode($inv_no)."\">Send and Print Updated Invoice?</a><p>\n
";		
		}			
	}		
}
elseif($f == "re_calculate") {
	$error = '';
	$INVOICE['error'] = ''; // Begin Required field eval	 
	$assoc_vars = ''; // Variables to insert into association table
	$detail_vars = '';
	if(!is_numeric($inv_no)){
		$INVOICE['error'] .= "Customer Invalid or not selected<br />";
		$_SESSION['INVOICE'] = $INVOICE;
		header("Location: https://".$_SERVER['HTTP_HOST'] .$_SERVER['PHP_SELF']);
		exit;
	}
	else {		
		$redirect = "https://".$_SERVER['HTTP_HOST'] .$_SERVER['PHP_SELF']."?f=detail&action=".urlencode($action)."&inv_no=".urlencode($inv_no);
		$assoc_vars .= '
<input type="hidden" name="action" value="add" />
<input type="hidden" name="inv_no" value="'.pg_escape_string(trim(chop($inv_no))).'" />
<input type="hidden" name="inv_date" value="'.pg_escape_string(trim(chop($inv_date))).'" />
<input type="hidden" name="cust_no" value="'.pg_escape_string(trim(chop($cust_no))).'" />
<input type="hidden" name="c_name" value="'.pg_escape_string(trim(chop($display_cust_name))).'" />
<input type="hidden" name="last_updated" value="'.pg_escape_string(trim(chop($last_updated))).'" />';
		if(strlen(trim(chop($ship_cost)))) {
			if(is_numeric($ship_cost)) {
				$assoc_vars .= '
<input type="hidden" name="ship_cost" value="'.pg_escape_string(trim(chop($ship_cost))).'" />';
			}
			else {
				$error .= "Shipping cost must be numeric<br />\n";
			}
		}		
		$tmpresults = array();
		$valid = 0;
		$total = 0;
		$counter = 0;			
		for($i = 0; $i < count($line); $i++) {					
			$counter++; // Display Line counter
			$extended = 0;
			$delflag[$i] = false;	
			if(is_numeric($qty[$i])) {
				$printQty = $qty[$i];
			}
			else {
				$printQty = '0.00';
			}
			if(is_numeric($unit[$i])) {
				$printUnit = $unit[$i];
			}
			else {
				$printUnit = '0.00';
			}
			
			if(strlen($s_date[$i]) || strlen($obj_code[$i]) || strlen($activity_code[$i]) || strlen($unit[$i]) || strlen($desc[$i]) || strlen($qty[$i])) {
				// Verify if date is it valid
				if($x = verifyDate($s_date[$i])) {
					$servicedate = $x;
				}
				else {
					$error .= "Invalid date specified on invoice line $counter<br />\n";
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
					$error.= "Unit Price entered on line $counter , No quantity entered<br />\n";
				}
				if(!strlen($unit[$i]) && strlen($qty[$i])) {
					$error .= "Quantity entered on line $counter , No unit price entered<br />\n";
				}
				if( (strlen($obj_code[$i]) || strlen($activity_code[$i])) &&  (!strlen($unit[$i]) && !strlen($qty[$i]) )) {
					$error .= "Unit price or Quantity omitted on line $counter<br />\n";
				}									
				if( ( $qty[$i] != 0 ) && ($unit[$i] !=0.00 ) ) {					
					if( !strlen(trim(chop($obj_code[$i]))) ) {
						$error .= "Object code not entered on line $counter<br />\n";
					}
					if( !strlen(trim(chop($activity_code[$i]))) ) {
						$error.= "Activity code not entered on line $counter<br />\n";
					}
					$extended = ($qty[$i] * $unit[$i]) * 1.00;
					$total = $total + $extended;						
				}				
				if(is_numeric($extended)) {
					$extended = number_format($extended,2,'.','');
				}				
				if(!$error) {						
					$detail_vars .= '
<!-- Invoice line '.$i.' -->
<input type="hidden" name="line_no['.$valid.']" value="'.htmlspecialchars($line[$i]).'" />
<input type="hidden" name="s_date['.$valid.']" value="'.$servicedate.'" />
<input type="hidden" name="obj_code['.$valid.']" value="'.htmlspecialchars($obj_code[$i]).'" />
<input type="hidden" name="activity_code['.$valid.']" value="'.htmlspecialchars($activity_code[$i]).'" />
<input type="hidden" name="qty['.$valid.']" value="'.htmlspecialchars($printQty).'" />
<input type="hidden" name="desc['.$valid.']" value="'.htmlspecialchars($desc[$i]).'" />
<input type="hidden" name="unit['.$valid.']" value="'.htmlspecialchars($printUnit).'" />
';				
					$valid++;
					// echo $printUnit." " . $printQty."<br />";
				}
				$tmpresults[] = (array("date" => $servicedate, "obj_code" => $obj_code[$i], "activity_code" => $activity_code[$i], "qty" => $printQty, "desc" => $desc[$i], "unit" => $printUnit, "extended" => number_format($extended,2,'.',','), "delete" => $delflag[$i] ));					
			}				
		}			
		if($error) {
			$INVOICE['error'] = $error;
			$INVOICE['subvars'] = $_POST;		
			$_SESSION['INVOICE'] = $INVOICE;
			header("Location: $redirect");
			exit;				
		}
		else {			
			$existdata = '';
			// No errors, Bring back existing lines for total calculations
			$res = execute_sql(db_connect(), "SELECT ALL * FROM invoice_detail WHERE inv_no = ".pg_escape_string($inv_no));
			if(!pg_num_rows($res)) {
				$existdata = '
	<tr valign="top">
		<td class="address" colspan="6">No existing detail lines</td>
	</tr>';
			}
			else {
				for($j = 0; $j < pg_num_rows($res); $j++) {
					$extended = 0;
					$record = pg_fetch_array($res,$j);
					
					// $results .= '<pre>'.print_r($record,1).'</pre>';
					
					
					$objtmp = codeReturn($INVOICE['global-vars']['obj'],$record['obj_code']);
					$acttmp = codeReturn($INVOICE['global-vars']['activity'],$record['activity_code']);
					if( ( $record['qty'] >= 1 ) && ( $record['unit_price'] !=0.00 ) ) {
						$extended = $record['qty'] * $record['unit_price'];
						$total = $total + $extended;
					}					
					$existdata .= '
	<tr valign="top" class="addold">
		<td class="address">'.date("d/m/Y",$record['s_date']).'</td>
		<td class="address">'.$objtmp['text'].'<br />'.$acttmp['text'].'</td>
		<td class="address">'.number_format($record['qty'],2, '.', '').'</td>
		<td class="address">'.htmlspecialchars($record['item_description']).'</td>
		<td class="address">'.number_format($record['unit_price'],2, '.', '').'</td>
		<td class="address">'.number_format($extended, 2, '.', '').'</td>
	</tr>';
				}				
			}			
			 // Add formatting / totals for confirmation screen of new invoice here
			$overtotal = calcTotal($total, $gst, $pst, $ship_cost,null,null,$inv_date);			
			$mytitle = "IITS Invoice application - [Invoice confirmation, Total verification]";			
			$results .= "<h2>$mytitle</h2>\n";
			include("includes/add_invoice_verify_form3.php");				
			$results .= '
<form method="post" action="'.htmlentities($_SERVER['PHP_SELF']).'?f=insertnew">
'. $assoc_vars.'
'.$detail_vars.'
<br />
<input type="submit" value="Update Invoice" />
</form>
';				
		}		
	}			
}
elseif($f == "insertnew") {
	if(!is_numeric($inv_no)){
		$INVOICE['error'] .= "Invalid Invoice selected<br />";
		$_SESSION['INVOICE'] = $INVOICE;
		header("Location: https://".$_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF'])."/edit_invoice.php");
		exit;
	}
	else {
		$db = db_connect();
		$res = execute_sql($db,"BEGIN"); // Begion Transaction
		$res = execute_sql($db,"SELECT revision_num FROM invoice_assoc WHERE inv_no = ".pg_escape_string($inv_no));
		$rev_no = pg_fetch_result($res,0,'revision_num') + 1 ;		
			
		$sql = "UPDATE invoice_assoc set ";
		$sql .= "revision_num = $rev_no, ";		
		$sql .= "updated_by = '".pg_escape_string($INVOICE['user']['name'])."', ";
		$sql .= "updated_on = '" . mktime() . "', ";
		$sql .= "updated_ip = '".returnRemoteaddr()."', ";
		( $ship_cost > 0.00 ) ? $sql .= "ship_cost = '" . pg_escape_string($ship_cost) . "', " : $sql .= "ship_cost = '0.00', ";
		$sql .= "last_updated = '".mktime() . "' ";			
		$sql .= "WHERE inv_no = '".pg_escape_string($inv_no)."' ";
		
		if(!strlen($last_updated)){
			$sql .= "AND last_updated IS NULL";
		}
		else {
			$sql .= "AND last_updated = ".pg_escape_string($last_updated);
		}
		$res = execute_sql($db,$sql);		
		if(pg_affected_rows($res) == 0 ) {
			 // Prevent illegal update of old invoice
			$res = execute_sql($db, "ROLLBACK");
			$$error .= "Unable to update, Invoice in use<br />";
			$INVOICE['error'] = $error;
			$redirect = "https://".$_SERVER['HTTP_HOST'] .$_SERVER['PHP_SELF']."?f=detail&action=".urlencode($action)."&inv_no=".urlencode($inv_no);
			$_SESSION['INVOICE'] = $INVOICE;
			header("Location: $redirect");
			exit;				
		}
		else {
			$results .= "<h3>Associations added successfully</h3>\n";			
			for($i = 0; $i < count($line_no); $i++){
				$subqty = '0.00';
				$subunit = '0.00';
				$subdate = mktime();
				
				if(is_numeric($qty[$i])) {
					$subqty = $qty[$i];
				}
				if(is_numeric($unit[$i])) {
					$subunit = $unit[$i];
				}
				if(is_numeric($s_date[$i])) {
					$subdate = $s_date[$i];
				}		
				$sql = '';
				$sql .= "INSERT INTO invoice_detail (inv_no, s_date, obj_code, activity_code, qty, unit_price, item_description) VALUES (";
				$sql .= "'".pg_escape_string($inv_no)."', ";
				$sql .= "'".pg_escape_string($subdate)."', ";
				strlen($obj_code[$i]) ? $sql .= "'". pg_escape_string($obj_code[$i])."', " : $sql .= "'00000', ";				
				strlen($activity_code[$i]) ? $sql .= "'". pg_escape_string($activity_code[$i])."', " : $sql .= "'NOTAPP', ";				
				$sql .= "'". pg_escape_string($subqty)."', ";
				$sql .= "'". pg_escape_string($subunit)."', ";
				$sql .= "'". pg_escape_string($desc[$i])."'";
				$sql .= ")";
				//echo $sql."<br />\n";
				$res = execute_sql($db,$sql);
				 		
			}
			$res = execute_sql($db,"COMMIT");
			$mytitle = "IITS Invoice application - [".count($line_no). " Lines Added to invoice number ".htmlspecialchars($inv_no)."]";			
			$results .= "<h2>$mytitle</h2>\n";
			$results .= "<p class=\"address\"><a href=\"send.php?f=send&amp;inv_no=".urlencode($inv_no)."\" target=\"new\">Send and Print Update Invoice?</a><p>\n";
						 
		}		
		
	}
}
else {
	$db = db_connect();
	$mytitle = "IITS Invoice application - [Edit Invoice]";	
	$results .= "<h2>$mytitle</h2>\n";	
	if(isset($INVOICE['error'])) {
		$results .= '<p class="error">';
		$results .= $INVOICE['error'];
		$results .= '</p>';
		$INVOICE['error'] = '';
		unset($INVOICE['error']);
	}					
	include("includes/edit_form1.php");
	$results .= '<p class="note"><strong>View Flag</strong>: ';
	if( $INVOICE['user']['access'] == 0 ) {
		$results .= 'View All';
	}
	else {
		$results .= 'View only those created by ' . htmlspecialchars($INVOICE['user']['name']);
	}
	$results .= '
</p>
';
}
$_SESSION['INVOICE'] = $INVOICE;
include("includes/template.php");
?>