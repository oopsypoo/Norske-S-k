<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Resultat</title>
<link href="css/index.css" rel="stylesheet" type="text/css" />
</head>

<body>

<?php
 include '../includes/simplehtmldom/simple_html_dom.php';

function get_data($url)
{
  $ch = curl_init();
  $timeout = 10;
  $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
  $useragent = 'Opera/9.80 (J2ME/MIDP; Opera Mini/5.0 (Windows; U; Windows NT 5.1; en) AppleWebKit/886; U; en) Presto/2.4.15';
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

//Norske bokstaver: ae = %E6, oe = %F8, aa = %E5, 
function umlaute($text){ 
    $returnvalue=""; 
    for($i=0;$i<strlen($text);$i++){ 
        $teil=hexdec(rawurlencode(substr($text, $i, 1))); 
        if($teil<32||$teil>1114111){ 
            $returnvalue.=substr($text, $i, 1); 
        }else{ 
            $returnvalue.="&#".$teil.";"; 
        } 
    } 
    return $returnvalue; 
} 


$resources = 0;

$method = $_SERVER['REQUEST_METHOD'];
if($method=='POST')
{
	$skillelinje = "------------------------<br/>\n";
	$tlf_nr = $_POST["lemma"];

	$hvor=$_POST["hvor"];
	$firmasoek = $_POST["firma"];
//	$tlf_nr = gul_tekst($tlf_nr);
//	$hvor = gul_tekst($hvor);
	$tlf_nr = urlencode($tlf_nr);
	$hvor = urlencode($hvor);
	$stq = 0;
	$stq_streng = "&stq=";
	if(isset($_POST["stq"]))
	{
		$tlf_nr = $_POST['cur_search_word'];
		$hvor = $_POST['cur_geo_area'];
		$stq_streng .= $_POST["stq"];
		$firmasoek = $_POST['cur_firma'];
//		echo "Streng: ".$stq_streng;
	}
	else
		$stq_streng .= $stq;
//	echo "search_word: ".$tlf_nr;
	if($hvor)
		$hvor2 = strtr($hvor, " ", "+");
	else
		$hvor2="";
	
	$hva = strtr($tlf_nr, " ,", "+");
	if($firmasoek)
		$url = "http://mobil.gulesider.no/query?search_word=".$hva."&geo_area=".$hvor2."&hpp=10&what=mobcs";

	else
		$url = "http://mobil.gulesider.no/query?search_word=".$hva."&geo_area=".$hvor2."&hpp=10&what=mobwp";
//	echo "url: ".$url;
	$url .= $stq_streng;
//	echo "Vi ser litt paa denne strengen en gang til: ".$url."<br/>\n";
 	echo "<b>Resultat av ditt s&oslash;k:</b><br/>".$skillelinje;
	$rc = get_data($url);
/*	echo "<b>---------------------------------------------------------------</b><br/><br/>";
	echo $rc;
	echo "<b>---------------------------------------------------------------</b><br/><br/>";
*/
	$html = str_get_html($rc);
	$res_cathead = $html->find('.catHead');
	if($res_cathead)
	{
		if($firmasoek)
		{
		//	echo "Original streng: ".$res_cathead[0]."<br/>\n";
			$cathead_string = substr(strip_tags($res_cathead[0]), 5);
		}
		else {
			$cathead_string = substr(strip_tags($res_cathead[0]), 13); }
		echo $cathead_string."<br/>";
	
		$t1 = str_replace("av", "", $cathead_string); 
		$t2 = str_replace("(", "", $t1); 
		$t3 = str_replace(")", "", $t2); 
		//echo "t3:   ".$t3."<br/>";
		list($nedre, $temp) = explode("-", $t3);
		$temp2 = explode(" ", $temp);
		$nedre = intval($nedre);
		$oevre = intval($temp2[0]);
		$max = intval($temp2[2]);
		if($oevre <= $max)
		{
			$bs = 12;
			echo "<table id=\"button_restraint\">\n";
			echo "<tr>\n";
			if($nedre > 1)
			{
				echo "<td id=\"back_room\">";
				echo "<form id=\"form_tilbake\" name=\"form_tilbake\" >\n";
				echo "<input type=\"submit\" id=\"button_style\" name=\"Tilbake\" id=\"tilbake_knapp\" value=\"Tilbake\" onclick=\"history.go(-1);return false;\" size=\"".$bs."\" />";
				echo "</form>\n";
				echo "</td>";
			}
			if($oevre < $max) {
			echo "<td id=\"next_room\">";
			$stq_val = $stq+$oevre;
			echo "<form id=\"neste\" name=\"neste\" method=\"post\" action=\"index.php\">\n";
			echo "<input type=\"hidden\" name=\"stq\" value=\"".$stq_val."\" />";
			echo "<input type=\"hidden\" name=\"cur_geo_area\" value=\"".$hvor."\" />";
			echo "<input type=\"hidden\" name=\"cur_search_word\" value=\"".$tlf_nr."\" />";
			echo "<input type=\"hidden\" name=\"cur_firma\" value=\"".$firmasoek."\" />";		
			echo "<input type=\"hidden\" name=\"search_type\" value=\"1\" />";	
			echo "<input type=\"submit\" id=\"button_style\" name=\"Neste\" id=\"neste_knapp\" value=\"Neste\" size=\"".$bs."\" /><br/>";
			echo "</form>\n";
			//echo "<a href = \"".$url.$stq_streng.$stq_val."\">Neste:</a><br/>";
			echo "</td>";
			}
			echo "</tr>\n";
			echo "</table>\n";


		}
	}
	foreach($html->find('table td.hTd2') as $res)
	{
//		echo "<b>".$res."</b><br>";
		$res2 = strip_tags($res,"<br/>");
		$vals = explode("<br/>", $res2);
		foreach($vals as $val)
		{
			$res3 =  umlaute($val);
			echo $res3."<br/>";
		}
		echo $skillelinje;
	}

}
?>

</body>
</html>