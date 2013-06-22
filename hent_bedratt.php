<?php
  	$todays_charset = "utf-8";
  
  	include 'config.php';
	include 'opendb.php';
	mysql_set_charset($todays_charset, $conn);
	
	$query = "select visningsnavn, forslags_txt, forklaring, dato, ferdig from innhold where bvist='1' order by dato desc";
	$result = mysql_query($query);
	if(!$result)
		echo "<div class=\"msg_error\">".mysql_error($conn)."</div>";
	$row = mysql_fetch_row($result);
	if($row)
	{
		echo "<table class=\"table_header\">";
		echo "<tr>";
		echo "<td>Dato</td>";
		echo "<td>Bruker</td>";
		echo "<td>Forslag</td>";
		echo "<td>Forklaring</td>";
		echo "<td>Ferdig?</td>";
		echo "<tr>";
		$annenhver = 0;
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
				echo "<td class=\"table_date\">".$row[3]."</td>";
				echo "<td>".$row[0]."</td>";
				echo "<td>".$row[1]."</td>";
				echo "<td>".$row[2]."</td>";
				echo "<td>".$row[4]."</td>";
				echo "</tr>";
				$row = mysql_fetch_row($result);
		}
		echo "</table>";
	}
	if($result)
		mysql_free_result($result);
	include 'closedb.php';
?>
