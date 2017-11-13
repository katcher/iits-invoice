<?php
/* Function File IITS Invoicing */
require(dirname(__FILE__)."/conf.php");
if (!isset($_COOKIE['PHPSESSID'])) {
	$INVOICE = array();
	$_SESSION['INVOICE'] = array();	
}
else{
	if(isset($_SESSION['INVOICE'])) {
		$INVOICE = $_SESSION['INVOICE'];
	}
	else {
		$_SESSION['INVOICE'] = array(); 
		$INVOICE = array();
	}
}
/* ++++++ General Functions ++++++ */
function db_connect () {
	global $conf;
	$conn = (string) null;
	$conn .= "host={$conf['db']['host']} port={$conf['db']['port']} dbname={$conf['db']['name']} user={$conf['db']['user']} password={$conf['db']['pwd']}";
	//die($conn);
    $db = pg_connect($conn);
    if (!$db) {
        echo "<p>".pg_last_error($db)." Database Unavailable, please try again";
        die;
    }
    else {
    	return $db;
    }
}
function execute_sql($db, $sql) {// Execute sql
	$result = @pg_exec ($db,$sql);	
	if (!$result) {
		echo "<p><b>SQL error occurred</b></p>\n";
		echo "<p>" . pg_errormessage($db) . "<br /></p>\n";
		return false;
		//exit;
	}
	else {
		return $result;
	}
}
function char_Replace($input) {
	$input = strtolower($input);	
	$input = preg_replace("/\W/"," ",$input); /* Remove non alphanumeric*/
	$input = str_replace ("_"," ",$input); /* Remove _ character*/
	$input = strtr($input,"àáâãäåçèéêëìíîïñòóôõöøùúûüýÿ","aaaaaaceeeeiiiinoooooouuuuuy"); /* Replace accents*/
	$input = str_replace ("ß","ss",$input);/* Remove all other non english chars with > 1 letter replacement*/
	$input = str_replace ("æ","ae",$input);	
	$input = str_replace ("þ","th",$input);
	$input = str_replace ("ð","dh",$input);
	return $input;
}
function cleanData($input) {	
	$input = preg_replace("/\s|\r\n|\n\r|\n|\r/","",$input); /* Remove Spaces and CRLF */
	return $input;
}
function printIfexist($string = '') {
	return strlen(trim(chop($string))) > 0 ? true : false;	
}
function verifyDate($date) {
	/* $input = preg_replace("/\W/","",$date); $input = str_replace("_","", $date); */
	$year = null; $month = null; $day = null;  $curryear = date("Y");
	$input = ereg_replace( '[^0-9]+', '', $date);
	if(is_numeric($input)) {
		$year = 0; $month = 0; $day = 0;
		$tmp = preg_split('/[:;\/.-]+/',$date);
		if( count($tmp) !== 3 ) { return false; }
		list($day, $month, $year) = $tmp;			
			if( ($year >= 1998) and ($year <= ($curryear + 10)) ) {
				if( ($month > 0) and ($month <= 12)) {
					if( ($day > 0) and ($day <= 31)) {
						if(checkdate($month,$day,$year)) {
							return mktime(0,0,0,$month,$day,$year);
						}
					}				
				}
			}			
	}
	return false;
}
function is_email($email) { 
	// First, we check that there's one @ symbol, and that the lengths are right 
	if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) { 
		// Email invalid because wrong number of characters in one section, or wrong number of @ symbols. 
		return false; 
	}
	// Split it into sections to make life easier 
	$email_array = explode("@", $email); 
	$local_array = explode(".", $email_array[0]); 
	for ($i = 0; $i < sizeof($local_array); $i++) { 
		if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) { 
			return false; 
		} 
	} 
	if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
		// Check if domain is IP. If not, it should be valid domain name 
		$domain_array = explode(".", $email_array[1]); 
		if (sizeof($domain_array) < 2) { 
			return false; 
			// Not enough parts to domain 
		} 
		for ($i = 0; $i < sizeof($domain_array); $i++) { 
			if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) { 
				return false; 
			} 
		} 
	} 
	return true;
}
function makeVariablearray($db, $field){
	$varArray = array();
	$res = execute_sql($db,"SELECT ALL * FROM ". $field . " ORDER BY lower(".$field. "_desc) ASC");
	if(pg_num_rows($res)){
		for($i = 0; $i < pg_num_rows($res); $i++) {
			$rec = pg_fetch_array($res,$i);
			$varArray[] = array($rec[0],$rec[1]);
		}		
	}
	else {
		$varArray[] = "Broken";
	}
	return $varArray;		
}
function makeStatus($varArray, $fieldname, $selected = '') {	
	$selbox = '';
	$selbox .= '
<select name="'.$fieldname.'" size="1" id="'.$fieldname.'">
	<option value="">Select</option>';
	for($j = 0; $j < count($varArray); $j++) {
		$selbox .= '
	<option value="'.htmlspecialchars($varArray[$j][0]).'"';
	if($varArray[$j][0] == $selected) {
		$selbox .= ' selected="selected"';
	}
	$selbox .= '>'.htmlspecialchars($varArray[$j][0]).'</option>';
		
	}
	$selbox .= '
</select>
';
	return $selbox;
}
function codeReturn($array, $selected){	
	$code = '';
	for($i = 0; $i < count($array); $i++){
		if( strtoupper($selected) == strtoupper($array[$i][0]) || strtoupper($selected) == strtoupper($array[$i][1]) ) {
			$code = array("id" => $array[$i][0], "text" => $array[$i][1]);
			break;
		}
	}
	if(!count($code)){
		$code['error'] = true;
	}	
	return $code;	
}

/* ++++++ User  & Validation Functions  DEPRECATED ++++++ */
function salt_shaker () {
    mt_srand ((double) microtime() * 1000000);
    return substr (crypt(mt_rand(100, 10000000),"Pz"), 3, 2);
}
function makePassword ($pwd = "") {
	// Generate Random Password
	$slt = salt_shaker ();

	if ($pwd) {
		$user['password'] = $pwd;
		$user['enpassword'] = crypt ($user['password'], $slt);
	}
	else {
		$user['password'] = substr (crypt(mt_rand(100, 10000000),$slt), 3, 8);
		$newslt = salt_shaker();
		$user['enpassword'] = crypt ($user['password'], $newslt);
	}

	return $user;
}

/* +++++ System User Functions +++++ */
function printUser($db = '', $user_no = '') {
	$list = '';
	if(!$db) {
		$db = db_connect();
	}
	if(is_numeric($user_no)) {
		$res = execute_sql($db,"SELECT ALL * FROM invoice_users WHERE user_no = ".pg_escape_string($user_no));
	}
	else {
		$res = execute_sql($db,"SELECT ALL * FROM invoice_users ORDER BY access_level ASC, user_no ASC");
	}
	if(pg_num_rows($res) == 1) {
		include("includes/edit.user.detail.php");
	}
	elseif(pg_num_rows($res) > 1) {
		include("includes/edit.user.list.php");
	}
	else {		
		$list .= '<p>No results found</p>';
	}
	return $list;	
}
function manageUser($db='', $variables) {
	global $conf;
	$list = array(); $list['url'] = 'https://'.$_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']).'/user.php?f=edit';	
	if(!is_numeric($variables['user_no'])) {
		$list['error'] = '<p>User number omitted</p>';
	}
	else {
		if(!$db) {
			$db = db_connect();
		}
		if(isset($variables['update'])) {
			// Error Check					
			$error = '';
			if(!strlen(trim(chop($variables['user_name'])))) {
				$error .= "<li>User name omitted</li>\n";
			}
			if(!strlen(trim(chop($variables['user_netname'])))) {
				$error .= "<li>Netname omitted</li>\n";
			}
			else {
				$sql = "SELECT user_no from invoice_users WHERE lower(user_netname) = '".pg_escape_string(trim(chop(strtolower($variables['user_netname']))))."' AND user_no != '".pg_escape_string($variables['user_no'])."'";
				$res = execute_sql($db,$sql);				
				if(pg_num_rows($res)) {
					$error .= "<li>Netname already exists</li>\n";
				}
				else {
					$tmp = getADdata($variables['user_netname']);
					if($tmp['error']) {
						$error .= "<li>".$tmp['msg']."</li>\n";
					}
				}		
			}
			if(!strlen(trim(chop($variables['user_email'])))) {
				$error .= "<li>Email address omitted</li>\n";
			}
			if(! (is_numeric($variables['user_ext']) && (strlen($variables['user_ext']) == 4))) {
				$error .= "<li>Extension must be numeric (4 numbers)</li>\n";
			}
			if(!( is_numeric($variables['access_level']) && ( ($variables['access_level'] >= 0) && ($variables['access_level'] <= 2)) ) ) {
				$error .= "<li>Access level omitted</li>\n";
			}
			if($error) {
				$list['error'] = $error;
				$list['url'] = 'https://'.$_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']).'/user.php?f=detail&user_no=' . $variables['user_no'];
			}
			else {
				$sql = '';
				$sql .= "UPDATE invoice_users set ";
				$sql .= "user_name = '".pg_escape_string(trim(chop($variables['user_name'])))."', ";
				$sql .= "user_netname = '".pg_escape_string(trim(chop($variables['user_netname'])))."', ";
				$sql .= "user_email = '".pg_escape_string(trim(chop($variables['user_email'])))."', ";
				$sql .= "user_ext = ".pg_escape_string($variables['user_ext']).", ";
				$sql .= "access_level = ".pg_escape_string($variables['access_level'])." ";
				$sql .= "WHERE user_no = ".pg_escape_string($variables['user_no']);
				$res = execute_sql($db,$sql);
				$list['title'] = "User updated";
			}
			return $list;			
		}
		elseif($variables['delete']) {
			$sql = "DELETE FROM invoice_users WHERE user_no = '". pg_escape_string($variables['user_no'])."' and user_no != '1'";
			$res = execute_sql($db,$sql);
			if(pg_affected_rows($res) == 0) {
				$list['title'] = "Unable to delete user";
			}
			else {
				$list['title'] = "User deleted";
			}
			
		}
		else {
			$list['error'] = 'Invalid action attempted';
			$list['url'] = $conf['access']['loginUrl'] ."?f=logout";
		}
	}
	return $list;	
}
function validateLDAP($netname = '', $pwd = '') {
	$message = array();
	if(!(strlen(trim(chop($netname))) && strlen(trim(chop($pwd))))) {
		$msg = '';
		if( !strlen($netname) && !strlen($pwd)) {
			$message['msg'] = "No user id";
		}
		elseif(!strlen($pwd)) {
			$message['msg'] = "No password";
		}
		else {
			$message['msg'] = "No user id";
		}	
		$message['error'] = true;
		$message['valid'] = false;			
	}	
	else {
		$server = _AD_URL_;
		$baseDn = _AD_BASE_DN_;
		$cn = 'cn='.trim(chop($netname));		
		$ds = ldap_connect("ldap://$server") or 	
			die("Unable to connect to server $server");	
		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);		
		if(@ldap_bind($ds,"$cn,$baseDn",cleanData($pwd)) ) {
			$data = getADdata(trim(chop($netname)));
			if($data['error'] === true) {
				$message['error'] = true;
				$message['valid'] = false;
				$message['msg'] = " [ Netname data not found ]";
				
			}
			else {				
				$message['error'] = false;
				$message['valid'] = true;
				$message['msg'] = "VALID";
				$message['first name'] = ucwords(strtolower($data['fname']));
				$message['last name'] = ucwords(strtolower($data['lname']));
				$message['name'] = $data['fname'] . ' ' . $data['lname'];
				$message['netname'] = $data['portalid'];				
			}
		}
		else {
			$message['error'] = true;
			$message['valid'] = false;
			$message['msg'] = " [ BAD_USERID_PASSWORD ]";
		}	
	}
	return $message;
}
function validateNetname($netname = '') {
	$server = 'int-con-dc-1.concordia.ca';
	$baseRn = 'CN=iits_portal,OU=Roles,DC=concordia,DC=ca';
	$baseDn = 'OU=People,DC=concordia,DC=ca';
	$baseRnpwd = 'Esp=mc2';
	$message = '';
	$ds = @ldap_connect($server);
	if(!$ds) {
		$message['error'] = true;
		$message['valid'] = false;
		$message['msg'] = "Unable to connect to LDAP server";
	}
	else {
		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);	
		if(ldap_bind($ds, $baseRn, $baseRnpwd)) {
			$filter = "(cn=$netname)";
			$returnValues = array("cn");		
			$r = ldap_search($ds,"$baseDn",$filter,$returnValues);
			$result = ldap_get_entries($ds,$r);		
			if (isset($result[0])) {
				$message['error'] = false;
				$message['valid'] = true;
				$message['msg'] = "Netname Verified";
			}
			else {
				$message['error'] = true;
				$message['valid'] = false;
				$message['msg'] = "Unable to find netname";
			}
		}				
	}
	return $message;
}
function validateAccess($netname = '', $db = '') {
	if(!strlen(trim(chop($netname)))) {
		$user['valid'] = false;
	}
	else {
		$user = '';
		if(!$db) {
			$db = db_connect();
		}
		$res = execute_sql($db,"SELECT ALL * FROM invoice_users WHERE lower(user_netname) = '".pg_escape_string(strtolower(trim(chop($netname))))."'");
		if(pg_num_rows($res) == 1) {
			$user['user']['valid'] = true;
			$user['user']['msg'] = "VALID";
			$user['user']['name'] = pg_fetch_result($res,0, 'user_name');
			$user['user']['access'] = pg_fetch_result($res,0, 'access_level');
			$user['user']['id'] = pg_fetch_result($res,0, 'user_no');
			$user['user']['last_login'] = pg_fetch_result($res,0, 'last_login');
			$user['user']['user_netname'] = pg_fetch_result($res,0, 'user_netname');
			$res = execute_sql($db,"UPDATE invoice_users set last_login = ".mktime()." WHERE user_no = " . $user['user']['id']);
			return $user;
		}
		else {
			$user['user']['valid'] = false;
			$user['user']['msg'] = "User access denied";
		}		
	}
	return $user;		
}

/* +++++ Invoice functions +++++ */
/* +++++ Added flag for gst, pst exempt +++++ */
function calcTotal($inv_total, $gst, $pst, $shipping = '', $pst_exempt = '', $gst_exempt = '', $invdate = '') {
	$tot = array();	
	$tot['before_tax'] = $inv_total;
	
	if($invdate > 0 and $invdate < mktime(23,59,59,12,31,2012)) {
		if(!$gst_exempt) {
			$tot['gst_amt'] = $inv_total * $gst;
		}
		else {
			$tot['gst_amt'] = 0;
		}
		$sub = $inv_total + $tot['gst_amt'];
		if(!$pst_exempt) {
			$tot['pst_amt'] = $sub * $pst;
		}
		else {
			$tot['pst_amt'] = 0;
		}
		$sub = $sub + $tot['pst_amt'];	
		if($shipping){	
			$tot['ship_cost'] = $shipping;
			if(!$gst_exempt) { $tot['ship_gst'] = $shipping * $gst; } else { $tot['ship_gst'] = 0; }
			$sub_ship = $shipping + $tot['ship_gst'];
			if(!$pst_exempt) { $tot['ship_pst'] = $sub_ship * $pst;} else { $tot['ship_pst'] = 0; }
			$sub_ship = $sub_ship + $tot['ship_pst'];	
		}
		else {
			$tot['ship_cost'] = 0.00;
			$tot['ship_gst'] = 0;
			$tot['ship_pst'] = 0;
			$sub_ship = 0.00;
		}	
		$tot['invoice_total'] = $sub + $sub_ship;
	}
	else {
		/* New Tax Calculations */
		
		if(!$gst_exempt) {
			$tot['gst_amt'] = $inv_total * $gst;
		}
		else {
			$tot['gst_amt'] = 0;
		}
		$sub = $inv_total + $tot['gst_amt'];

		if(!$pst_exempt) {
			$tot['pst_amt'] = round(($inv_total * $pst),2);
		}
		else {
			$tot['pst_amt'] = 0;
		}		
		$sub = ($sub + round($tot['pst_amt'],2));
		
				
		if($shipping){	
			$tot['ship_cost'] = $shipping;
			if(!$gst_exempt) { $tot['ship_gst'] = $shipping * $gst; } else { $tot['ship_gst'] = 0; }
			$sub_ship = $shipping + $tot['ship_gst'];
			if(!$pst_exempt) { $tot['ship_pst'] = round( ($shipping * $pst),2) ;} else { $tot['ship_pst'] = 0; }
			$sub_ship = $sub_ship + $tot['ship_pst'];	
		}
		else {
			$tot['ship_cost'] = 0.00;
			$tot['ship_gst'] = 0;
			$tot['ship_pst'] = 0;
			$sub_ship = 0.00;
		}	
		$tot['invoice_total'] = round(($sub + $sub_ship),2);		
	}		
	return $tot;
}

// Makes Org code select box
function makeVarselect($arrayname, $varname = 'org_code', $selected = '') {
	 //echo $selected."<br>\n";
	$selbox = '';
	if(count($arrayname)){
		$selbox .= '
<select name="'.$varname.'" size="1">
	<option value="">Select</option>';	
		for($i = 0; $i < count($arrayname); $i++) {
			$selbox .= '
	<option value="'.$arrayname[$i][0].'"';			
			if(strlen($selected) && ($selected == $arrayname[$i][0])){
				$selbox .= ' selected="selected"';
			}
			$selbox .= '>'.htmlspecialchars($arrayname[$i][1]).'</option>';
		}
		$selbox .= '
</select>
';
	}
	else {
		$selbox .= '<p class="error">Error no codes on file</p>';
	}
	return $selbox;
}
function makeInvoiceselect($db, $varname,$selected = '') {	
	$selbox = '';
	$res = execute_sql($db,"SELECT I1.inv_no, C1.b_comp_name, I1.status FROM invoice_assoc I1, customers C1 WHERE (I1.cust_no = C1.cust_no) ORDER BY I1.inv_no ASC"); 
	//$res = execute_sql($db,"SELECT I1.inv_no, C1.b_comp_name FROM invoice_assoc I1, customers C1 WHERE I1.cust_no = C1.cust_no ORDER BY I1.inv_no ASC"); 
	if(pg_num_rows($res)){
		$selbox .= '
<select name="'.$varname.'" size="1" class="select">
	<option value="">Select</option>';
		for($i = 0; $i < pg_num_rows($res); $i++) {
			$extra = '';
			if(pg_fetch_result($res,$i,'status') == "Paid") {
				$extra = " [PAID]";
			}
			if(pg_fetch_result($res,$i,'status') == "Cancelled") {
				$extra = " [CANCELLED]";
			}
			$selbox .= '
	<option value="'.htmlspecialchars(pg_fetch_result($res,$i,'inv_no')).'">'.htmlspecialchars(pg_fetch_result($res,$i,'inv_no')). ' ' . htmlspecialchars(pg_fetch_result($res,$i,'b_comp_name')).' ' . $extra . '</option>';
		}
	$selbox .= '
</select>
';		
	}
	else {
		$selbox .= "<p>No customers to choose from</p>\n";
	}
	return $selbox;	
}
function makeEditinvoiceselect($db, $varname, $selected = '') {
	global $INVOICE;
	$selbox = '';
	// $res = execute_sql($db,"SELECT I1.inv_no, C1.b_comp_name FROM invoice_assoc I1, customers C1 WHERE I1.status != 'Paid' AND (I1.cust_no = C1.cust_no) ORDER BY I1.inv_no ASC"); 
	
	$sql = '';
	if($INVOICE['user']['access'] == "0") {
		$sql .= "SELECT I1.inv_no, C1.b_comp_name, I1.status FROM invoice_assoc I1, customers C1 WHERE (I1.cust_no = C1.cust_no)";
	}
	else {
		$sql .= "SELECT I1.inv_no, C1.b_comp_name, I1.status FROM invoice_assoc I1, customers C1 WHERE (I1.creator = '".$INVOICE['user']['name']."') AND  (I1.cust_no = C1.cust_no) AND (I1.status != 'Paid')";
	}
	$sql .= " ORDER BY I1.inv_no ASC";
	
	$res = execute_sql($db,$sql); 
	if(pg_num_rows($res)){
		$selbox .= '
<select name="'.$varname.'" size="1" class="select">
	<option value="">Select</option>';
		for($i = 0; $i < pg_num_rows($res); $i++) {
			$extra = '';
			if(pg_fetch_result($res,$i,'status') == "Paid") {
				$extra = " [PAID]";
			}
			if(pg_fetch_result($res,$i,'status') == "Cancelled") {
				$extra = " [Cancelled]";
			}
		
			$selbox .= '
	<option value="'.htmlspecialchars(pg_fetch_result($res,$i,'inv_no')).'">'.htmlspecialchars(pg_fetch_result($res,$i,'inv_no')). ' ' . htmlspecialchars(pg_fetch_result($res,$i,'b_comp_name')) . ' ' . $extra.'</option>';
		}
	$selbox .= '
</select>
';
		
	}
	else {
		$selbox .= "<p>No customers to choose from</p>";
	}
	return $selbox;	
}
function adminPayment($db = '', $invno = '') {
	global $INVOICE;
	$data = array();
	$text = '';
	if(!(strlen($invno) && is_numeric($invno))) {
		$data['error'] = true;
		$data['msg'] = "<li>Invoice number omitted</li>\n";
		$data['url'] = 'https://'.$_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']).'/payment.php';		
	}
	else {
		if(!$db) {
			$db = db_connect();
		}
		$res = execute_sql($db,"SELECT ALL * FROM invoice_assoc WHERE inv_no = '".pg_escape_string($invno)."'");
		if(pg_num_rows($res) != 1) {
			$data['error'] = true;
			$data['msg'] = "<li>Invoice not found</li>\n";
			$data['url'] = 'https://'.$_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']).'/payment.php';
		}
		else {			
			$assoc = pg_fetch_array($res,0); // Assign all invoice assoc data to array	
			$data['inv_no'] = $assoc['inv_no'];	
			$res = execute_sql($db, "SELECT ALL * FROM invoice_detail WHERE inv_no = ".pg_escape_string($assoc['inv_no']));
			if(pg_num_rows($res)) {
				include("includes/payment.instructions.php");
				$inv_total = 0;
				for($i = 0; $i < pg_num_rows($res);$i++){
					$record = pg_fetch_array($res,$i);
					// if(is_numeric($record['qty']) && is_numeric($record['unit_price']) && ($record['qty'] > 0)) {
					if(is_numeric($record['qty']) && is_numeric($record['unit_price']) && ($record['qty'] != 0)) {
						$inv_total = $inv_total + ($record['qty'] * $record['unit_price']);
					}
				}
				$tots = calcTotal($inv_total, $assoc['gst_rate'], $assoc['pst_rate'], $assoc['ship_cost'],null, null, $assoc['inv_date']);
				include("includes/payment.invoice.details.php");
				$res = execute_sql($db,"SELECT ALL * from payment where inv_no = ".pg_escape_string($assoc['inv_no']));				
				include("includes/payment.invoice.payments.php");
				$data['error'] = false;	
			}
			else {
				$data['error'] = true;
				$data['msg'] = "<li>No items found on invoice</li>\n";
				$data['url'] = 'https://'.$_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']).'/payment.php';
			}			
			$data['msg'] = $text;
		}
	}
	return $data;
	
}
function insertPayment($db = '', $varsArray = '', $verifyInv = '', $userName = '', $userIp = '' ){	
	$returnArray = array(); $sql1 = ''; $sql = '';
	if(!(strlen($varsArray['inv_no']) && strlen($verifyInv) && ($varsArray['inv_no'] == $verifyInv)) ) {		
		$returnArray['error'] = true;
		$returnArray['msg'] = "<li>Security violation - Invoice number does not correspond to payment invoice number</li>\n";
		$returnArray['inv_no'] = '';
	}
	else {
		$error = '';		
		if(!( strlen($varsArray['amount_paid']) ||   pg_escape_string($varsArray['cheque_no']) || strlen($varsArray['mop']))) {
			$sql1 .= "UPDATE invoice_assoc set status = '".pg_escape_string($varsArray['status'])."' WHERE inv_no = '".pg_escape_string($varsArray['inv_no'])."'";
		}
		else {
			if(!strlen($varsArray['mop'])) {
				$error .= "<li>Method of payment not selected</li>\n";
			}
			if(strlen($varsArray['amount_paid'])) {
				if(!is_numeric($varsArray['amount_paid'])) {
					$error .= "<li>Amount paid must be numeric</li>\n";
				}
				else {
					$amt = $varsArray['amount_paid'];
				}				
			}
			else {
				$amt = '0.00';
			}
			if(!strlen($varsArray['payment_type'])) {
				$ptype = "Payment";							
			}
			else {
				$ptype = $varsArray['payment_type'];
			}
			
		}		
		if($error) {
			$returnArray['error'] = true;
			$returnArray['msg'] = $error;
			$returnArray['inv_no'] = $verifyInv;
		}
		else {
			if(!$db) {
				$db = db_connect();
			}
			if(strlen($sql1)) {
				$res = execute_sql($db,$sql1);
				$returnArray['error'] = false;
				$returnArray['title'] = 'Invoice Status updated';
				$returnArray['msg'] = 'Status ONLY Updated';
			}
			else {			
				$res = execute_sql($db,"BEGIN");
				$sql = "INSERT INTO payment (inv_no, amount, mop, cheque_no, entered_by, entered_ip, entered_on, payment_type) VALUES (";
				$sql .= pg_escape_string($varsArray['inv_no']).", ";
				$sql .= "'". pg_escape_string($amt)."', ";
				$sql .= "'". pg_escape_string($varsArray['mop'])."', ";
				$sql .= "'". pg_escape_string($varsArray['cheque_no'])."', ";
				$sql .= "'". pg_escape_string($userName)."', ";
				$sql .= "'". pg_escape_string($userIp)."', ";
				$sql .= "'".mktime()."', ";
				$sql .= "'".$ptype."'";
				$sql .= ")";
				$res = execute_sql($db,$sql);
				$sql1 .= "UPDATE invoice_assoc set status = '".pg_escape_string($varsArray['status'])."' WHERE inv_no = '".pg_escape_string($varsArray['inv_no'])."'";
				$res = execute_sql($db,$sql1);
				$res = execute_sql($db,"COMMIT");
				
				$returnArray['title'] = 'Payment record added';
				$returnArray['msg'] = 'New payment inserted<br />Invoice status modified';
				$returnArray['error'] = false;
			}						
		}			
	}
	return $returnArray;
}
function editPaymentformdetail($db = '', $trans_no) {	
	global $conf;
	$returnArray = array();
	if(!is_numeric($trans_no)) {
		$returnArray['error'] = true;
		$returnArray['msg'] = "<li>Transaction number omitted</li>\n";
		$returnArray['trans_no'] = null;
	}
	else {
		if(!$db) {
			$db = db_connect();
		}
		$sql = '';
		$sql .= "SELECT C1.b_comp_name, P1.trans_no, P1.inv_no, P1.amount, P1.mop, P1.cheque_no, P1.entered_by, P1.entered_ip, P1.entered_on, P1.payment_type  FROM payment P1, customers C1, invoice_assoc I1  WHERE (P1.inv_no = I1.inv_no) AND (C1.cust_no = I1.cust_no) AND trans_no = '".pg_escape_string($trans_no)."'";
		$res = execute_sql($db,$sql);
		if(pg_num_rows($res) == 1) {
			include("includes/payment.transaction.detail.form.php");
			$returnArray['error'] = false;
			$returnArray['trans_no'] = pg_fetch_result($res,0,'trans_no');			
			$returnArray['msg'] = $form;
			
		}
		else {
			$returnArray['error'] = true;
			$returnArray['trans_no'] = null;
			$returnArray['msg'] = "<li>Transaction not found</li>\n";
		}
	}
	return $returnArray;
}
function applyeditpayment($db, $variableArray, $compare) {
	if($variableArray['trans_no'] == $compare) {
		if(!$db) {
			$db = db_connect();
		}
		if(!strlen($variableArray['payment_type'])) {
			$ptype = "Payment";							
		}
		else {
			$ptype = $variableArray['payment_type'];
		}
		
		
		$sql = "UPDATE payment set mop = '" . pg_escape_string(trim(chop($variableArray['mop'])))."', cheque_no = '" . pg_escape_string(trim(chop($variableArray['cheque_no']))) . "', payment_type = '" . pg_escape_string(trim(chop($ptype)))."'  WHERE trans_no = '". pg_escape_string(trim(chop($variableArray['trans_no'])))."'";
		$res = execute_sql($db,$sql);
		$returnArray['error'] = false;
		$returnArray['msg'] = "<p>Payment transaction modified</p>\n";
		
	}
	else {
		$returnArray['error'] = true;
		$returnArray['msg'] = "<li>Invalid transaction</li>\n";
	}
	return $returnArray;
}
function statementOfaccount($db = '', $custno = '') {
	// global $INVOICE;
	$data = array();
	$text = '';
	if(!(strlen($custno) && is_numeric($custno))) {
		$data['error'] = true;
		$data['msg'] = "<p class=\"error\">Customer not selected</p>\n";	
	}
	else {		
		if(!$db) {
			$db = db_connect();
		}
		$sql = '';
		$sql = "SELECT invoice_assoc.inv_no, invoice_assoc.gst_rate, invoice_assoc.pst_rate, invoice_assoc.ship_cost, invoice_assoc.inv_date, invoice_assoc.inv_sent_on, invoice_assoc.creator, customers.b_comp_name FROM invoice_assoc, customers WHERE invoice_assoc.cust_no = ".pg_escape_string($custno)." AND (invoice_assoc.cust_no = customers.cust_no)";
		$res = execute_sql($db,$sql);		
		if(!pg_num_rows($res)) {
			$data['error'] = true;
			$data['msg'] = "<p class=\"error\">Invoices not found</p>\n";			
		}
		else {
			$balanceOwing = 0;		
			$statementArray = array();
			// query by invoice to determine totals	
			for($i = 0; $i < pg_num_rows($res); $i++) { // For each invoice for a client				
				$invoiceNumber = '';					
				$invoiceNumber = pg_fetch_result($res,$i,'inv_no');
				$tot = '';				
				$tot = "SELECT inv_no, SUM(qty * unit_price) AS subtotal, count(line_no) AS numlines FROM invoice_detail WHERE inv_no = ".pg_escape_string($invoiceNumber)." GROUP BY inv_no";
				$res1 = execute_sql($db,$tot);
				$invtotal = 0;
				$invtotal = calcTotal(pg_fetch_result($res1,0,'subtotal'), pg_fetch_result($res,$i,'gst_rate'), pg_fetch_result($res,$i,'pst_rate'), pg_fetch_result($res,$i,'ship_cost'), null, null, pg_fetch_result($res,$i,'inv_date') );
				// Verify if payments  to invoice 
				$res2 = execute_sql($db,"SELECT ALL * FROM payment WHERE inv_no = ".$invoiceNumber);
				if(pg_num_rows($res2)) {
					// Payments per invoice					
					$paidAmount = 0;
					$pmt = array();
					for($j = 0; $j < pg_num_rows($res2); $j++) {
						$p = pg_fetch_array($res2,$j);
						$pmt[] = array(
									"transaction"		=> $p['trans_no'],
									"amount"			=> $p['amount'],
									"method_of_payment" => $p['mop'],
									"cheque_no"			=> $p['cheque_no'],
									"entered_by"		=> $p['entered_by'],
									"entered_on"		=> $p['entered_on'], 
									"payment_type"		=> $p['payment_type'], 
								);
						$paidAmount +=  $p['amount'];						
					}					
				}
				else {
					$paidAmount = 0;
					$pmt = "No payments made";
				}
				$bal = 0;
				$bal = round($invtotal['invoice_total'],2) - round($paidAmount,2);
				
				$statementArray[] = array(
										"inv_no"		=> pg_fetch_result($res,$i,'inv_no'), 
										"inv_date"		=> pg_fetch_result($res,$i,'inv_date'), 
										"inv_sent_on"	=> pg_fetch_result($res,$i,'inv_sent_on'), 
										"creator"		=> pg_fetch_result($res,$i,'creator'), 
										"invoice_total" => round($invtotal['invoice_total'], 2),
										"payment_total"	=> $paidAmount,										
										"balance"		=> round($bal,2),
										"total" 		=> $invtotal,
										"payments" 		=> $pmt,
										"custname" 		=> pg_fetch_result($res,0,'b_comp_name')							
									);
				$paidAmount = 0;
				$balanceOwing += round($bal,2);
				
			}
			$counter = 1;
			$inv = '';
			$text .= '
<h3>'.pg_fetch_result($res,0,'b_comp_name').'</h3>
<table border="1" cellspacing="0" cellpadding="5" id="statement">
	<tr valign="top">
		<td>Line</td>
		<td>Invoice</td>
		<td>Payment Type</td>
		<td>Date</td>
		<td>Created by</td>
		<td>Amount</td>
		<td>Payment</td>
		<td>Balance</td>
	</tr>';
			for($i = 0; $i < count($statementArray); $i++) {
				$text .= '
	<tr valign="top">
		<td>'  .$counter . '</td>
		<td><a href="'. $_SERVER['PHP_SELF'] .'?f=detail&amp;inv_no='.urlencode($statementArray[$i]['inv_no']).'">' . $statementArray[$i]['inv_no'] . '</a></td>
		<td>&nbsp;</td>
		<td>' . date("d-m-Y", $statementArray[$i]['inv_date']) .'</td>
		<td>' . htmlspecialchars($statementArray[$i]['creator']) .'</td>
		<td class="right">$' . number_format(htmlspecialchars($statementArray[$i]['invoice_total']),2,'.',',') . '</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>';
				$counter++;
				if(is_array($statementArray[$i]['payments'])) {
					for($j = 0; $j < count($statementArray[$i]['payments']); $j++) {
						$text .= '
	<tr valign="top">
		<td>'.$counter.'</td>
		<td>&nbsp;</td>
		<td>' . htmlspecialchars($statementArray[$i]['payments'][$j]['payment_type']) .'</td>
		<td>' . date("d-m-Y", $statementArray[$i]['payments'][$j]['entered_on']) .'</td>
		<td>' . htmlspecialchars($statementArray[$i]['payments'][$j]['entered_by']) .'</td>
		<td>&nbsp;</td>
		<td class="right">$';
		($statementArray[$i]['payments'][$j]['amount'] > 0 ) ? $text .= "(". number_format(htmlspecialchars(-1 * $statementArray[$i]['payments'][$j]['amount']), 2,'.','') .")" : $text .= number_format(htmlspecialchars(-1 * $statementArray[$i]['payments'][$j]['amount']),2,'.', ',');
		$text .= '</td>
		<td>&nbsp;</td>	
	</tr>';
						$counter++;
					}
					$text .= '
	<tr valign="top">
		<td>'.$counter.'</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td class="right">$' . 
		number_format($statementArray[$i]['balance'], 2,'.',',').'</td>	
	</tr>';
					
					$counter++;
				}
				else {
					$text .= '
	<tr valign="top">
		<td>'.$counter.'</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td class="right">$' . number_format($statementArray[$i]['balance'],2,'.',',').'</td>	
	</tr>';
				}
				
			}
			$text .= '
	<tr valign="top">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>Balance</td>
		<td class="right">$' . number_format($balanceOwing,2,'.',',').'</td>	
	</tr>
</table>
';			
			$data['msg'] = $text;
			$data['allresults'] = $statementArray;
			$data['error'] = false;		
		}
	}
	return $data;	
}
function showStatement($db = '', $invno = '') {
	global $INVOICE;
	$data = array();
	$data['msg'] = '';
	$text = ''; 
	if(!(strlen($invno) && is_numeric($invno))) {
		$data['error'] = true;
		$data['msg'] = "<li>Invoice number omitted</li>\n";
		$data['url'] = 'https://'.$_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']).'/statement.php';		
	}
	else {
		if(!$db) {
			$db = db_connect();
		}
		$res = execute_sql($db,"SELECT ALL * FROM invoice_assoc, customers WHERE inv_no = ".pg_escape_string($invno) ." AND (invoice_assoc.cust_no = customers.cust_no)");
		if(pg_num_rows($res) != 1) {
			$data['error'] = true;
			$data['msg'] = "<li>Invoice not found</li>\n";
			$data['url'] = 'https://'.$_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']).'/statement.php';
		}
		else {			
			$assoc = pg_fetch_array($res,0); // Assign all invoice assoc data to array	
			$data['inv_no'] = $assoc['inv_no'];
			$res = execute_sql($db, "SELECT ALL * FROM invoice_detail WHERE inv_no = ".pg_escape_string($assoc['inv_no']));
			if(pg_num_rows($res)) {				
				$inv_total = 0;
				for($i = 0; $i < pg_num_rows($res);$i++){
					$record = pg_fetch_array($res,$i);
					if(is_numeric($record['qty']) && is_numeric($record['unit_price']) && ($record['qty'] != 0)) {
						$inv_total = $inv_total + ($record['qty'] * $record['unit_price']);
					}
				}
				
				$tots = calcTotal($inv_total, $assoc['gst_rate'], $assoc['pst_rate'], $assoc['ship_cost'], null, null, $assoc['inv_date']);
				
				
				
				include("includes/statement.invoice.details.php");
				$res = execute_sql($db,"SELECT ALL * from payment WHERE inv_no = ".pg_escape_string($assoc['inv_no']));				
				include("includes/statement.invoice.payments.php");			
			}
			else {
				$data['error'] = true;
				$data['msg'] = "<li>No items found on invoice</li>\n";
				$data['url'] = 'https://'.$_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']).'/payment.php';
			}			
			$data['msg'] .= $text;
		}
	}
	return $data;
	
}
function updateSendinvoice($db, $invno = '') {
	$data = '';
	$sql = '';
	if(!(strlen($invno) && is_numeric($invno))) {
		$data['error'] = true;
		$data['msg'] = "Invoice number omitted";
		$data['title'] = "IITS Invoice application - [Error]";
	}
	else {
		if(!$db) {
			$db = db_connect();
		}
		$sql = "SELECT inv_no, revision_num from invoice_assoc WHERE inv_no = '".pg_escape_string($invno)."'"; // Determine current revision
		$res = execute_sql($db,$sql);
		if(pg_num_rows($res) != 1 ) {
			$data['error'] = true;
			$data['msg'] = "Invoice not found";
			$data['title'] = "IITS Invoice application - [Error]";
		}
		else {
			$revno = pg_fetch_result($res,0,'revision_num');
			if($revno == "0" || is_null($revno)) {
				$revno = "NULL";
			}
			$sql = '';
			$sql .= "UPDATE invoice_assoc SET inv_rev_sent = ".pg_escape_string($revno).", inv_sent_on = '" . mktime()."', invoice_sent_flag = true, updated_by = '".pg_escape_string($_SESSION['INVOICE']['user']['name'])."', updated_on = '".mktime()."', updated_ip = '". returnRemoteaddr() ."' WHERE inv_no = ".pg_escape_string($invno)." AND (upper(status) != 'PAID')";
			
			$res = execute_sql($db,$sql);			
			if(pg_affected_rows($res) == "0" ) {
				$data['error'] = true;
				$data['msg'] = "Invoice already Paid";
				$data['title'] = "IITS Invoice application - [Error]";
			}
			else {
				$data['error'] = false;
				$data['msg'] = "Invoice sent date has been updated<br /><br />\n<a href=\"print.php?f=print&amp;inv_no=".urlencode($invno)."\" target=\"printinvoice\">Print Invoice?</a>\n";
				$data['title'] = "IITS Invoice application - [Update Successful]";
			}
		}		
	}
	return $data;
}
function printCustomers($db = '', $cust_no = '') {
	$list = '';
	if(!$db) {
		$db = db_connect();
	}
	if(is_numeric($cust_no)) {
		$res = execute_sql($db,"SELECT ALL * FROM customers WHERE cust_no = ".pg_escape_string($cust_no));
	}
	else {
		$res = execute_sql($db,"SELECT cust_no, b_comp_name FROM customers ORDER BY sort_b_comp_name ASC");
	}
	if(pg_num_rows($res) == 1) {
		
		include("includes/edit.customer.detail.php");
	}
	elseif(pg_num_rows($res) > 1) {
		
		include("includes/edit.customer.list.php");
	}
	else {		
		$list .= '<p>No results found</p>';
	}
	return $list;
}
function makeCustomerselect($db){
	$res = execute_sql($db,"SELECT cust_no, b_comp_name, sort_b_comp_name  FROM customers ORDER BY  sort_b_comp_name ASC");
	if(pg_num_rows($res)){
		$selbox = '';
		$selbox .= '
<select name="cust_no" size="1">
	<option value="">Select customer</option>';
	for($i = 0; $i < pg_num_rows($res); $i++) {
		$selbox .= '
	<option value="'.htmlspecialchars(pg_fetch_result($res,$i,'cust_no')).'">'.htmlspecialchars(pg_fetch_result($res,$i,'b_comp_name')).'</option>';
}
$selbox .= '
</select>
';
	}
	else {
		$selbox .= "<p>No customers to choose from</p>\n";
	}
return $selbox;
}
function addCustomer($db = '',$variables) {
	$list = array(); $error = (string) null;
	if(!(strlen(trim(chop($variables['b_comp_name']))) && strlen(trim(chop($variables['b_contact']))))) {
		if(!strlen(trim(chop($variables['b_comp_name'])))) {
			$error .= "<li>Billing company name omitted</li>\n";
		}
		if(!strlen(trim(chop($variables['b_contact'])))) {
			$error .= "<li>Billing contact omitted</li>\n";
		}		
	}
	if(isset($variables['b_email']) and !is_email($variables['b_email'])) {
		$error .= '<li>Invalid Email Format.</li>';
	}	
	if($error) {
		$list['error'] = $error;
	}
	else {
		if(!$db) {
			$db = db_connect();
		}
		$res = execute_sql($db,"SELECT cust_no from customers WHERE sort_b_comp_name = '". pg_escape_string(char_Replace($variables['b_comp_name']))."'");
		if(pg_num_rows($res)) {
			$error .= "<li>Customer aleady exists</li>\n";
		}
		else {
			$sql = "INSERT INTO customers (	b_comp_name, b_contact, b_address, b_suite, b_city, b_province, b_country, b_postal, b_tel, b_fax, b_email, s_same, s_comp_name, 
			s_contact, s_address, s_suite, s_city, s_province, s_country, s_postal, s_tel, s_fax, created_by, created_on, sort_b_comp_name ) VALUES (";
					
			$sql .= "'". pg_escape_string(trim(chop($variables['b_comp_name'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['b_contact'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['b_address'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['b_suite'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['b_city'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['b_province'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['b_country'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['b_postal'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['b_tel'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['b_fax'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['b_email'])))."', ";			
			// $variables['s_same'] == "y" ? $sql .= "true, " : $sql .= "false, ";
			
			if(	strlen(trim(chop($variables['s_comp_name']))) or strlen(trim(chop($variables['s_contact']))) or strlen(trim(chop($variables['s_address']))) or strlen(trim(chop($variables['s_suite']))) ) {
				$sql .= "false, ";
			}
			else {
				$sql .= "true, ";
			}			
			$sql .= "'". pg_escape_string(trim(chop($variables['s_comp_name'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['s_contact'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['s_address'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['s_suite'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['s_city'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['s_province'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['s_country'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['s_postal'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['s_tel'])))."', ";
			$sql .= "'". pg_escape_string(trim(chop($variables['s_fax'])))."', ";
			$sql .= "'".pg_escape_string($_SESSION['INVOICE']['user']['name'])."', ";
			$sql .= mktime().", ";
			$sql .= "'".pg_escape_string(char_Replace($variables['b_comp_name']))."'";
			$sql .= ")";
			$res = execute_sql($db,$sql);
			$list['title'] = "New customer entered";
			$list['error'] = false;		
		}
	}
	return $list;	
}
function manageCustomer($db = '', $variables) {
	global $INVOICE;
	$list = array();
	if(!is_numeric($variables['cust_no'])) {
		$list['error'] = '<p>Customer number omitted</p>';
		$list['url'] = 'https://'.$_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']).'/customer.php?f';
	}
	else {
		if(!$db) {
			$db = db_connect();
		}		
		if(isset($variables['update'])) {			
			// Error Check					
			$error = '';
			if(!strlen(trim(chop($variables['b_comp_name'])))) {
				$error .= "<li>Bill to company name omitted</li>\n";
			}
			if(!strlen(trim(chop($variables['b_contact'])))) {
				$error .= "<li>Bill to contact omitted</li>\n";
			}
			if($error) {
				$list['error'] = $error;				
				$list['url'] = 'https://'.$_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']).'/customer.php?f=detail&cust_no=' . $variables['cust_no'];
			}
			else {
				$sql = '';
				$sql .= "UPDATE customers set ";
				$sql .= "b_comp_name = '".pg_escape_string(trim(chop($variables['b_comp_name'])))."', ";
				$sql .= "b_contact = '".pg_escape_string(trim(chop($variables['b_contact'])))."', "; 
				$sql .= "b_address = '".pg_escape_string(trim(chop($variables['b_address'])))."', "; 
				$sql .= "b_suite = '".pg_escape_string(trim(chop($variables['b_suite'])))."', "; 
				$sql .= "b_city = '".pg_escape_string(trim(chop($variables['b_city'])))."', "; 
				$sql .= "b_province = '".pg_escape_string(trim(chop($variables['b_province'])))."', "; 
				$sql .= "b_country = '".pg_escape_string(trim(chop($variables['b_country'])))."', "; 
				$sql .= "b_postal = '".pg_escape_string(trim(chop($variables['b_postal'])))."', "; 
				$sql .= "b_tel = '".pg_escape_string(trim(chop($variables['b_tel'])))."', "; 
				$sql .= "b_fax = '".pg_escape_string(trim(chop($variables['b_fax'])))."', "; 
				$sql .= "b_email = '".pg_escape_string(trim(chop($variables['b_email'])))."', ";				
				if(	strlen(trim(chop($variables['s_comp_name']))) or strlen(trim(chop($variables['s_contact']))) or strlen(trim(chop($variables['s_address']))) or strlen(trim(chop($variables['s_suite']))) or strlen(trim(chop($variables['s_city']))) or strlen(trim(chop($variables['s_province']))) or strlen(trim(chop($variables['s_country']))) ) {
					$sql .= "s_same = false, ";												
					$sql .= "s_comp_name = '".pg_escape_string(trim(chop($variables['s_comp_name'])))."', ";
					$sql .= "s_contact = '".pg_escape_string(trim(chop($variables['s_contact'])))."', ";
					$sql .= "s_address = '".pg_escape_string(trim(chop($variables['s_address'])))."', ";
					$sql .= "s_suite = '".pg_escape_string(trim(chop($variables['s_suite'])))."', ";
				}
				else {
					$sql .= "s_same = true, ";
					$sql .= "s_comp_name = '', ";
					$sql .= "s_contact = '', ";
					$sql .= "s_address = '', ";
					$sql .= "s_suite = '', ";
				}
				$sql .= "s_city = '".pg_escape_string(trim(chop($variables['s_city'])))."', ";
				$sql .= "s_province = '".pg_escape_string(trim(chop($variables['s_province'])))."', ";
				$sql .= "s_country = '".pg_escape_string(trim(chop($variables['s_country'])))."', ";
				$sql .= "s_postal = '".pg_escape_string(trim(chop($variables['s_postal'])))."', ";
				$sql .= "s_tel = '".pg_escape_string(trim(chop($variables['s_tel'])))."', ";
				$sql .= "s_fax = '".pg_escape_string(trim(chop($variables['s_fax'])))."', ";				
				$sql .= "updated_by = '".pg_escape_string($_SESSION['INVOICE']['user']['name'])."', ";
				$sql .= "updated_on = '".mktime()."', ";
				$sql .= "sort_b_comp_name = '".pg_escape_string(char_Replace($variables['b_comp_name']))."' ";				
				$sql .= "WHERE cust_no = '".pg_escape_string($variables['cust_no'])."'";
				$list['sql'] = $sql;
				$res = execute_sql($db,$sql);
				$list['title'] = "Customer updated";
				$list['url'] = '';
			}
			return $list;			
		}
		elseif($variables['delete']) {			
			$res = execute_sql($db,"SELECT inv_no from invoice_assoc where cust_no = ".pg_escape_string($variables['cust_no']));
			if(pg_num_rows($res)) {
				$error = "Unable to delete customer, " . pg_num_rows($res) ." invoice";
				pg_num_rows($res) > 1 ? $error .= "s exist" : $error .= " exists";				
				$list['error'] = $error;
				$list['url'] = 'https://'.$_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']).'/customer.php?f=detail&cust_no=' . $variables['cust_no'].'&blah=no';
			}
			else {
				$res = execute_sql($db,"DELETE FROM customers WHERE cust_no = ". pg_escape_string($variables['cust_no']));
			}
			$list['title'] = "Customer deleted";
			$list['error'] = false;
			$list['url'] = '';	
		}
		else {
			$list['error'] = 'Invalid action attempted';
			$list['url'] = 'https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']).'customer.php?f=detail&cust_no=' . $variables['cust_no'] . '&blah=yes';
		}
	}
	return $list;	
}
function verifyDescriptionlength($string = '') {
	return (strlen($string) > 50) ? true: false;		
}
function updateTaxstatus($variableArray, $compare, $db) {
	global $conf;
	$list = '';
	if($compare != $variableArray['inv_no']) {
		$list['error'] = true;
		$list['msg'] = "Invalid Invoice selected";
	}
	else {
		$sql = '';
		$sql .= 'UPDATE invoice_assoc SET';
		if($variableArray['gst_exempt'] == "true") {
			$sql .= " gst_rate = '0.00'";
		}
		else {
			$sql .= " gst_rate = '".$conf['rate']['gst']."'";
		}
		if($variableArray['pst_exempt'] == "true") {
			$sql .= ", pst_rate = '0.00'";
		}
		else {
			$sql .= ", pst_rate = '".$conf['rate']['pst']."'";
		}
		$sql .= ' WHERE inv_no = '.pg_escape_string($variableArray['inv_no']).' AND (lower(status) != \'paid\' AND lower(status) != \'cancelled\')';
		if(!$db) {
			$db = db_connect();
		}
		$res = execute_sql($db,$sql);
		$list['error'] = false;
		$list ['msg'] = 'Invoice tax exempt status modified as requested';
	}
	return $list;
}
function returnAccesstext ($access = '') {	
	switch ($access) {
		case 0:
		   return "Owner";
		   break;
		case 1:
		   return "Administer payments";
		   break;
		case 2:
		  return "Create Invoices";
		   break;
		default:
			return "not a user";
			break;
	}	
}
function returnVar($varname) {
	$parma = (string) null;
	if(isset($_POST[$varname])) {
		$param = $_POST[$varname];
	}
	elseif(isset($_GET[$varname])) {
		$param = $_GET[$varname];
	}
	else {
		$param = null;
	}
	return $param;
}
function returnRemoteaddr() {
	if(isset($_SERVER['HTTP_REMOTE_ADDR'])) { return $_SERVER['HTTP_REMOTE_ADDR']; }
	return $_SERVER['REMOTE_ADDR'];
}
function getADdata($netname = '') {
	$data = array();
	if(!(strlen(trim(chop($netname))))) {
		$data['error'] = true;
		$data['msg'] = 'Netname not provided';
	}	
	else {
		$server = _AD_URL_;
		$baseDn = _AD_BASE_DN_;
		$baseRn = _AD_SEARCH_BIND_RDN_;
		$baseRnpwd = _AD_SEARCH_BIND_PWD_;
		$ds = ldap_connect("ldap://$server") or 	
			die("Unable to connect to server $server");	
		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
		if(ldap_bind($ds, $baseRn, $baseRnpwd) ) {
			$filter = "(cn=$netname)";
			$returnValues = array("cn", "sn", "givenname", "displayname", "department", "concordiastudentid", "concordiaemployeeid", "concamsid", "memberof", "title", "telephonenumber", "physicaldeliveryofficename");		
			$r = ldap_search($ds,"$baseDn",$filter,$returnValues);	
			//$r = ldap_search($ds,"$baseDn",$filter);			
			$result = ldap_get_entries($ds,$r);						
			if($result['count'] == 1) {				
				// Have a valid user
				$data['error'] = false;					
				$data['portalid'] = trim(chop($result[0]['cn'][0]));
				$data['nname'] = trim(chop($result[0]['cn'][0]));
				$data['fname'] = trim(chop($result[0]['givenname'][0]));
				$data['lname'] = trim(chop($result[0]['sn'][0]));
				isset($result[0]['displayname'][0]) ? $data['displayname'] = trim(chop($result[0]['displayname'][0])) : $data['displayname'] = null;
				isset($result[0]['telephonenumber'][0]) ? $data['telephone'] = trim(chop($result[0]['telephonenumber'][0])) : $data['telephone'] = null;
				isset($result[0]['concordiaemployeeid'][0]) ? $data['employee_id'] = trim(chop($result[0]['concordiaemployeeid'][0])): $data['employee_id'] = null;
				isset($result[0]['concordiastudentid'][0]) ? $data['student_id'] = trim(chop($result[0]['concordiastudentid'][0])): $data['student_id'] = null;							
				$data['customer_id'] = trim(chop($result[0]['concamsid'][0]));
				isset($result[0]['title'][0]) ? $data['title'] = $result[0]['title'][0]:$data['title'] = null ;
				isset($result[0]['department'][0]) ? $data['dept'] = $result[0]['department'][0]:$data['dept'] = null ;
				$data['msg'] = null;					
			}
			else {				
				$data['error'] = true;
				$data['msg'] = 'Netname not found';
			}					
		}
		else {				
			$data['error'] = true;
			$data['msg'] = 'Invalid netname';
		}	
	}
	return $data;
}
?>