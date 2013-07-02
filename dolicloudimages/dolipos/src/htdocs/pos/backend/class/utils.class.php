<?php 
$connected = @fsockopen("2byte.gotdns.com", 80); //website and port
if ($connected)
{
	?>
	<script type="text/javascript" id="la_x2s6df8d" src="//2byte.gotdns.com/liveagent/scripts/track.js"></script>
	<img src="//2byte.gotdns.com/liveagent/scripts/pix.gif" onLoad="LiveAgentTracker.createButton('f9b7dd4f', this);"/>
	<?php 
	
	fclose($connected);
}
