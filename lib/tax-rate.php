<?php
/* Tax Rate Definition */

/* GST Rate */
if( mktime() > mktime(23,59,59,12,31,2007)) {
	$conf['rate']['gst'] = .05; // changed Jan 1, 2008 by Eric
}
else {
	$conf['rate']['gst'] = .06; // changed June 30, 2006 by Eric
}



/* PST Rate */
if( mktime() > mktime(23,59,59,12,31,2012) ) {
	$conf['rate']['pst'] = .09975; // changed Jan 3, 2013 by Eric
}
elseif( mktime() > mktime(23,59,59,12,10,2011) ) {
	$conf['rate']['pst'] = .095; // changed Dec 14, 2011 by Eric
}
elseif( mktime() > mktime(23,59,59,12,31,2010) ) {
	$conf['rate']['pst'] = .085; // changed Dec 20, 2010 by Eric
}
else {
	$conf['rate']['pst'] = .075; // changed June 30, 2006 by Eric
}