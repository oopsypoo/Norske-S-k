<?php

	/* rss-nyhetene kommer til å havne på høyre side enten som lenker eller som en drop-down box med 
		ferdige lenker...ikke helt sikker ennå, men mest sannsynlig 
		Her er lenkene jeg vet jeg skal ha med:
		
		Dagens Næringsliv - generelt(alle nyheter)
		-	http://www.dn.no/rss
		Aftenposten: Innenriks
		-	http://www.aftenposten.no/eksport/rss-1_0/?seksjon=nyheter_iriks&utvalg=siste
		NRK-innenriks
		-	http://www.nrk.no/norge/siste.rss
		Din Side (generelt, alt)
		-	http://www.dinside.no/phpf/feed/rss/dinside.php
		IT-Avisen
		-	http://www.itavisen.no/rss.php
		Linux Magasinet (Linux-generelle nyheter) må lage egen modul her . . .men skal ha den.
		-	http://www.linmag.no/article/archive/17/
		Hardware.no 
		-	http://www.hardware.no/feeds/general.xml
		Teknisk ukeblad
		-	http://www.tu.no/rss/
		
	*/
	global $xml_file;
	include '../includes/simplehtmldom/simple_html_dom.php';
	//when using simple_htm_dom with xml documents you have to remove self-enclosing-tags variable to null
	//I only removed the 'link' string from the array so that that I can read rss-feeds properly
	
	function strip_cdata($string)
	{
	    preg_match_all('/<!\[cdata\[(.*?)\]\]>/is', $string, $matches);
	    return str_replace($matches[0], $matches[1], $string);
	} 
	$method = $_SERVER['REQUEST_METHOD'];
	if($method == 'GET')
	{
		$link_item = $_GET['menuitem'];
		$query = "select xternal_link from menuitem where idmenu=$link_item";
		include 'nscfg.php';
		include 'nsodb.php';
		$result = mysql_query($query, $mysql_connection);
		if($result)
		{
			$row = mysql_fetch_row($result);
			if($row)
			{
				$html = file_get_html($row[0]);
				//echo $html->plaintext."<br /><br /><br />";
				$overskrift = $html->find('title',0)->innertext;
				//$olink = $html->getElementByTagName("link");
				$olink = $html->find("link",0)->innertext;
/*				echo "<h2>".$test."</h2><br/>\n";  */
				$kdesc = $html->find('description',0)->innertext;
				if(mb_detect_encoding($kdesc, 'UTF-8', true)=='UTF-8')
					$kdesc = $kdesc;
				else 
					$kdesc = utf8_encode($kdesc);
				echo "<br /><h3><a target=\"_blank\" href=\"$olink\">".$overskrift."</a></h3>".$kdesc."\n";
				$items = $html->find('item');
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

					
					
					$html2 .= "<p><a target=\"_blank\" href=\"$link\" title=\"$tittel\">$tittel</a><br />".
									strip_cdata($desc)."<br />$pubDato<br /></p>";
				}
				if(isset($html2))
					echo $html2;
				mysql_free_result($result);
				include 'nscdb.php';
				
			}
		}
	}
?>