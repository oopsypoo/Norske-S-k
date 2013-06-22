<?php
	
	/*	Trenger disse css-klassene og ideene
	div id= 'yr-varsel'  --- yr-container(main)
	class 'v'	--- brukt i tabeller th og td
	class 'pluss' --- brukt i tabell til td temperatur
	class 'skilje' --- egen 'tr' med colspan = 7, synlig border(top eller bottom) for å skille dag-data.
	*/
	if(!isset($valgt_fylke))
		$valgt_fylke = 'østfold';
	if(!isset($valgt_kommune))
		$valgt_kommune = 'sarpsborg';
	if(!isset($valgt_poststed))
		$valgt_poststed = 'greåker';
/*	echo "Fylke: ".$valgt_fylke."<br />\n";
	echo "Kommmune: ".$valgt_kommune."<br />\n";
	echo "Poststed: ".$valgt_poststed."<br />\n";
*/
	global $yr_url;
	$part_url ='http://www.yr.no/sted/Norge';
	
	$yr_url = $part_url."/".$valgt_fylke."/".$valgt_kommune."/".$valgt_poststed; 
	//$yr_url = 'http://www.yr.no/sted/Norge/Østfold/Sarpsborg/Sarpsborg';
	include_once('skript/yr.php');
	
	

?>