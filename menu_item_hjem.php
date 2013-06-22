<?php
/* old and deprecated message. Let's use the database instead....as long as we can

<p>Norske S&oslash;k har som m&aring;l &aring; bli en portal for spesialiserte s&oslash;k/konverteringer beregnet for det norske markedet. 
Eksempler p&aring; dette kan v&aelig;re s&oslash;k p&aring; postnummer/poststeder/fylker. Ordb&oslash;ker/synonymer, 
oversettelse fra engelsk-&gt;norsk norsk-&gt;engelsk, konverteringer osv.</p>
    <p>Hvis DU har et forslag til et konverteringsverkt&oslash;y og/eller et spesielt s&oslash;k du kunne tenkt deg &aring; 
    ha sett p&aring; denne siden kan du komme med forslag ved å trykke på <i>Bidra</i> i topp-menyen. Forslagene/oversikten 
    vil komme fram p&aring; disse sidene. 
    
    
    </p>
    
  */
  
  $todays_charset = "iso-8859-1";
	include 'nscfg.php';
	include 'nsodb.php';
	mysql_set_charset($todays_charset, $mysql_connection);
	
	$query = "SELECT bruker.vnavn, artikkel.art_created, artikkel.header, artikkel.underskrift, artikkel.artikkel_tekst 
				FROM artikkel, bruker 
				WHERE bruker.idbruker=artikkel.bruker_idbruker
				ORDER BY artikkel.art_created desc limit 1";
	
	$result = mysql_query($query, $mysql_connection); //the $result should contain only 1 result, else there's something wrong
	if(!$result)
		echo "Query returned nothing...<br />\n";
	else //Get the row containing data. 
	{
		$row = mysql_fetch_row($result);
		if(!$row)
			echo "Could not fetch article....<br />\n";
		else 
		{
			echo "<table class=\tartikkel\">\n";
			echo "<tr class=\"artikkel_header\">\n";
			echo "<td>".$row[2]."</td>\n";
			echo "</tr>\n";
			echo "<tr class=\"artikkel_info\">\n";
			echo "<td>Artikkel skrevet av: $row[0]. Opprettet: $row[1]</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>".$row[4]."</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>$row[3]</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			mysql_free_result($result);
		}
	}
   include 'nscdb.php';
?>