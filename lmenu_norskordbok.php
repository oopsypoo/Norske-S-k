
<?php
	function rstrstr($haystack,$needle)
   {
   	return substr($haystack, 0,strpos($haystack, $needle));
   }
	include '../includes/simplehtmldom/simple_html_dom.php';
	
	function get_data($url)
	{
	  $ch = curl_init();
	  $timeout = 10;
	  $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
	//  $useragent = 'Opera/9.80 (J2ME/MIDP; Opera Mini/5.0 (Windows; U; Windows NT 5.1; en) AppleWebKit/886; U; en) Presto/2.4.15';
	  curl_setopt($ch,CURLOPT_URL,$url);
	//  curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
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

	$mtype = $_GET['mtype'];
	$mitem = $_GET['menuitem'];
	$todays_charset = "iso-8859-1";
	echo "<p>Skriv inn det du s&oslash;ker p&aring; nedenfor:<br /></p>\n";
	echo	"<form id=\"dictnoid\" name=\"dictnoid\" method=\"get\" action=\"index.php\">\n";
		echo	"<input type=\"hidden\" name=\"mtype\" value=\"$mtype\" />\n";
	  	echo	"<input type=\"hidden\" name=\"menuitem\" value=\"$mitem\" />\n";
	  	echo	"<input name=\"lemma\" type=\"text\" id=\"lemma\" tabindex=\"1\" size=\"45\" maxlength=\"45\" />\n";
	  	echo	"<input type=\"submit\" name=\"dbsearch\" id=\"dbsearch\" value=\"Search\" tabindex=\"2\" />\n";
	echo "</form>\n";
	echo "<br />\n";
	echo "<br />\n";
	if(isset($_GET["lemma"]))
	{
		
		$idlemma=$_GET["lemma"];
		$idlemma = urlencode($idlemma);
		
		$url = "http://no.thefreedictionary.com/".$idlemma;
		
		$rc = get_data($url);
		
		$html = str_get_html($rc);

		$rc1 = $html->find('div[id=MainTxt]');
		if(!$rc1)//ingenting ble funnet
		{
			echo "Ordet ble ikke funnet .<b>$idlemma</b>. <br />\n";
		}
		else
		{
			if(!isset($rc1))
				echo "<h1>Fant ikke rc1=MainTxt</h1><br />";
	//		$rc1[0] = rstrstr($rc1[0]->outertext, "<p class=\"brand_copy\">");
			
			$rc2 = $rc1[0]->find('div');
			if(!isset($rc2))
				echo "<h1>Fant ikke rc2=<div></h1><br />";
			
			$runsegs = $rc1[0]->find('div[class=runseg]');
			if(!$runsegs) //then we just copy the whole show and print it. Else we can format and do fun stuff
			{
				$text = rstrstr($rc1[0]->outertext, "<script>kdict()</script>");
				echo $text."<br />\n";
				
			}
			else
			{
				$counter = count($runsegs);
				$t = 0;
				$toptext = rstrstr($rc2[0]->innertext, "<div class=runseg>");
				if(isset($runsegs))
				{
					echo "<table>\n";
					echo "<tr>";
					echo "<td><u><b>".$toptext."</b></u></td>";
					echo "</tr>\n";
					while($t < $counter)
					{
						
						
						$text = rstrstr($runsegs[$t]->innertext, "<div class=ds-single>");
						if(!$text)//this can happen alot whcih can meen 1 of 2 things: no illustrations or we can't find anything
						{
							$text = $runsegs[$t]->innertext;
						}
						echo "<tr>";
						echo "<td>".$text."</td>\n";
						echo "</tr>\n";
						$illustrations = $runsegs[$t]->find('div[class=ds-single]');
						$u = 0;
						if(($counter2 = count($illustrations)) > 0)
						{
							while($u < $counter2)
								echo "<tr><td>&emsp;&emsp;&mdash;".$illustrations[$u++]->innertext."</td></tr>\n";
						}
						$t++;
					}
					echo "</table>\n";
				}
			}
		}
		echo "<br /><hr />\n";
		$word_browse = $html->find('table[id=Browsers]');
		
		if($word_browse)
		{
			$a_links = $word_browse[0]->find('a');
			if($a_links)
			{
				$antall_linker = count($a_links);
				$i = 0;
				while($i < $antall_linker)
				{
					$url_info = parse_url($a_links[$i]->href);
				
					if(!$url_info['host'])//domain/hostname is missing
					{
						$url = "index.php?mtype=$mtype&amp;menuitem=$mitem&amp;lemma=".substr($a_links[$i]->href, 1);
						$a_links[$i]->href = $url; //set new url
					}
					$i++;
				}
			}
			echo $word_browse[0]->outertext."\n";
		}
	}
		
?>
