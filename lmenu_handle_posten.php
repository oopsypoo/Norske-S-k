<?php
	
  	function conv_url_kommunenavn($input)
	{
	    $temp = NULL;
	    $len = strlen($input);
	    for ($i = 0; $i < $len; $i++)
	    {
	        $ch = $input[$i];
	        switch($ch) 
	        {
				case 'Ø':	        	
	        	case 'ø':
	        		$temp .= 'o';
	        		break;
				case 'Å':				
				case 'Æ':	        	
	        	case 'æ':
	        	case 'å':
	        		$temp .= 'a';
	        		break;
	        	case ' ':
	        		$temp .= '-';
	        		break;
	        	default:
	        		$temp .= $ch;
	        		break;
	        
	        }
	    }
	    return $temp;
	}
	function strip_cdata($string)
	{
	    preg_match_all('/<!\[cdata\[(.*?)\]\]>/is', $string, $matches);
	    return str_replace($matches[0], $matches[1], $string);
	}
	//skriver ut kommuner som tilhører et fylke med tilsvarende fylkes-id 
	function vis_kommuner($fylkesid)
	{
		global $conn;
		$sql_kommuner = "select kommune.knr,kommune.knavn, fylke.fnavn from kommune, fylke where kommune.fylke_idfylke=$fylkesid 
					&& fylke.idfylke=kommune.fylke_idfylke order by kommune.knavn";
		
		$result  = mysql_query($sql_kommuner, $conn);
		$row = mysql_fetch_row($result);
		if($row) 
		{
			echo "<table width=\"100%\">
					<tr><td>Kommuner i fylket <b><i>$row[2]</i></b></td></tr>\n";
			while(1) 
			{
				if(!$row)
					break;
				echo "<tr><td><a href=\"index.php?mtype=2&menuitem=6&postnummer=&poststed=&velg_kommune=
						".urlencode($row[0])."&nyheter=gjem\">$row[1]</a></td></tr>\n";
				$row = mysql_fetch_row($result);
			}
			echo "</table><br />\n";
		}
	}
	function vis_poststeder($kommunenr)
	{
		global $conn;
		$sql_kommuner = "select poststed.pnavn, kommune.knavn 
							from poststed, kommune
							where poststed.kommune_knr='$kommunenr' && poststed.kommune_knr=kommune.knr";
		
		$result  = mysql_query($sql_kommuner, $conn);
		$row = mysql_fetch_row($result);
		if($row) 
		{
			echo "<table width=\"100%\">
					<tr><td>Poststeder i kommunen <b><i>$row[1]</i></b></td></tr>\n";
			while(1) 
			{
				if(!$row)
					break;
				echo "<tr><td><a href=\"index.php?mtype=2&menuitem=6&postnummer=&poststed=&velg_poststed=
				".urlencode($row[0])."&nyheter=gjem\">$row[0]</a></td></tr>\n";
				$row = mysql_fetch_row($result);
			}
			echo "</table><br />\n";
		}
	}
	function vis_distriktsnyheter($distrikt)
	{
		global $conn;
		
		include_once( '../includes/simplehtmldom/simple_html_dom.php');
		
		$sql_nyheter = "select nrk_nh.rss_lenke from fylke, nrk_nh where fylke.fnavn='$distrikt' 
												&& fylke.idfylke=nrk_nh.fylke_idfylke";
		$result = mysql_query($sql_nyheter, $conn);
		$row = mysql_fetch_row($result);
		
		$html = file_get_html($row[0]);
		//echo $html->plaintext."<br /><br /><br />";
		$overskrift = $html->find('title',0)->innertext;
		//$olink = $html->getElementByTagName("link");
		$olink = $html->find("link",0)->innertext;
/*				echo "<h2>".$test."</h2><br/>\n";  */
		$kdesc = $html->find('description',0)->innertext;
		echo "<br /><h3><a href=\"$olink\">".$overskrift."</a></h3>".$kdesc."\n";
		$items = $html->find('item');
		$html2 = "";
		foreach($items as $item)
		{
			$tittel = $item->find('title',0)->innertext;
			if(mb_detect_encoding($tittel, 'UTF-8', true)=='UTF-8')
				$tittel = $tittel;
			else 
				$tittel = utf8_encode($tittel);
			$tittel = strip_cdata($tittel);
			$desc = $item->find('description',0)->innertext;
			if(mb_detect_encoding($desc, 'UTF-8', true)=='UTF-8')
				$desc = $item->find('description',0)->innertext;
			else
				$desc = utf8_encode($item->find('description',0)->innertext);	
			
			$desc = html_entity_decode($desc);
			$pubDato = $item->find('pubDate',0)->innertext;
			$link =	$item->find('link',0)->innertext;

			
			
			$html2 .= "<p><a href=\"$link\" title=\"$tittel\">$tittel</a><br />".
							strip_cdata($desc)."<br />$pubDato<br /></p>";
		}
		if(isset($html2))
			echo $html2;
		mysql_free_result($result);
		
	}
	function curPageURL() 
	{
		$pageURL = 'http';
		if(isset($_SERVER["HTTPS"]))
		{
			if ($_SERVER["HTTPS"] == "on") 
			{
				$pageURL .= "s";
			}
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") 
		{
		  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} 
		else 
		{
		  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	include 'config.php';
	include 'opendb.php';
	if(isset($_GET['velg_fylke'])) 
	{
		$fylkesid = $_GET['velg_fylke'];
		if($fylkesid != '')
			vis_kommuner($fylkesid);
 	}
 	elseif(isset($_GET['velg_kommune'])) 
	{
		$kommunenr = $_GET['velg_kommune'];
		if($kommunenr != '')
			vis_poststeder($kommunenr);
 	}
	if(isset($_GET['postnummer']) || isset($_GET['poststed']))
	{
		if($_GET['postnummer'] != NULL)
		{ //echo "postnummer er satt";
			$pnr = $_GET['postnummer'];
			$sql = "select fylke.fnavn, kommune.knavn, postnummer.poststed_pnavn
						from  postnummer, kommune, poststed, fylke
						where postnummer.pnr='$pnr' 
							&& poststed.pnavn=postnummer.poststed_pnavn 
							&& poststed.kommune_knr=kommune.knr
							&& kommune.fylke_idfylke=fylke.idfylke";
			$valg_ferdig = TRUE;		
			$search_post = TRUE; 
		}
		else if($_GET['poststed']!=NULL)
		{//echo "poststed er satt";
			$psted = $_GET['poststed'];
			$sql = "select fylke.fnavn, kommune.knavn, postnummer.poststed_pnavn
						from  postnummer, kommune, poststed, fylke
						where postnummer.poststed_pnavn='$psted' 
							&& poststed.pnavn=postnummer.poststed_pnavn 
							&& poststed.kommune_knr=kommune.knr
							&& kommune.fylke_idfylke=fylke.idfylke";
			$valg_ferdig = TRUE;		
			$search_post = TRUE;
		}
	}
	if($valg_ferdig == TRUE)
	{
		if($search_post == FALSE)
			$sql = "select fylke.fnavn, kommune.knavn, poststed.pnavn
				from fylke, kommune, poststed 
				where fylke.idfylke=$fylke && kommune.knr='$kommune' && poststed.pnavn='$poststed'";
 		//echo "SQL: $sql<br />\n";
 		//dette skulle bli 1 rad..ikke noe mer.
 		$result = mysql_query($sql, $conn);
		if(!$result)
		{
			echo "<div class=\"msg_error\">".mysql_error($conn)."</div>";
			include 'closedb.php';
			exit();
		}
		$row = mysql_fetch_row($result);
		if($psted)
		{
			if(!$row)//hvis vi ikke finner noe på poststed så kan vi ta å prøve å søke på kommune.  
			{
				$sql = "select poststed.pnavn, fylke.fnavn from poststed, kommune, fylke where kommune.knavn='$psted' 
											&& kommune.knr=poststed.kommune_knr && kommune.fylke_idfylke=fylke.idfylke";
				$result = mysql_query($sql, $conn);
				if(!$result)
				{
					echo "<div class=\"msg_error\">".mysql_error($conn)."</div>";
					include 'closedb.php';
					exit();
				}
				$row = mysql_fetch_row($result);
				
				if($row)
				{
					echo "Hadde ikke noe resultat på poststeder, men fant $psted under kommune. Velg et poststed i $psted:<br />\n";
					echo "<table>\n";
					echo "<tr><th>Poststed</th></tr>\n";
					while(1)
					{
						if(!$row)
							break;
						echo "<tr><td><a href=\"index.php?mtype=2&menuitem=6&postnummer=&poststed=".urlencode($row[0]).
											"&velg_fylke=0\">".$row[0]."</a></td></tr>\n";
						$row = mysql_fetch_row($result);
					}
					echo "</table><br />\n";
					unset($row);//gjør dette slik at "tulle-data" ikke blir sendt videre i scriptet
					$valg_ferdig = FALSE;
				}
			}
		}
		elseif($pnr)
		{
			if(!$row)//hvis vi ikke finner postnummeret kan vi ramse opp de 20 nærmeste  
			{
				$p_min = intval($pnr)-30; echo "p_min: ".$p_min;
				if($p_min<1)
					$p_min = 1;
				$p_max = intval($pnr)+30;echo "p_max: ".$p_max;
				if($p_max > 9991)
					$p_max == 9991;
				$pstr_min = sprintf("%'04s",$p_min);
				$pstr_max = sprintf("%'04s", $p_max);
				$sql = "select postnummer.pnr, postnummer.poststed_pnavn from postnummer 
							where postnummer.pnr between '$pstr_min' and '$pstr_max'";
				echo "SQL: ".$sql;
				$result = mysql_query($sql, $conn);
				if(!$result)
				{
					echo "<div class=\"msg_error\">".mysql_error($conn)."</div>";
					include 'closedb.php';
					exit();
				}
				$row = mysql_fetch_row($result);
				
				if($row)
				{
					echo "Fant ikke noe på postnummeret $pnr. Men du kan velge disse som er i nærheten:<br />\n";
					echo "<table>\n";
					echo "<tr><th>Postnummer</th><th>Navn</th></tr>\n";
					while(1)
					{
						if(!$row)
							break;
						echo "<tr><td><a href=\"index.php?mtype=2&menuitem=6&postnummer=&poststed=".urlencode($row[1]).
											"&velg_fylke=0\">".$row[0]."</a></td><td>".$row[1]."</td></tr>\n";
						$row = mysql_fetch_row($result);
					}
					echo "</table><br />\n";
					unset($row);//gjør dette slik at "tulle-data" ikke blir sendt videre i scriptet
					$valg_ferdig = FALSE;
				}
			}
		}
		if(isset($row))//alt ok...nå kan vi begynne å skrive ut all info.
		{
			$valgt_fylke = $row[0];
			$valgt_kommune = $row[1];
			$valgt_poststed = $row[2];
		//	setcookie("fylke","",time() - 60*60);
		//	setcookie("kommune","",time() - 60*60);
			
			$sql_options = "select poststed.pnavn,poststed.pnavn 
								from poststed, kommune
								where kommune.knavn='$valgt_kommune' 
										&& kommune.knr=poststed.kommune_knr order by poststed.pnavn";
			echo "<table><tr>
						<th colspan=\"7\">Min Kommune <i>$valgt_kommune</i></th></tr>
						<tr>
							<td class=\"andre_poststeder\" colspan=\"3\">Andre poststeder i $valgt_kommune</td>
							<td colspan=\"4\">";
							write_sql_options($sql_options, "id_flere_poststeder", "flere_poststeder", NULL, NULL);
			$sql_options = "select kommune.knavn,kommune.knavn
								from kommune, fylke
								where fylke.fnavn='$valgt_fylke' && fylke.idfylke=kommune.fylke_idfylke order by kommune.knavn"; 
			echo "</td>
						</tr>
						<tr>
							<td class=\"andre_kommuner\" colspan=\"3\">Andre kommuner i $valgt_fylke</td>
							<td colspan=\"4\">";
							write_sql_options($sql_options, "id_flere_kommuner", "flere_kommuner", NULL, NULL);
			$name = conv_url_kommunenavn(utf8_decode($valgt_kommune));
			$url = "http://www.".$name.".kommune.no";
			$sql_aviser = "select avis.idavis, avis.navn, avis.url, fylke.webadr from avis, fylke
								where fylke.fnavn='$valgt_fylke' && fylke.idfylke=avis.fylke_idfylke order by avis.navn";
			echo "</td>
						</tr>
						<tr>
							<td class=\"andre_poststeder\" colspan=\"3\">$valgt_kommune sitt nettsted: </td>
							<td class=\"andre_poststeder\" colspan=\"4\"><a href=\"$url\" target=\"_blank\">".ucfirst(strtolower($valgt_kommune))." kommune</a></td>
						</tr>";
			
						$result = mysql_query($sql_aviser, $conn);
						if(!$result)
						{
							echo "<div class=\"msg_error\">".mysql_error($conn)."</div>";
							include 'closedb.php';
							exit();
						}
						$row = mysql_fetch_row($result);
			echo "			<tr>
							<td class=\"andre_poststeder\" colspan=\"7\"><a target=\"_blank\" href=\"$row[3]\">$valgt_fylke fylkeskommune sitt nettsted</a> </td>
						//	<td class=\"andre_poststeder\" colspan=\"4\"></td>
						</tr>
						<tr>
							<td class=\"andre_kommuner\" colspan=\"3\">Lokalaviser i $valgt_fylke: </td>
							<td colspan=\"4\">";
			
						echo "<select id=\"idaviser\" name=\"navnavis\" onChange=\"openNew(this.options[this.selectedIndex].value)\">\n";
						echo "<option value=\"Velg en Avis\">Velg en Avis</option>";
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
						echo "</select><br />\n";
						echo "</td>
							</tr>";
						
						echo	"<tr>";
						$nyheter = $_GET['nyheter']; 
						if(($nyheter == 'gjem'))
						{
							echo"		<td class=\"andre_kommuner\" colspan=\"7\"><a href=\"index.php?mtype=2
										&menuitem=6&postnummer=&poststed=$valgt_poststed
									&velg_fylke=0&pnummer_btn=Finn&nyheter=vis\"><b>Vis</b></a> Distriktsnyheter fra 
									$valgt_fylke</td>";
						}
						else if($nyheter == 'vis')
						{
							echo"		<td class=\"andre_kommuner\" colspan=\"7\"><a href=\"index.php?mtype=2&menuitem=6&postnummer=&poststed=$valgt_poststed
									&velg_fylke=0&pnummer_btn=Finn&nyheter=gjem\"><b>Gjem</b></a> Distriktsnyheter fra 
									$valgt_fylke</td>";
						}
						echo"		</tr>
						</table>";
						if($nyheter == 'vis')
						{
							vis_distriktsnyheter($valgt_fylke);
						}
		}
	}
	if($result)
		mysql_free_result($result);
	include 'closedb.php';
	echo "<br />\n";
	
	if($valg_ferdig == TRUE)
	{
		global $coord;
		//vi må gjøre om strengene fra utf-8 til iso-8859-1
		$valgt_poststed = utf8_decode($valgt_poststed);
		$valgt_kommune = utf8_decode($valgt_kommune);
		$valgt_fylke = utf8_decode($valgt_fylke);
		$title_tekst = "$valgt_poststed, $valgt_kommune, $valgt_fylke, Norge";
		include('hent_yr.php');
		echo "<div id=\"map_container\"></div>\n";
		echo "<script type=\"text/javascript\"> codeAddress(\"$title_tekst\"); </script>";
	}
		
?>