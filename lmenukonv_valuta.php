<p>Velg et land for &aring; finne ut hva kursen er p&aring; valgte valuta. Verdiene er hentet fra Norges Bank og blir oppdatert ca kl.1530 Norsk tid.</p>
<p>Grunnen til at jeg valgte Norges Bank l&oslash;sning er pga historikken, samt at det verdiene er forholdsvis greie &aring; f&aring; tak i.</p>


<?php
 	$todays_charset = "UTF-8";
  
  	include 'config.php';
	include 'opendb.php';
	mysql_set_charset($todays_charset, $conn);
 	$valuta = "select * from valutakurser order by land asc";
 	$result = mysql_query($valuta, $conn);
	//echo "<br /><b>".htmlentities($pnavn)."</b>";
	if(!$result)
	{
		echo "<div class=\"msg_error\">".mysql_error($conn)."</div>";
		include 'closedb.php';
		exit();
	}
	$row = mysql_fetch_row($result);
	
	echo "<form id=\"idform_valuta\" name=\"form_valuta\" method=\"post\" action=\"index.php\">";
	echo "<select id=\"id_select_valuta\" name=\"valuta_valg\" tabindex=\"1\">";
  	if($row)
	{
		while(1)
		{
			if(!$row)
				break;
			echo "<option value=\"".$row[2]."\">".$row[1]."</option>";
			$row = mysql_fetch_row($result);
		}
	}
	echo "</select>";
	echo "<input type=\"submit\" id=\"button_style\" name=\"pnummer_btn\" id=\"id_pnummer_btn\" value=\"Hent\" tabindex=\"2\"/><br/>";
	echo "<input type=\"hidden\" name=\"search_type\" value=\"3\" />";
	
	
   echo "</form>";
	if($result)
		mysql_free_result($result);
	include 'closedb.php';
	
?>