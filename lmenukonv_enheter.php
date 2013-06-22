<p>Finn ut hva britiske enheter blir i forhold til det metriske systemet.</p>


<?php
	//sjekk om vi kan hente verdier....
	$pressed_btn = 0;
	$pressed_masse_btn = 0;
	$svar = 0;
	$prefix_val = 0;
	$br_antall = 1;
	$enhet_valg_br = "";
	$enhet_si_prefiks = "";
	$enhet_en_no = "";
	$enhet_masse_si_prefiks = "kilo";//default
	$enhet_valg_masse_br = "pound";
	$method = $_SERVER['REQUEST_METHOD'];
	$masse_antall = 1;
	$volum_antall = 1;
	$enhet_volum_si_prefiks = "";
	$enhet_valg_volum_br = "pint";
	$volum_prefix_val = 0;
	
	if($method == 'POST')
	{
		$t = $_POST['btn_type'];
		
		if($t == "lengde")
		{
			$pressed_btn = 1;
			$br_antall = $_POST['br_antall'];
			$enhet_valg_br = $_POST['enhet_valg_br'];
			$enhet_si_prefiks = $_POST['enhet_si_prefiks'];
		}
		else if($t == "masse")
		{
			/* *******   post fra masse-konvertering *******   */
			$pressed_masse_btn = 1;
			$masse_antall = $_POST['masse_antall'];
			$enhet_masse_si_prefiks = $_POST['enhet_masse_si_prefiks'];
			$enhet_valg_masse_br = $_POST['enhet_valg_masse_br'];
		}
		else if($t == 'volum')
		{
			/* *******   post fra masse-konvertering *******   */
			$pressed_volum_btn = 1;
			$volum_antall = $_POST['volum_antall'];
			$enhet_volum_si_prefiks = $_POST['enhet_volum_si_prefiks'];
			$enhet_valg_volum_br = $_POST['enhet_valg_volum_br'];
		}
		
	}
 	
 	$todays_charset = "ISO-8859-1";
  
  	include 'nscfg.php';
	include 'nsodb.php';
	mysql_set_charset($todays_charset, $mysql_connection);
 	$enheter = "select * from lengde_bram";
 	$result = mysql_query($enheter, $mysql_connection);
	
	if(!$result)
	{
		echo "<div class=\"msg_error\">".mysql_error($mysql_connection)."</div>";
		include 'nscdb.php';
		exit();
	}
	$row = mysql_fetch_row($result);



	echo "<form method=\"post\" action=\"index.php\" id=\"formid_enheter_br_m\" name=\"name_enheter_br_m\" accept-charset=\"iso-8859-1\" lang=\"no\">\n";
	echo "<table width=\"100%\">\n";
	echo "<tr class=\"table_header\">\n";
	echo "	<th colspan=\"7\">Konverter Britiske lengde-enheter til metrisk     </td>\n";
	echo "</th>\n";
	echo "<tr class=\"table_row1\">\n";
	echo "	<td>Konverter: </td>\n";
	echo "	<td><input type=\"text\" name=\"br_antall\" value=\"".$br_antall."\" tabindex=\"1\" size=\"4\" /></td>\n";
	echo "	<td><select id=\"id_select_enhet_br\" name=\"enhet_valg_br\" tabindex=\"2\">\n";
	$i = 0;
	if($row)
	{
		while(1)
		{
			if(!$row)
				break;
			if($enhet_valg_br == $row[0])
				echo "	<option value=\"".$enhet_valg_br."\" selected=\"selected\">".$enhet_valg_br."</option>\n";
			else
				echo "<option value=\"".$row[0]."\">".$row[0]."</option>\n";
			if($pressed_btn)
			{
				if($enhet_valg_br == $row[0])
				{
					$enhet_en_no = $row[4];
					$float_val = $row[2];
					$svar = $br_antall*$float_val;
				}
			} 
			$navn[$i++] = $row[2];
			$row = mysql_fetch_row($result);
		}
	}
	echo "</select>\n";
	echo "</td>\n";
	echo "<td> til </td>";
	$enheter = "select navn, n from si_prefiks";
 	$result = mysql_query($enheter, $mysql_connection);
	
	if(!$result)
	{
		echo "<div class=\"msg_error\">".mysql_error($mysql_connection)."</div>";
		include 'nscdb.php';
		exit();
	}
	$row = mysql_fetch_row($result);
	echo "	<td><select id=\"id_select_si_prefiks\" name=\"enhet_si_prefiks\" tabindex=\"3\">\n";
	echo "	<option value=\"\" selected=\"selected\"></option>\n";
	$i = 0;
	if($row)
	{
		while(1)
		{
			if(!$row)
				break;
			if($enhet_si_prefiks == $row[0])
				echo "	<option value=\"".$enhet_si_prefiks."\" selected=\"selected\">".$enhet_si_prefiks."</option>\n";
			else
				echo "<option value=\"".$row[0]."\">".$row[0]."</option>\n";
			if($enhet_si_prefiks == $row[0])
			{
				$prefix_val = $row[1];
			}
			$row = mysql_fetch_row($result);
		}
	}
	$svar = $svar/pow(10, $prefix_val);
	
	echo "</select>";
	echo "</td>\n";
	echo "<td>meter</td>";
	echo "<td>";
	echo "<input type=\"submit\" name=\"pnummer_btn\" id=\"id_pnummer_btn\" value=\"Kalkuler\" tabindex=\"4\" class=\"btn_style\" /><br/>";
	echo "</td>\n";
/*	echo "<td>Svar: </td>\n";
	echo "<td><input type=\"text\" name=\"konv_svar\" value=\"".$svar."\"  size=\"10\" readonly=\"true\" /></td>\n";
*/	echo "</tr>\n";
	if($pressed_btn)
	{
		echo "<tr class=\"table_header_result\">";
		echo "<td colspan=\"7\"><b>".$br_antall."</b> ".$enhet_valg_br."(".$enhet_en_no.") tilsvarer <b>".$svar."</b> ".$enhet_si_prefiks."meter</td>";
		echo "</tr>\n";
	}
	echo "</table>\n";
	echo "<input type=\"hidden\" name=\"search_type\" value=\"4\" />\n";
	echo "<input type=\"hidden\" name=\"btn_type\" value=\"lengde\" />\n";
	echo "</form>\n";
	
	/* ********************************************************************/
	/* All code from here on is for mass...     */	
	/* ********************************************************************/
		
	
	echo "<br /><br />\n";
	$enheter = "select navn, grunntall from masse_bram";
 	$result = mysql_query($enheter, $mysql_connection);
	
	if(!$result)
	{
		echo "<div class=\"msg_error\">".mysql_error($mysql_connection)."</div>";
		include 'nscdb.php';
		exit();
	}
	$row = mysql_fetch_row($result);
		
	echo "<form method=\"post\" action=\"index.php\" id=\"formid_enheter_masse\" name=\"name_enheter_masse\" accept-charset=\"iso-8859-1\" lang=\"no\">\n";
	echo "<table width=\"100%\">\n";
	echo "<tr class=\"table_header\">\n";
	echo "	<th colspan=\"7\">Konverter Britiske masse-enheter til metriske </td>\n";
	echo "</th>\n";
	echo "<tr class=\"table_row1\">\n";
	echo "	<td>Konverter: </td>\n";
	echo "	<td><input type=\"text\" name=\"masse_antall\" value=\"".$masse_antall."\" tabindex=\"1\" size=\"4\" /></td>\n";
	echo "	<td><select id=\"id_select_enhet_br\" name=\"enhet_valg_masse_br\" tabindex=\"2\">\n";
	if($row)
	{
		while(1)
		{
			if(!$row)
				break;
			if($enhet_valg_masse_br == $row[0])
				echo "	<option value=\"".$enhet_valg_masse_br."\" selected=\"selected\">".$enhet_valg_masse_br."</option>\n";
			else
				echo "<option value=\"".$row[0]."\">".$row[0]."</option>\n";
//			if($pressed_btn)
			{
				if($enhet_valg_masse_br == $row[0])
				{
					$float_val = $row[1];
					$svar2 = $masse_antall*$float_val*1000;
				}
			} 
			$row = mysql_fetch_row($result);
		}
	}
	echo "</select>\n";
	echo "</td>\n";
	echo "<td> til </td>";
	
	$enheter = "select navn, n from si_prefiks";
 	$result = mysql_query($enheter, $mysql_connection);
	
	if(!$result)
	{
		echo "<div class=\"msg_error\">".mysql_error($mysql_connection)."</div>";
		include 'nscdb.php';
		exit();
	}
	$row = mysql_fetch_row($result);
	echo "	<td><select id=\"id_masse_select_si_prefiks\" name=\"enhet_masse_si_prefiks\" tabindex=\"3\">\n";
	echo "	<option value=\" \"> </option>\n";
	$i = 0;
	if($row)
	{
		while(1)
		{
			if(!$row)
				break;
			if($enhet_masse_si_prefiks == $row[0])
				echo "	<option value=\"".$enhet_masse_si_prefiks."\" selected=\"selected\">".$enhet_masse_si_prefiks."</option>\n";
			else
				echo "<option value=\"".$row[0]."\">".$row[0]."</option>\n";
			if($enhet_masse_si_prefiks == $row[0])
			{
				$prefix_val = $row[1];
			}
			$row = mysql_fetch_row($result);
		}
	}
	
	echo "</select>";
	echo "</td>\n";
	$svar = $svar2/pow(10, $prefix_val);
	echo "<td>gram</td>";
	echo "<td>";
	echo "<input type=\"submit\" name=\"pnummer_btn_masse\" id=\"id_pnummer_btn\" value=\"Kalkuler\" tabindex=\"4\" class=\"btn_style\" /><br/>";
	echo "</td>\n";
/*	echo "<td>Svar: </td>\n";
	echo "<td><input type=\"text\" name=\"konv_svar\" value=\"".$svar."\"  size=\"10\" readonly=\"true\" /></td>\n";
*/	echo "</tr>\n";
	
	if($pressed_masse_btn)
	{
		echo "<tr class=\"table_header_result\">";
		echo "<td colspan=\"7\"><b>".$masse_antall."</b> ".$enhet_valg_masse_br." tilsvarer <b>".$svar."</b> ".$enhet_masse_si_prefiks."gram</td>";
		echo "</tr>\n";
	}
	echo "</table>\n";
	echo "<input type=\"hidden\" name=\"search_type\" value=\"4\" />\n";
	echo "<input type=\"hidden\" name=\"btn_type\" value=\"masse\" />\n";
	echo "</form>\n";
	
	/* ********************************************************************/
	/* All code from here on is for volumes...     */	
	/* ********************************************************************/
	echo "<br /><br />\n";
	$enheter = "select navn, grunntall from volum_bram";
 	$result = mysql_query($enheter, $mysql_connection);
	
	if(!$result)
	{
		echo "<div class=\"msg_error\">".mysql_error($mysql_connection)."</div>";
		include 'nscdb.php';
		exit();
	}
	$row = mysql_fetch_row($result);
		
	echo "<form method=\"post\" action=\"index.php\" id=\"formid_enheter_volum\" name=\"name_enheter_volum\" accept-charset=\"iso-8859-1\" lang=\"no\">\n";
	echo "<table width=\"100%\">\n";
	echo "<tr class=\"table_header\">\n";
	echo "	<th colspan=\"7\">Konverter Britiske volum-enheter til metriske </td>\n";
	echo "</th>\n";
	echo "<tr class=\"table_row1\">\n";
	echo "	<td>Konverter: </td>\n";
	echo "	<td><input type=\"text\" name=\"volum_antall\" value=\"".$volum_antall."\" tabindex=\"1\" size=\"4\" /></td>\n";
	echo "	<td><select id=\"id_select_volum_br\" name=\"enhet_valg_volum_br\" tabindex=\"2\">\n";
	if($row)
	{
		while(1)
		{
			if(!$row)
				break;
			if($enhet_valg_volum_br == $row[0])
				echo "	<option value=\"".$enhet_valg_volum_br."\" selected=\"selected\">".$enhet_valg_volum_br."</option>\n";
			else
				echo "<option value=\"".$row[0]."\">".$row[0]."</option>\n";
//			if($pressed_btn)
			{
				if($enhet_valg_volum_br == $row[0])
				{
					$float_val = $row[1];
					$svar2 = $volum_antall*$float_val;
				}
			} 
			$row = mysql_fetch_row($result);
		}
	}
	echo "</select>\n";
	echo "</td>\n";
	echo "<td> til </td>";
	
	$enheter = "select navn, n from si_prefiks";
 	$result = mysql_query($enheter, $mysql_connection);
	
	if(!$result)
	{
		echo "<div class=\"msg_error\">".mysql_error($mysql_connection)."</div>";
		include 'nscdb.php';
		exit();
	}
	$row = mysql_fetch_row($result);
	echo "	<td><select id=\"id_select_si_prefiks_v\" name=\"enhet_volum_si_prefiks\" tabindex=\"3\">\n";
	echo "	<option value=\"\" selected=\"selected\"></option>\n";
	$i = 0;
	if($row)
	{
		while(1)
		{
			if(!$row)
				break;
			if($enhet_volum_si_prefiks == $row[0])
				echo "	<option value=\"".$enhet_volum_si_prefiks."\" selected=\"selected\">".$enhet_volum_si_prefiks."</option>\n";
			else
				echo "<option value=\"".$row[0]."\">".$row[0]."</option>\n";
			if($enhet_volum_si_prefiks == $row[0])
			{
				$volum_prefix_val = $row[1];
			}
			$row = mysql_fetch_row($result);
		}
	}
	
	echo "</select>";
	echo "</td>\n";
	$svar = $svar2/pow(10, $volum_prefix_val);
	echo "<td>liter</td>";
	echo "<td>";
	echo "<input type=\"submit\" name=\"pnummer_btn_volum\" id=\"id_pnummer_btn_v\" value=\"Kalkuler\" tabindex=\"4\" class=\"btn_style\" /><br/>";
	echo "</td>\n";
/*	echo "<td>Svar: </td>\n";
	echo "<td><input type=\"text\" name=\"konv_svar\" value=\"".$svar."\"  size=\"10\" readonly=\"true\" /></td>\n";
*/	echo "</tr>\n";
	
	if($pressed_volum_btn)
	{
		echo "<tr class=\"table_header_result\">";
		echo "<td colspan=\"7\"><b>".$volum_antall."</b> ".$enhet_valg_volum_br." tilsvarer <b>".$svar."</b> ".$enhet_volum_si_prefiks."liter</td>";
		echo "</tr>\n";
	}
	echo "</table>\n";
	echo "<input type=\"hidden\" name=\"search_type\" value=\"4\" />\n";
	echo "<input type=\"hidden\" name=\"btn_type\" value=\"volum\" />\n";
	echo "</form>\n";
	
	include 'nscdb.php';
	
?>
