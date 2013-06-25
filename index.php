<?php
	header('Content-language: nb-NO');	
//	header('Content-type: text/html; charset=iso-8859-1');
	header('Content-type: text/html; charset=UTF-8');
	if(isset($_GET['menuitem']))
	{
		if(($_GET['postnummer'] != NULL) || ($_GET['poststed'] != NULL))
		{
			//echo "Vi er øverst og skal vel egentlig ikke gjøre så mye <br />\n";		
		}
		else if($_GET['menuitem'] == 6)
		{
			if(isset($_GET['velg_fylke'])) 
		 	{
		 		setcookie("fylke", $_GET['velg_fylke'],0);
		 	}
		 	elseif(isset($_GET['velg_kommune']))
		 	{
		 		 setcookie("kommune", $_GET['velg_kommune'],0);
		 	}
		 	elseif(isset($_GET['velg_poststed']))
		 	{
		 		if(isset($_COOKIE['kommune']))
		 		{
		 			$poststed = $_GET['velg_poststed'];
		 			$fylke = $_COOKIE['fylke'];
		 			$kommune = $_COOKIE['kommune'];


		 			//echo $_COOKIE['fylke'].", ".$_COOKIE['kommune'].", ".$poststed."<br />\n";
		 		}
		 	}
		 }
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="no" lang="no">
<head>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />



<?php
	if($method = $_SERVER['REQUEST_METHOD']) 
	{
		$menutype = 1;
		$menuitem = 1;
		if($method == 'GET')
		{
			if(isset($_GET['mtype']))
			{
				$menutype = $_GET['mtype'];
				$menuitem = $_GET['menuitem'];
			}
			$todays_charset = "utf-8";
			include 'nscfg.php';
			include 'nsodb.php';
			mysql_set_charset($todays_charset, $mysql_connection);
			$query = "SELECT menuitem.ptitle, menuitem.description, menuitem.keywords
						FROM menuitem, mtitle, menu, mtype
						WHERE menuitem.idmenu=".$menuitem;
			$result = mysql_query($query, $mysql_connection);
 		 	$row = mysql_fetch_row($result);
 		 	echo "<meta name=\"description\" content=\"".$row[1]."\" />";
 		 	echo "<meta name=\"keywords\" content=\"".$row[2]."\" />";
			echo "<title>".$row[0]."</title>";
//api-keys are old and outdated. Disable for now
/*			if($menuitem == 6) // if posten we want to load the google map api's and other stuff..
			{
				echo "<script src=\"http://maps.googleapis.com/maps/api/js?v=3&amp;lang=no&amp;sensor=false&amp;key=ABQIAAAAWafN0cFdZdY7toV0Kkx_6hT_G3qCK4TGByXw74x9_IP7DnMe_hQCscdTxDzmeU3vru4U9s9CoR6lWQ\" type=\"text/javascript\"></script>\n";
				echo "<script type=\"text/javascript\" src=\"skript/gmap.js\"></script>\n";
			}
*/			echo "<script language=\"javascript\"><!--\n";
			echo "	function postSubmit()
						{
							document.postnummer.submit();
						}
						function openNew(link)
						{
							if(link!=0)
							{
								window.open(link, '_blank');
							}
						}
						
		--></script>";
			mysql_free_result($result);
		}
		else
		{
			echo "<title>Norske S&oslash;k</title>";
		}


	}
	else
	{
		echo "<title>Norske S&oslash;k</title>";
	}	
	include 'nscdb.php';
?> 

<link href="css/index.css" rel="stylesheet" type="text/css" />	

<style type="text/css">
body {
	background-color: #699;
}
</style>


</head>

<body>

<div class="bckgrnd1">
  
  <div id="toplogo">Norske S&oslash;k</div>
  <div id="toppic"></div>
  <?php
	$todays_charset = "utf-8";
	include 'nscfg.php';
	include 'nsodb.php';
	mysql_set_charset($todays_charset, $mysql_connection);
	$query = "SELECT menuitem.item_name, mtitle.mtitle_text, menuitem.idmenu, mtype.idmtype, menuitem.ptitle
				FROM menuitem, mtitle, menu, mtype 
				WHERE mtype.mtname='msearch' && menu.mtitle_idmtitle=mtitle.idmtitle && menu.mtype_idmtype=mtype.idmtype && menuitem.idmenu=menu.menuitem_idmenu";
 		$result = mysql_query($query, $mysql_connection);
 		 $row = mysql_fetch_row($result);
  echo "<div class=\"left_menu\">";
    echo $row[1]; 
    echo "<hr />";
    echo "<ul class=\"menu\">";
    while($row)
    {
    	echo "<li><a href=\"index.php?mtype=".$row[3]."&"."menuitem"."=".$row[2]."\" title=\"".$row[4]."\">".$row[0]."</a></li>"; 
		$row = mysql_fetch_row($result);
    }
    echo "</ul>";
  echo "</div>";
  echo "<div class=\"left_menu_konv\">";
  $query = "SELECT menuitem.item_name, mtitle.mtitle_text, menuitem.idmenu, mtype.idmtype, menuitem.ptitle
				FROM menuitem, mtitle, menu, mtype 
				WHERE mtype.mtname='mconvert' && menu.mtitle_idmtitle=mtitle.idmtitle && menu.mtype_idmtype=mtype.idmtype && menuitem.idmenu=menu.menuitem_idmenu";
 		$result = mysql_query($query, $mysql_connection);
 		 $row = mysql_fetch_row($result);
    echo $row[1]; 
    echo "<hr />";
    echo "<ul class=\"menu\">";
      while($row)
	    {
	      echo "<li><a href=\"index.php?mtype=".$row[3]."&"."menuitem"."=".$row[2]."\" title=\"".$row[4]."\">".$row[0]."</a></li>";
	      $row = mysql_fetch_row($result);
	    }
    echo "</ul>";
  echo "</div>";
  echo "<div class=\"left_menu_tabell\">";
    $query = "SELECT menuitem.item_name, mtitle.mtitle_text, menuitem.idmenu, mtype.idmtype, menuitem.ptitle
				FROM menuitem, mtitle, menu, mtype 
				WHERE mtype.mtname='mtables' && menu.mtitle_idmtitle=mtitle.idmtitle && menu.mtype_idmtype=mtype.idmtype && menuitem.idmenu=menu.menuitem_idmenu";
 		$result = mysql_query($query, $mysql_connection);
 		 $row = mysql_fetch_row($result);
    echo $row[1]; 
    echo "<hr />";
    echo "<ul class=\"menu\">";
      while($row)
	    {
	      echo "<li><a href=\"index.php?mtype=".$row[3]."&"."menuitem"."=".$row[2]."\" title=\"".$row[4]."\">".$row[0]."</a></li>";
	      $row = mysql_fetch_row($result);
	    }
    echo "</ul>";
  echo "</div>";
  
  echo "<div class=\"topmenupos\">";
  	echo "<ul class=\"menu-horz-ul\">";
echo "    <!-- når man legger på toppmeny-elementer må man huske å forandre \"width\" i css-skriptet silk at det ser bra ut -->";

	$query = "SELECT menuitem.item_name, mtitle.mtitle_text, menuitem.idmenu, mtype.idmtype, menuitem.ptitle
FROM menuitem, mtitle, menu, mtype 
WHERE mtype.mtname='topmenu' && menu.mtitle_idmtitle=mtitle.idmtitle && menu.mtype_idmtype=mtype.idmtype && menuitem.idmenu=menu.menuitem_idmenu";
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
				while($row) 
				{
					echo "<li class=\"list-menu-horz\"><a class=\"top_menu\" href=\"index.php?mtype=".$row[3]."&"."menuitem"."=".$row[2]."\" title=\"".$row[4]."\">".$row[0]."</a></li>\n";
					$row = mysql_fetch_row($result);
				}
			}
    	}
    	mysql_free_result($result);
   


		echo "</ul>";
	  	echo "</div>";
	  	
	  	/* setter inn min-kommune-søk her */
	  	global $valg_ferdig;
	 	echo "<div class=\"minkommune\">\n";
	 	echo "Min Kommune<br />\n";
	 	echo "<hr />\n";
	 	include('rmenu_minkommune.php');
	 	echo "</div>\n"; 
	 	/* slutt min-kommune-meny */
	 	include 'nscfg.php';
		include 'nsodb.php';
	  	echo "<div class=\"rmenu_news\">";
	   $query = "SELECT menuitem.item_name, mtitle.mtitle_text, menuitem.idmenu, mtype.idmtype, menuitem.ptitle
					FROM menuitem, mtitle, menu, mtype 
					WHERE mtype.mtname='mnews' && menu.mtitle_idmtitle=mtitle.idmtitle && menu.mtype_idmtype=mtype.idmtype && menuitem.idmenu=menu.menuitem_idmenu
					ORDER by menuitem.item_name ASC";
	 	$result = mysql_query($query, $mysql_connection);
	 	$row = mysql_fetch_row($result);
	   echo $row[1]; 
	   echo "<hr />";
	   echo "<ul class=\"menu\">";
      while($row)
	    {
	      echo "<li><a href=\"index.php?mtype=".$row[3]."&"."menuitem"."=".$row[2]."\" title=\"".$row[4]."\">".$row[0]."</a></li>";
	      $row = mysql_fetch_row($result);
	    }
    echo "</ul>";
  echo "</div>";
  	echo "<div class=\"bckgrnd_main\">";
  
  	if($method = $_SERVER['REQUEST_METHOD']) 
  	{
		if($method == 'GET')
		{
			if(isset($_GET['mtype']))
				$menutype = $_GET['mtype'];
			if(isset($_GET['menuitem']))
				$menuitem = $_GET['menuitem'];
			if($menuitem)
			{
				$query = "select menuitem.maction
								from menuitem
								where menuitem.idmenu=$menuitem";
				$result = mysql_query($query, $mysql_connection);
				if($result)
				{
 		 			$row = mysql_fetch_row($result);
 		 			mysql_free_result($result);
 		 			include('nscdb.php');
 		 			if($row)
 		 			{
 		 				switch($menuitem) 
 		 				{
 		 				case 6:
 		 					//if($valg_ferdig == TRUE)//bare hvis alle valg er blitt gjort
 		 						include("$row[0]");
 		 					break;
 		 				default:
 		 					include("$row[0]");
 		 					break;
 		 				}
 		 			/*	$action = "php ".$row[0];
 		 				exec($action, $action_result, $ret_val);
 		 				if($ret_val == 0) //execution of php-script returned no errors 
 		 				{
 		 					foreach($action_result as $string)
 		 						echo $string;
 		 				}
 		 				else 
 		 				{
 		 					echo "<h3>Action(\'".$action."\') failed...</h3>\n";
 		 				}*/
 		 			}
 		 			else 
 		 			{
 		 				echo "<h3>mysql_fetch_row failed.</h3>\n";
 		 			}
 		 		}
 		 		else
 		 		{
 		 			echo "<h3>mysql_query failed- </h3><br />\n query: $query";
 		 		}
			}
			else 
			{
				include("menu_item_hjem.php");
			}

		}
		
	
		if($method == 'POST') //someone is posting a search...therefore we can centrate all text with
										//with a css-class...echo the div-class...
		{
			$menuid = $_POST['search_type'];
			
			switch($menuid)
			{
			case 1://gulesider soek
				echo "<div class=\"centrate\">";
				echo "<p>";
				readfile("lmenu_gulesider.html");
				include 'gulesok.php';
				echo "</p>";
				echo "</div>";		
				break;
			case 3://valuta konvertering/søk
				include 'lmenukonv_valuta.php';
				include 'valuta_handle.php';
				break;
			case 4:
				include 'lmenukonv_enheter.php';
				break;
			default:
				include("menu_item_hjem.php");
				break;
			}
			
		}
	}
	else 
	{
		include("menu_item_hjem.php");
	}
	   
  ?>
    
</div><!-- end div class bck_main -->
  <!--
   <div class="bckgrnd_main">P&aring; disse sidene kan du s&oslash;ke pa all den informasjon som man skulle trenge samlet p&aring; en plass. Nyttig er selvf&oslash;lgelig relativt. Disse sidene er reklame-fri. Dette er noe jeg har laget fordi dette er noe jeg ikke har funnet p&aring; nettet enn&aring;. Dvs forskjellige s&oslash;keverkt&oslash;y p&oslash; en plass. De st&oslash;rste leverand&oslash;rene som Google har veldig gode s&oslash;kemotorer men det er ikke slikt jeg savner. Jeg savner praktisk informasjon servert rett foran meg uten at det skal ta over enten Skrivebordsplassen eller meny-plassen p&aring; nettleseren.
  </div>
  -->
  

</div>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-10328293-2']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</body>
</html>
