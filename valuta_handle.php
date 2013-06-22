<?php
	//get simple html-dom-interface/(functions
	include '../includes/simplehtmldom/simple_html_dom.php';
	function get_data($url)
{
  $ch = curl_init();
  $timeout = 10;
  $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
  $useragent = "Mozilla/5.0 (X11; Linux x86_64; rv:5.0) Gecko/20100101 Firefox/5.0";
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, "./cookie.txt");
  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
  curl_setopt($ch, CURLOPT_FAILONERROR, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_AUTOREFERER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
  $data = curl_exec($ch);

  curl_close($ch);
  return $data;
}
	$todays_charset = "ISO-8859-1";
  
  	include 'config.php';
	include 'opendb.php';
	mysql_set_charset($todays_charset, $conn);
	
	$method = $_SERVER['REQUEST_METHOD'];
	if($method == 'POST')
	{
		$isokode = $_POST['valuta_valg'];
		if($isokode)
		{
			$query = "select * from valutakurser where iso_kode='".$isokode."'";
			$result = mysql_query($query, $conn);
			if(!$result)
			{
				include 'closedb.php';
				exit("Mysql-error..exiting<br />");
			}		
			$row = mysql_fetch_row($result);
			$link = "http://www.norges-bank.no/no/prisstabilitet/valutakurser/";
			
			if(!$row[4]) //ikke utgått
			{
				$fullink = $link.$row[2]."/";
			}
			else 
			{
				$fullink = $link.$row[2]."-utgatt/";
			}
			
			$rc = get_data($fullink);
			if(!$rc)
			{
				echo "url that failed: ".$fullink."<br />";
				include 'closedb.php';
				exit("Getting resource from get_data failed...exiting");
			}
			$html = str_get_html($rc);
						
/*			if(!$row[4]) //se etter litt forskjellige ting hvis en valuta er utgått eller ikke
			{				//å finne riktig tekst nøyaktig blir vanskelig når det ikek er knyttet til en klasse
							//nærmeste klasse er midtre kolonne som inneholder alt. Dette kan vi bruke hvis en valuta har utgått
				$res_valuta = $html->find('.stikktittelboks', 0);
				$res2 = $html->find('strong', 0);
				if(!$res_valuta)
				{
					echo "Kunne ikke finne resource_valuta in html file...<br />\n";
				}
				else 
					echo $res_valuta->plaintext."<br />\n";
				
				if(!$res2)
				{
					$res2 = $html->find('h3', 0);
					if(!$res2)
						echo "Kunne ikke finne resource2_enhet in html file..hverken strong eller h3.<br />\n";
				}
				if($res2)
					echo "<b>".$res2->innertext."</b><br />\n";
				
				$res_table = $html->find('table', 0);//we can do this since there is only one table
				if(!$res_table)
					echo "Could not find table-resource in html file...<br />\n";
				else
					echo $res_table;
			}
			else */
			{
				$res_midt = $html->find('div[id=innholdmidt]', 0);//get the div id element
				$a_links = $res_midt->find('a');
				if($a_links)
				{
					$antall_linker = count($a_links);
					$i = 0;
					while($i < $antall_linker)
					{
						$url_info = parse_url($a_links[$i]->href);
					
						if(!$url_info['host'])//domain/hostname is missing
						{
							$url = "http://www.norges-bank.no".$a_links[$i]->href;
							$a_links[$i]->href = $url; //set new url
						}
						$i++;
					}
				}
				$res_table = $res_midt->find('table');
				$res_th = $res_midt->find('th');
				$res_tr = $res_midt->find('tr');
				$res_tr[0]->valign = NULL;
				$res_tr[0]->outertext = '<tr class="tab_row1"><td>Dato</td><td>Verdi</td><td> &nbsp; &nbsp; &nbsp; &nbsp;</td></tr>';
				if($res_table)
				{
					//remove table attributes
					$res_table[0]->style = NULL;
					$res_table[0]->border = NULL;
					$res_table[0]->class = 'tabeller';
					$res_table[1]->class = 'tabeller';
				//	$res_table[1]->style = 'text-align: center';
					
				}
				if($res_th)
				{
					if(isset($res_th))
					{
						$res_th[0]->width = NULL;
						$res_th[1]->width = NULL;
					//	$res_th[0]->style = 'text-align: left';
					//	$res_th[1]->style = 'text-align: left';
					}
				}
				$p = 0;
				foreach($res_tr as $element)
				{
					if($p == 0)
					{
						$element->class = 'tab_row1';
						$p = 1;
					}
					else
					{
						$element->class = 'tab_row2';
						$p = 0;
					} 
				}
				if(!$res_midt)
					exit("Could not find class '.innholdmidt'..exiting<br />\n");
				else
					echo strip_tags($res_midt, "<p><strong><br /><table><tr><td><th><a>")."<br />\n";
				
				
			}
		}
		
	}
	include 'closedb.php';
		
?>