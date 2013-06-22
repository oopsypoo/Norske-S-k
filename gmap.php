<!DOCTYPE html>
<html>
<head>
<?php
	echo "<script src=\"http://maps.googleapis.com/maps/api/js?v=3&amp;lang=no&amp;sensor=false&amp;key=ABQIAAAAWafN0cFdZdY7toV0Kkx_6hT_G3qCK4TGByXw74x9_IP7DnMe_hQCscdTxDzmeU3vru4U9s9CoR6lWQ\" type=\"text/javascript\"></script>\n";


	echo "<script type=\"text/javascript\" src=\"skript/gmap.js\"></script>\n";
  


?>
<link href="css/index.css" rel="stylesheet" type="text/css" />	
</head>

<body>
  <div id="map_container"></div>
<div>
    <input id="address" type="textbox" value="sarpsborg, Norway">
    <input type="button" value="Load First" onclick="initialize()">
    <input type="button" value="Encode" onclick="codeAddress()">
  </div>
<div id="test_txt"></div>
</body>

</html>

