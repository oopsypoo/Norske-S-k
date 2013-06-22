<?php
	$todays_charset = "ISO-8859-1";
  
  	include 'config.php';
	include 'opendb.php';
	
	$valgt_fylke = NULL;
	$valgt_kommune = NULL;
	$valgt_poststed = NULL;
	$valgt_gyldig_poststed = false;
	mysql_set_charset($todays_charset, $conn);
	
	$method = $_SERVER['REQUEST_METHOD'];
	if($method == 'POST')
	{
		
		switch($menuid)
		{
		case 21:
			$pnr = $_POST['postnummer'];
			$query = "SELECT postnummer.poststed_pnavn, kommune.knavn, fylke.fnavn 
						FROM postnummer, poststed, kommune, fylke 
						WHERE pnr='".$pnr."' && postnummer.poststed_pnavn=poststed.pnavn 
													&& poststed.kommune_knr=kommune.knr 
													&& kommune.fylke_idfylke=fylke.idfylke";
			$result = mysql_query($query);
			if(!$result)
				echo "<div class=\"msg_error\">".mysql_error($conn)."</div>";
			$row = mysql_fetch_row($result);
			if($row)
			{
				echo "<table class=\"table_header_result\">";
				echo "<tr>";
				echo "<td>Postnummer</td>";
				echo "<td>Poststed</td>";
				echo "<td>Kommune</td>";
				echo "<td>Fylke</td>";
				echo "<tr>";
				while(1)
				{
					if(!$row)
						break;
					if($annenhver == 0)
					{
						echo "<tr class=\"table_row1\">";
						$annenhver = 1;
					}
					else
					{
						echo "<tr class=\"table_row2\">";
						$annenhver = 0;
					}
					echo "<td>".$pnr."</td>";
					echo "<td>".htmlentities($row[0])."</td>";
					echo "<td>".htmlentities($row[1])."</td>";
					echo "<td>".htmlentities($row[2])."</td>";
					echo "</tr>";
					$kommune = $row[1];
					$fylke = $row[2];
					$valgt_poststed = $row[0];
					$valgt_kommune = $kommune;
					$valgt_fylke = $fylke;
					$valgt_gyldig_poststed = true;
					$row = mysql_fetch_row($result);
				}
				echo "</table>";
				echo "<script type=\"text/javascript\"> codeAddress(\"".$kommune.", ".$fylke.", Norge\"); </script>";
				
			}
			if($result)
				mysql_free_result($result);
			break;
		case 22:
			$pnavn = $_POST['poststed'];
/*			$query = "SELECT postnummer.pnr, kommune.knavn, fylke.fnavn 
						FROM postnummer, poststed, kommune, fylke 
						WHERE poststed_pnavn='".iconv("utf-8", "iso-8859-1", $pnavn)."' && postnummer.poststed_pnavn=poststed.pnavn 
																											&& kommune.knr=poststed.kommune_knr 
																											&& fylke.idfylke=kommune.fylke_idfylke";
	*/	
			$query = "SELECT postnummer.pnr, kommune.knavn, fylke.fnavn 
						FROM postnummer, poststed, kommune, fylke 
						WHERE poststed_pnavn='".$pnavn."' && postnummer.poststed_pnavn=poststed.pnavn 
																											&& kommune.knr=poststed.kommune_knr 
																											&& fylke.idfylke=kommune.fylke_idfylke";
			$result = mysql_query($query);
			//echo "<br /><b>".htmlentities($pnavn)."</b>";
			if(!$result)
				echo "<div class=\"msg_error\">".mysql_error($conn)."</div>";
			$row = mysql_fetch_row($result);
			if($row)
			{
				echo "<table class=\"table_header_result\">";
				echo "<tr>";
				echo "<td>Poststed</td>";
				echo "<td>Postnummer</td>";
				echo "<td>Kommune</td>";
				echo "<td>Fylke</td>";
				echo "<tr>";
				while(1)
				{
					if(!$row)
						break;
					if($annenhver == 0)
					{
						echo "<tr class=\"table_row1\">";
						$annenhver = 1;
					}
					else
					{
						echo "<tr class=\"table_row2\">";
						$annenhver = 0;
					}
					echo "<td>".$pnavn."</td>";
					echo "<td>".htmlentities($row[0])."</td>";
					echo "<td>".htmlentities($row[1])."</td>";
					echo "<td>".htmlentities($row[2])."</td>";
					$kommune = $row[1];
					$fylke = $row[2];
					echo "</tr>";
					$row = mysql_fetch_row($result);
				}
				echo "</table>";
				echo "<script type=\"text/javascript\"> codeAddress(\"".$kommune.", ".$fylke.", Norge\"); </script>";
				$valgt_poststed = $kommune;
				$valgt_kommune = $kommune;
				$valgt_fylke = $fylke;
				$valgt_gyldig_poststed = true;
			}
			if($result)
				mysql_free_result($result);
			break;
		case 23:
			$fylke = $_POST['fylkes_valg'];
			
			$query = "SELECT knr, knavn, fylke.fnavn FROM kommune, fylke WHERE kommune.fylke_idfylke='".$fylke."' && kommune.fylke_idfylke = fylke.idfylke";
			$result = mysql_query($query, $conn);
			if(!$result)
				echo "<div class=\"msg_error\">".mysql_error($conn)."</div>";
			
			$row = mysql_fetch_row($result);
			if($row)
			{
				$fylke_fnavn = $row[2];
				echo "<table class=\"table_header_result\">";
				echo "<tr>";
				echo "<td>Kommunenummer</td>";
				echo "<td>Kommune</td>";
				echo "<tr>";
				while(1)
				{
					if(!$row)
						break;
					if($annenhver == 0)
					{
						echo "<tr class=\"table_row1\">";
						$annenhver = 1;
					}
					else
					{
						echo "<tr class=\"table_row2\">";
						$annenhver = 0;
					}
					echo "<td>".$row[0]."</td>";
					echo "<td><a href=\"index.php?mtype=2&menuitem=6&knr=".$row[0]."\">".htmlentities($row[1])."</a></td>";
					echo "</tr>";
					$row = mysql_fetch_row($result);
				}
				echo "</table>";
				if($fylke_fnavn == "Svalbard")
					echo "<script type=\"text/javascript\"> codeAddress(\"".$fylke_fnavn."\"); </script>";
				else
					echo "<script type=\"text/javascript\"> codeAddress(\"".htmlentities($fylke_fnavn).", Norge\"); </script>";
			}
			if($result)
				mysql_free_result($result);
			break;
		}
		
		include 'closedb.php';
	}
	if($valgt_gyldig_poststed)
		include_once('vis_yr.php');

?>

