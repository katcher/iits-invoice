function checkEmpty(form) {
	if (form.cust_no.options[form.cust_no.selectedIndex].value == '') {
		alert('Select a Customer.');
		form.cust_no.focus();
		return false;
	}		
	else {
		return true;
	}
}

function checkEditempty(form) {
	var errmsg = "";		
	var flag = false;
	var valid = false;
		
	for(i = 0; i < form.action.length; i++) {
		if(form.action[i].checked) {
			valid = true;
			break;
		}			
	}
	if(!valid) {
		errmsg += "Action not selected\n";
		flag = true;
	}		
	if (form.inv_no.options[form.inv_no.selectedIndex].value == "") {
		errmsg += "Select an Invoice\n";
		flag = true;			
	}				
	if(flag) {
		alert("Errors encountered\n"+errmsg);
		return false;
	}					
	else {
		return true;
	}
}

function cInvoice(form) {
	if (form.inv_no.options[form.inv_no.selectedIndex].value == '') {
		alert('Select an Invoice.');
		form.inv_no.focus();
		return false;
	}		
	else {
		return true;
	}
}

