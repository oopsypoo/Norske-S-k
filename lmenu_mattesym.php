<p>Matematiske symboler er viktig i alle former for beskrivelser. Her kan dere se/finne html-entiteten for de forskjellige symbolene. <br />
	Desverre s&aring; har jeg ikke f&aring;tt kommet s&aring; langt at jeg har laget en norsk beskrivelse. Men det skal komme etterhvert.
</p>

<h3>Matematiske Symboler</h3>
<br />
<?php
	//$todays_charset = "UTF-8";
	include 'nscfg.php';
	include 'nsodb.php';
//	mysql_set_charset($todays_charset, $mysql_connection);
	$query = "select e_no,e_navn,beskrivelse from matte_symbol";
	
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
			echo "<th>Html(no)</th>";
			echo "<th>Html(navn)</th>";
			echo "<th>Beskrivelse</th>";
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
					echo "<td>".$row[0]."</th>";
					echo "<td>".htmlspecialchars($row[0])."</th>";
					echo "<td>".htmlspecialchars($row[1])."</th>";
					echo "<td>".$row[2]."</th>";
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