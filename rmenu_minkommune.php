<?php
	include 'config.php';
	include 'opendb.php';
	$valg_ferdig = FALSE;
	$search_post = FALSE;
	function write_sql_options($sql, $id, $name, $default_option, $func) 
	{
		global $conn;
		$result = mysql_query($sql, $conn);
		if(!$result)
		{
			echo "<div class=\"msg_error\">".mysql_error($conn)."</div>";
			include 'closedb.php';
			exit();
		}
		$row = mysql_fetch_row($result);
		echo "<select id=\"$id\" name=\"$name\" tabindex=\"3\" onchange=\"$func\" width=\"20\">\n";
	  	if($row)
		{
			if(isset($default_option))
				echo "<option value=\"$default_option[0]\">$default_option[1]</option>";
			while(1)
			{
				if(!$row)
					break;
				echo "<option value=\"".$row[0]."\">".$row[1]."</option>";
				$row = mysql_fetch_row($result);
			}
		}
		echo "</select>\n";
	}
	
	echo "<form id=\"idform_poststed\" name=\"postnummer\" method=\"get\" action=\"index.php\">
			<input type=\"hidden\" name=\"mtype\" value=\"2\" />
 			<input type=\"hidden\" name=\"menuitem\" value=\"6\" />
  			Skriv postnummer<br/>
      	<input name=\"postnummer\" type=\"text\" id=\"id_postnummer\" maxlength=\"4\" tabindex=\"1\" />
      	<br />
			...eller poststed<br/>
      	<input name=\"poststed\" type=\"text\" id=\"id_poststed\" maxlength=\"45\" tabindex=\"2\" lang=\"no\" />
		";
	$todays_charset = "utf-8";
	mysql_set_charset($todays_charset, $conn);
 	$options[0] = 0;
 	$sql_options = "select * from fylke order by fylke.fnavn";
 	$options[1] = "Velg Fylke";
 	$id = "id_velg_fylke";
 	$name = "velg_fylke";
 	if(isset($_GET['velg_fylke'])) 
 	{
 		$fylke = $_GET['velg_fylke'];
 		if($fylke != 0)
 		{
	 		$sql_options = "select * from kommune where fylke_idfylke='$fylke' order by kommune.knavn";
			$options[1] = "Velg Kommune";
			$id = "id_velg_kommune";
	 		$name = "velg_kommune";
	 	}
 	}
 	elseif(isset($_GET['velg_kommune']))
 	{
		$kommune =  $_GET['velg_kommune'];
 		$sql_options = "select pnavn,pnavn from poststed where kommune_knr='$kommune' order by poststed.pnavn";
		$options[1] = "Velg Poststed";
		$id = "id_velg_poststed";
 		$name = "velg_poststed";
 	}
 	elseif(isset($_GET['velg_poststed']))
 	{
		$poststed =  $_GET['velg_poststed'];
 		$valg_ferdig = TRUE;
 		
 		$sql_options = "select * from fylke";
	 	$options[1] = "Velg Fylke";
	 	$id = "id_velg_fylke";
	 	$name = "velg_fylke";
 	}
 	
echo "	<br /> eller ".
			$options[1].
			"<br />";
	//echo "options[0]: ".$options[0].", options[1]: ".$options[1]."<br />\n";
	write_sql_options($sql_options, $id, $name, $options, "postSubmit()"); 
	echo "<br />\n";
	echo "<input type=\"hidden\" name=\"nyheter\" value=\"gjem\" />\n";
	echo "<input type=\"submit\" id=\"id_finn_poststed\" name=\"pnummer_btn\" id=\"id_pnummer_btn\" value=\"Finn\" tabindex=\"4\"/><br/>";
	
	echo "</form>";
	
	include 'closedb.php';
?>