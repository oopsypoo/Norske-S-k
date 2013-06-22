<?php

	
	//get simple html-dom-interface/(functions
	//Å bruke norske bokstaver går fint så lenge fil og sendt data er i iso-8859-1 format
	//we want to make $latitude and longitude global since we want to use these later on
	//for finding the location on the google-map. It's more precise than using the name of the location.
	//
	/* start classes for table showing data css-classes*/
	$table_class_vaervarsel = "table_class_vaervarsel";
	$tr_soloppgang ="tr_soloppgang";
	$tr_seperator = "tr_seperator";
	$tr_vaervarsel = "tr_vaervarsel";
	$second_row_class = "second_row_class";
	$first_row_class = "first_row_class";
	/* end classes for table showing data */
	
	$test_local = 0;
	//antall dager vi vil ha værvarsel for.
	$antall_dager = 3;
	global $valgt_fylke;
	global $valgt_kommune;
	global $valgt_poststed;
	/* bare sett noen default-verdier  hvis de ikke er satt*/
	if(!isset($valgt_fylke))
		$valgt_fylke = 'Østfold';
	if(!isset($valgt_kommune))
		$valgt_kommune = 'Sarpsborg';
	if(!isset($valgt_poststed))
		$valgt_poststed = 'Greåker';
	
	/*	Cache stuff here--some should be global params */
	$max_cache_age = 1200; //in seconds. 20min
	$cache_dir = "/tmp/yr_cache/";
	/* End cache-stuff   */
	function get_yr_date($date) //returns the date from the date-time string
	{
		if(!$date)
			return NULL;
		else
			return substr($date, 0,strpos($date, 'T'));
	}
	function get_hour($time) //returns the hour in time(two digits)
	{
		if(!$time)
			return NULL;
		else
			return substr($time, 0,-6);
	}
	function get_yr_time($date)//returns the time-part of the date-time format
	{
		if(!$date)
			return NULL;
		else
			return substr(strstr($date,'T'),1);
	}
	class ns_yr
	{
		var $file = '';
		var $root = '';
		var $sun = array('date' => '','rise' => '', 'set' => '');
		var $weather_data = ''; //this array holds all data according to how many days we want
		var $ht = '';
		var $symbol_path = 'http://fil.nrk.no/yr/grafikk/sym/b38/';
		var $cache_ready = false;
		var $create_cache = false;
		var $hash_file = '';
		//this is the first thing we do before anything else. Maybe we can save time
		//this function is called from init_dom. If we can use cache just get it and leave the function		
		private function check_cache()
		{
			global $valgt_poststed;
			global $valgt_kommmune;
			global $valgt_fylke;
			global $cache_dir;
			global $max_cache_age;
			if(!file_exists($cache_dir))
			{
				$mkd = mkdir($cache_dir);
				if(!$mkd)
				{
					echo "$cache_dir does not exist/could not be created. It is recommended to create this directory<br />\n
							It is recommended to create and give the appropriate access to the directory for faster access<br />\n";
					return 0;
				}
			}
			else 
			{
				$hash_string = md5(strtolower($valgt_poststed.$valgt_kommmune.$valgt_fylke));
				$this->hash_file = $cache_dir.$hash_string;
				if(file_exists($cache_dir.$hash_string))
				{
					date_default_timezone_set('Europe/Oslo');
					$p = getdate();
					$cur_date = $p[0]; //unix time
					$filecreated = filemtime($this->hash_file);
					$diff = $cur_date - $filecreated;
					//echo "cur_date: ".$cur_date." filecreated: ".$filecreated."defference is ".$diff."<br />\n";
					if($diff < $max_cache_age) //if this is the case then we can get the data.
					{
						$this->cache_ready = true;
						$this->ht = file_get_contents($this->hash_file);
						//echo "We will now get contents from cache...<br />\n";
						return 1;
					}
				}
				$this->create_cash = true;
				return 2;
			}
			return 0;
		}
		private function set_url()
		{
			global $test_local;
			global $valgt_fylke;
			global $valgt_kommune;
			global $valgt_poststed;
			
			$part_url ='http://www.yr.no/sted/Norge';
			$valgt_kommune = preg_replace('/ /','_',$valgt_kommune);
			$valgt_poststed = preg_replace('/ /','_',$valgt_poststed);
			$valgt_fylke = preg_replace('/ /','_',$valgt_fylke);
			$yr_url = $part_url."/".$valgt_fylke."/".$valgt_kommune."/".$valgt_poststed."/varsel.xml"; 
			$local_file = "yr_xml_data.xml";
			if($test_local)
				$this->file = $local_file;
			else 
				$this->file = $yr_url;
		}
		private function init_dom() 
		{
			$pf = $this->check_cache();
			if(!$pf)
			{
				echo "This script is based on caching data-contents...please contact adm for help..<br />\n";
				return NULL;
			}
			else if($pf == 2) //the cache-file is outdated or does not exist ...do all the stuff
			{
				$this->set_url();
				if(include '../includes/simplehtmldom/simple_xml_yr_dom.php')
				{
					//echo "from init_dom: helo<br />\n";
					//first test is to use full url. Else we're going to use local file. (Set to 1 for local)/////
					$xml = file_get_html($this->file);
					if(!$xml)
					{
						echo "Failed getting xml-data from: ".$this->file."<br />\n";
						echo "Getting resource from file_get_html failed...exiting";
						return NULL;
					}
					else
					{
						$weather = $xml->find('weatherdata', 0);
						if(!$weather)
						{
							echo "File we tried to get: ".$this->file." <br />\n";
							exit("Failed getting root of document: '<weatherdata>'");
						}
						return $weather;
					}
				}
				else 
				{
					echo "Failed including '../frodemeek.org/includes/simplehtmldom/simple_xml_yr_dom.php'<br />\n";
					return NULL;
				}
			}
			else if($pf == 1)
				return 1;
		}
		//sets latitude and longitude
		private function set_xy()
		{
			global $coord;
			
			//Get location info
			$location = $this->root->find('location',0);
			//we're only interested in the altitude and longitude data
			$coord[0] = $location->find('location',0)->getAttribute('latitude');
			if(!$coord[0])
			{
				echo "Could not get latitude data..error<br />\n";
				var_dump($coord[0]);
			}
			$coord[1] = $location->find('location',0)->getAttribute('longitude');
			if(!$coord[1])
			{
				echo "Could not get longitude data..error<br />\n";
				var_dump($coord[1]);
			}
		}
		//gets and sets sunrise and sunset time
		private function set_sun()
		{
			if(!$this->root)
				exit("Root's not set cannot go on anymore: set_sun()<br>\n");
			else 
			{
				$rc = $this->root->find('sun',0);
				if(!$rc)
					echo "Could not set/find sun-tag<br />\n";
				else 
				{
					//the data-time format is: 'dateTtime'.
					$this->sun['date'] = get_yr_date($rc->getAttribute('rise'));
					$this->sun['rise'] = get_yr_time($rc->getAttribute('rise'));
					$this->sun['set'] = get_yr_time($rc->getAttribute('set'));
				}
			}
		}
		private function set_forecast()
		{
			global $antall_dager;
			$dayctr = 0;
			if(!$this->root)
				exit("Root's not set cannot go on anymore: set_sun()<br>\n");
			else 
			{
				$forecast_chunks = $this->root->find('forecast',0)->find('tabular',0)->find('time');
				if(!$forecast_chunks)
				{
					echo "Could not get/find forecast-data from xml-file(set_forecast())<br />\n";
				}
				else
				{
					//all children are time-tags...get all of them
					//$forecast_chunks->find('time');
					foreach($forecast_chunks as $chunk)//all days are divided into 4: 0000->0600, 0600->1200, 1200->1800, 1800->2400
					//these periods are denoted in the time-tag as period-attribute
					{
						$time = array('from' => $chunk->getAttribute('from'), 
						'to' => $chunk->getAttribute('to'), 
						'period' => $chunk->getAttribute('period'));
						 
						//now we get the real data...the forecast-data
						$tmp = $chunk->find('symbol',0);
						$symbol = array('number' => $tmp->getAttribute('number'),
											'name' => $tmp->getAttribute('name'), 
											'var' => $tmp->getAttribute('var'));
						unset($tmp);
						$tmp = $chunk->find('precipitation',0);
						$precipitation = array('value' => $tmp->getAttribute('value'),
											'min' => $tmp->getAttribute('minvalue'), 
											'max' => $tmp->getAttribute('maxvalue'));
						unset($tmp);
						$tmp = $chunk->find('windDirection',0);
						$winddir = array('deg' => $tmp->getAttribute('deg'),
											'code' => $tmp->getAttribute('code'), 
											'name' => $tmp->getAttribute('name'));
						unset($tmp);
						$tmp = $chunk->find('windSpeed',0);
						$windspeed = array('mps' => $tmp->getAttribute('mps'),
											'name' => $tmp->getAttribute('name'));
						unset($tmp);
						$tmp = $chunk->find('temperature',0);
						$temperature = array('unit' => $tmp->getAttribute('unit'),
											'value' => $tmp->getAttribute('value'));
						
						$this->weather_data[$dayctr][] = array('time' => $time,
																	'symbol' => $symbol,
																	'precipitation' => $precipitation,
																	'winddir' => $winddir,
																	'windspeed' => $windspeed,
																	'temperature' => $temperature,
																	);
						
						if($time['period'] == 3) //then the next chunk is a new day
						{
							$dayctr++;
							if($dayctr >= $antall_dager)
								break;
						}
					}
				}
			}
		}
		public function get_sun() //returns an array containing the rise and set values... 
		{
			return $this->sun;
		}
		public function get_forecast()
		{
			return $this->weather_data;
		}
		public function get_url()
		{
			return $this->file;
		}
		public function write_forecast()
		{
			//echo "Hello from write_forecast<br />\n";
			global $valgt_fylke;
			global $valgt_kommune;
			global $valgt_poststed;
			global $antall_dager;
			//css-classes...
			global $table_class_vaervarsel;
			global $tr_soloppgang;
			global $tr_seperator;
			global $tr_vaervarsel;
			global $second_row_class;
			global $first_row_class;
			
			if($this->cache_ready)
			{
				//echo "printing cache-stuff..<br>\n";
				echo $this->ht;
			}
			else
			{
				//7 columns
				$dayctr = 0;
				$this->ht = "
				<table class=\"$table_class_vaervarsel\">
					<tr>
					<th colspan=\"7\">V&aelig;rvarsel for ".utf8_encode($valgt_poststed).", ".utf8_encode($valgt_kommune).", ".utf8_encode($valgt_fylke)."<th>
					</tr>
					<tr class=\"$tr_soloppgang\">
						<th colspan=\"3\">Dato</th><th colspan=\"2\">Soloppgang</th><th colspan=\"2\">Solnedgang</th>
					</tr>
					<tr class=\"$tr_soloppgang\">
						<td colspan=\"3\">".$this->sun['date']."</td>
						<td colspan=\"2\">".$this->sun['rise']."</td>
						<td colspan=\"2\">".$this->sun['set']."</td>
					</tr>
					<tr>
						<td colspan=\"7\"><a href=\"".utf8_encode($this->get_url())."\">V&aelig;rdata hentet fra yr.no, levert av Meteorologisk institutt og NRK</a></td>
					</tr>
					<tr class=\"$tr_vaervarsel\">
						<th>Dato</th>
						<th>Fra-Til</th>
						<th>Sym</th>
						<th>Nedb&oslash;r</th>
						<th>Temp</th>
						<th>Vind</th>
						<th>Styrke</th>
					</tr>
					<tr class=\"$tr_seperator\">
						<td colspan=\"7\"><hr /></td>
					</tr>";
					
					while($dayctr < $antall_dager) 
					{
						$last_day = count($this->weather_data[$dayctr]);
						$ch = 0;
						foreach($this->weather_data[$dayctr] as $data)
						{
							if($ch % 2)
								$row_class = $second_row_class;
							else
								$row_class = $first_row_class;
							$this->ht .= "<tr class=\"$row_class\">
								<td>".get_yr_date($data['time']['from'])."</td>
								<td>".get_hour(get_yr_time($data['time']['from'])).
									"<br />".get_hour(get_yr_time($data['time']['to']))."</td>
								<td><img src=\"".$this->symbol_path.$data['symbol']['var'].".png\" /></td>
								<td>".$data['precipitation']['value']."mm</td>
								<td>".$data['temperature']['value']."&deg;</td>
								<td>".$data['windspeed']['name']."</td>
								<td>".$data['windspeed']['mps']."m/s</td>
							</tr>";
							$ch++;
							if($ch == $last_day)
								$this->ht .= "<tr class=\"$tr_seperator\"><td colspan=\"7\"><hr /></td></tr>";
						}
						$dayctr++;
					}
					
				$this->ht .= "</table><br />\n";
				//echo "<p><b>".var_dump($this->weather_data)."</b></p>";
				//
			echo $this->ht;
			file_put_contents($this->hash_file, $this->ht);
			}
		}
		// this is a public function that sets all arrays and variables...hopefully it does 
		//all that is required
		public function Init()
		{
			$this->root = $this->init_dom(); //returns and sets the root of the document;
			//echo $this->cache_ready.", ".$this->create_cache.", ".$this->hash_file."<br />\n";
			if(!$this->root)
			{
				echo "Could not set root of document..exiting..<br />\n";
				return NULL;
			}
			if(!$this->cache_ready)
			{
				//echo "Hello from Initif(!\$this->cache_ready)<br />\n";
				$this->set_xy();//set latitude longittue
				$this->set_sun();//set sunrise and set.
				$this->set_forecast(); //get forecast data..sets the weather_data-array
			}
			else 
			{
				//echo "hallo<br />\n";
			}
			return 1;
		}
		
	}
	$test = new ns_yr();
	if(!$test->Init())
		exit(1);
	$test->write_forecast();

?>
