

<?php
	error_reporting(E_ALL);
	$array = array('sqrt','sinh','pi', 'deg2rad','tan');
	$bcheck = 1;
	function check_string($p) 
	{
		global $array;
		$not_true = 1;
		$i = strlen($p);
		$j = 0;
		$k = 0;
		while($j < $i)
		{
			if((($p[$j] > 'a') && ($p[$j] < 'z')) || (($p[$j] > 'A') && ($p[$j] < 'Z')))
			{
				while(1)
				{
					if($p[$j] == '(')
					{
						$streng[$k] = NULL;
						$fu = implode("", $streng);
						if(array_search($fu, $array) === FALSE)
						{
							echo $fu." is not a legal function. <br />";
							$not_true = 0;
						}
						$k = 0;
						$streng = NULL;
						break;
					}
					$streng[$k++] = $p[$j++];
				}
			} 
			if(!$p[$j])
				break;
			$j++;
		}
		return $not_true;
	}
	$xpress = "";	
	$method = $_SERVER['REQUEST_METHOD'];	
	if($method == 'POST')
	{
		
		$xpress = $_POST['expression'];
		
		if(!$bcheck = check_string($xpress))
		{
			echo("<b>Unvalid functions in input...no calculations will be done</b>"); 
		}
			
	}
	
	echo "<form name=\"kalkulator\" method=\"post\" action=\"calc.php\">";
	echo "<input type=\"text\" name=\"expression\"  size=\"50\" value=\"".$xpress."\" />";
	echo "<input type=\"submit\" name=\"sub_expression\"  size=\"50\" value=\"Kalkuler\" />";
	echo "</form>";
		
	$method = $_SERVER['REQUEST_METHOD'];
	if($method == 'POST')
	{
		if($bcheck) {
		$xpress = $_POST['expression'];
		$pf = fopen("/tmp/tmp.php", "w");
		$fx = "\$expression=".$xpress.";\n";
		fwrite($pf, "<?php\n");
		fwrite($pf, $fx);
		fwrite($pf, "echo \"<h1>\".\$expression.\"</h1>\";\n");
		fwrite($pf, "?>\n"); 
		fclose($pf);
		
		include "/tmp/tmp.php";
		}
	}
	
	

?>