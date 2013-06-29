<?php
	header('Content-language: nb-NO');	
//	header('Content-type: text/html; charset=iso-8859-1');
	header('Content-type: text/html; charset=UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Skjema behandlet</title>
<link href="css/index.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div class="container">
  <div class="content">
    <h1>Skjema behandlet</h1>
    
        <?php
	$todays_charset = "utf-8";
	$conn = NULL;
	include 'config.php';
	include 'opendb.php';
	mysql_set_charset($todays_charset, $conn);
	$method = $_SERVER['REQUEST_METHOD'];
	
	if($method == 'POST')
	{
		$result = "";
		$forslag = $_POST['txt_forslag'];
		$epost = $_POST['epost'];
		$user = $_POST['user'];
		$forklaring = $_POST['forklaring'];
		//we have to test values in case there's some refresh-cases where there is a problem with caheing etc. Therefore we have to test and see if 3 values allready exist in database.
		//These 3 values should make out a unique value. I think I solved the problem by nullifying the v values at the end of the script. Det hjalp ikke..
		
		if(!$forslag || !$epost || !$user)
		{
			echo "<p><div class=\"msg_error\">Fyll ut alle feltene..</div><br />";
			include 'closedb.php';
			echo "<a href=\"http://norskes&oslash;k.no\">http://norskes&oslash;k.no</a></p>";
		}
		else
		{
		/*	$test_query = "select forslags_txt, forklaring from innhold where forslags_txt='".$forslag."' && forklaring='".$forklaring."'";
			$result2 = mysql_query($test_query);
			if($result2)
			{
				mysql_free_result($result);
				include 'closedb.php';
				exit(1);
			}
		*/	$handle = fopen("/proc/sys/kernel/random/uuid", "r");
			if(!$handle)
			{
				echo "<div class=\"msg_error\">Could not open random-number-file...</div><br />\n";
				exit("<div class=\"msg_error\">Exiting with error from the script...</div>");
			}
			else
			{
				$random_string = fgets($handle, 37);
				fclose($handle);
			//	echo "Rand: ".$random_string."<br />\n";
				$link = "http://norskesøk.no/check.php?idforslag=";
				$full_link = $link.$random_string;
				$message = "Vær vennlig å klikk på linken nedenfor: \n".$full_link."\nMvh,\nnorskesøk.no";
				$headers = 'From: webmaster@xn--norskesk-c5a.no' . "\r\n" .
						'Reply-To: frode.meek@gmail.com' . "\r\n" .
						'X-Mailer: PHP/' . phpversion();
				
			}
			$query = "insert into innhold (forslags_txt, epost, visningsnavn, forklaring, public_key, bvist) values ('".
					$forslag."', '".
					$epost."', '".
					$user."', '".
					$forklaring."', '".
					$random_string."', '".
					"0')";
		//	echo "Query: ".$query."<br />\n";
			$result = mysql_query($query);
			if(!result)
			{
				 echo mysql_error($conn);
				 include 'closedb.php';
				 exit("<div class=\"msg_error\">mysql-error...exiting script</div>");
			}
			else
			{
				//echo "Query was successfull";
				mail($epost, "Bekreftelse", $message, $headers);
				echo "<p>En mail har blitt sendt til deg. Klikk p&aring; linken og ditt forslag vil bli synlig p&aring; forsiden.<br />";
				echo "G&aring; tilbake til <a href=\"http://norskes&oslash;k.no\">hovedside.</a></p>";
			}
		}
		if($result)
			mysql_free_result($result);
		$query = NULL;
		$forslag = NULL;
		$epost = NULL;
		$user = NULL;
		$forklaring = NULL;
		$forslag = NULL;
	}
	include 'closedb.php';
	
?>

    <!-- end .content --></div>
  <!-- end .container --></div>
</body>
</html>
