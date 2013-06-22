<p>Det greske alfabetet blir brukt til det meste innen Matematikken. Og da kan det v&aelig;re greit &aring; ha en oversikt over navn og utseende. Har ogs&aring;
tatt med html-entiteten(no og navn) for de forskjellige bokstavene. 
Jeg har selv tastet tastet inn all informasjon inn i databasen s&aring; det kan hende at feil kan forekomme. Hvis det er tilfellet s&aring; send en melding til meg.
</p>

<h3>Det greske alfabetet</h3>
<br />
<?php
	$todays_charset = "UTF-8";
	include 'nscfg.php';
	include 'nsodb.php';
	mysql_set_charset($todays_charset, $mysql_connection);
	$query = "select * from gresk_alfabet";
	
	$result = mysql_query($query, $mysql_connection);
	if(!$result)
	{
		echo "Mysql_error: ".mysql_error($result)."<br/>";
		exit("Could not execute query. Bad return-result...exiting");
	}
	else 
	{
		$row = mysql_fetch_row($result);
		$annenhver = 0;
		if($row)
		{
			echo "<table width=\"100%\">";
			echo "<tr>";
			echo "<th>Tegn(sm&aring;)</th>";
			echo "<th>Navn</th>";
			echo "<th>Html(no)</th>";
			echo "<th>Html(navn)</th>";
			echo "</tr>";
			while(1)
			{
				if(!$row)
					break;
				else 
				{
					if(!$annenhver)
					{
						echo "<tr class=\"tab_row1\">";
						$annenhver = 1;
					}
					else
					{ 
						echo "<tr class=\"tab_row2\">";
						$annenhver = 0;
					}
					echo "<td>".$row[5]."</th>";
					echo "<td>".$row[1]."</th>";
					echo "<td>".htmlspecialchars($row[4])."</th>";
					echo "<td>".htmlspecialchars($row[5])."</th>";
					echo "</tr>";
					$row = mysql_fetch_row($result);
				}	
			}
		}
		$result = mysql_query($query, $mysql_connection);
		$row = mysql_fetch_row($result);
		if($row)
		{
			echo "<tr>";
			echo "<th>Tegn(store)</th>";
			echo "<th>Navn</th>";
			echo "<th>Html(no)</th>";
			echo "<th>Html(navn)</th>";
			echo "</tr>";
			while(1)
			{
				if(!$row)
					break;
				else 
				{
					if(!$annenhver)
					{
						echo "<tr class=\"tab_row1\">";
						$annenhver = 1;
					}
					else
					{ 
						echo "<tr class=\"tab_row2\">";
						$annenhver = 0;
					}
					echo "<td>".$row[7]."</th>";
					echo "<td>".$row[1]."</th>";
					echo "<td>".htmlspecialchars($row[6])."</th>";
					echo "<td>".htmlspecialchars($row[7])."</th>";
					echo "</tr>";
					$row = mysql_fetch_row($result);
				}	
			}
		}
		echo "</table>";
		mysql_free_result($result);
		include 'nscdb.php';
	}


?>