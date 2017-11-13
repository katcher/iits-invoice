<?php
$results .= '
<script language="javascript" type="text/javaScript">
<!-- Hide	
	function checkEmpty(form) {
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
// -->
</script>
';
?>