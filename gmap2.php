
<!DOCTYPE html>
<html>
<script src="http://maps.googleapis.com/maps/api/js?v=3&amp;sensor=false&amp;key=ABQIAAAAWafN0cFdZdY7toV0Kkx_6hT_G3qCK4TGByXw74x9_IP7DnMe_hQCscdTxDzmeU3vru4U9s9CoR6lWQ" type="text/javascript"></script>
<script type='text/javascript' src='http://www.bradwedell.com/libs/MarkerClusterer-1.0/src/markerclusterer_compiled.js' ></script>

<?php

include_once("skript/GoogleMap.php");
include_once("skript/JSMin.php");
$MAP_OBJECT = new GoogleMapAPI(); 
$MAP_OBJECT->_minify_js = isset($_REQUEST["min"])?FALSE:TRUE;
$MAP_OBJECT->setDSN("mysql://gmap:un3versal@10.0.0.112/posten");

//Code to generate markers, taken from http://www.bradwedell.com/phpgooglemapapi/demos/advanced_icons.php
$marker_web_location = "http://www.bradwedell.com/phpgooglemapapi/demos/img/";
$default_icon = $marker_web_location."triangle_icon.png";
$blue_icon = $marker_web_location."blue_triangle_icon.png";
$green_icon = $marker_web_location."green_triangle_icon.png";
$yellow_icon = $marker_web_location."yellow_triangle_icon.png";
$default_icon_key = $MAP_OBJECT->setMarkerIcon($default_icon);
$blue_icon_key = $MAP_OBJECT->addIcon($blue_icon,$shadow="", 11, 11, 11, 11);
$green_icon_key = $MAP_OBJECT->addIcon($green_icon);
$yellow_icon_key = $MAP_OBJECT->addIcon($yellow_icon);
$default_marker_1 = $MAP_OBJECT->addMarkerByAddress("Denver,CO","Default Marker1","Default Marker1");
$default_marker_2 = $MAP_OBJECT->addMarkerByAddress("Littleton,CO","Default Marker2","This is default marker 2 that was originally initialized with the 'custom' default icon and was changed to a different icon after the marker was already created.");
$blue_marker = $MAP_OBJECT->addMarkerByAddress("Boulder,CO","Blue Marker","Blue Marker",$tooltip="", $blue_icon);
$green_marker = $MAP_OBJECT->addMarkerByAddress("Arvada,CO","Green Marker","Green Marker",$tooltip="", $green_icon);
$yellow_marker = $MAP_OBJECT->addMarkerByAddress("Lakewood,CO","yellow Marker","yellow Marker",$tooltip="", $yellow_icon);
$MAP_OBJECT->updateMarkerIconKey($default_marker_2, $blue_icon_key);

//Add a marker in a different state so that clustering becomes apparent
$MAP_OBJECT->addMarkerByAddress("Los Angeles, CA","Marker in LA");

//Enable Marker Clustering
$MAP_OBJECT->enableClustering();
//Set options (passing nothing to set defaults, just demonstrating usage
$MAP_OBJECT->setClusterOptions();
//Set MarkerCluster library location
$MAP_OBJECT->setClusterLocation("http://www.bradwedell.com/libs/MarkerClusterer-1.0/src/markerclusterer_compiled.js");


?>
<head>


<?php

		$MAP_OBJECT->getHeaderJS();
		$MAP_OBJECT->getMapJS();
		
?>






</head>

<body>


<?php
 
		$MAP_OBJECT->printOnLoad();
		$MAP_OBJECT->printMap();
		$MAP_OBJECT->printSidebar();

?>

</body>

</html>

