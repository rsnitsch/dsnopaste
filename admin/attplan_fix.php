<pre>
<?php
	/* BESCHREIBUNG:
	Berechnet die Laufzeiten und Ankunftsdaten aller Angriffsplan-Aktionen neu!
	*/
	error_reporting(E_ALL);
	
	define('INC_CHECK', TRUE);
	define('SSQL_INC_CHECK', TRUE);
	define('CFG_UPLOADED', TRUE);
	
	$root_path = '../';
	include($root_path.'include/config.inc.php');
	if (!enableMySQL(true)) {
		print_r($debuginfo);
		die("enableMySQL failed");
	}
	
	$all = $mysql->sql_query('SELECT * FROM attplans_actions');
	
	if($all)
	{
		while($row = $mysql->sql_fetch_assoc($all))
		{
			// erstmal den Server ermitteln
			$server=$mysql->sql_result($mysql->sql_query('SELECT server FROM attplans WHERE id='.$row['attplan_id']), 0, 'server');
			
			// ALTE METHODE
			$units=array(
				'spear' => $row['spear'],
				'sword' => $row['sword'],
				'axe' => $row['axe'],
				'spy' => $row['spy'],
				'light' => $row['light'],
				'heavy' => $row['heavy'],
				'ram' => $row['ram'],
				'catapult' => $row['catapult'],
				'snob' => $row['snob']
			);
			$timeperfield=getTimePerField($units, $server);
			$runtime=calcRuntime($row['from'], $row['to'], $timeperfield);
			$senddate=$row['arrive']-$runtime;
			
			$query = 'UPDATE attplans_actions SET runtime='.$runtime.', senddate='.$senddate.' WHERE id='.$row['id'];
			$success = $mysql->sql_query($query);
			if($success)
			{
				echo "Aktion mit der ID ".$row['id']." erfolgreich aktualisiert.\n";
			}
			else
			{
				echo "Aktion mit der ID ".$row['id']." NICHT aktualisiert!!! SQL-Fehler: ".$mysql->lasterror."\n";
			}
		}
	}
	else
	{
		echo "mysql error: ".$mysql->lasterror;
	}
?>
</pre>
<?php
	// diese Funktion berechnet, wie lange ein Trupp von einem Dorf zu einem anderen braucht
	function calcRuntime($from, $to, $timeperfield, $speed=1)
	{
		// die Start- und Zielkoordinate können wahlweise als String oder bereits als "Arraykoordinate" übergeben werden...
		if(!is_array($from))
			$from=getCoord($from);
		if(!is_array($to))
			$to=getCoord($to);
			
		$distance=calcDistance($from, $to);
		
		$time=$distance * $timeperfield;
		
		return $time * $speed;
	}
	
	// diese Funktion berechnet die Laufzeit für ein Feld anhand der Truppen... ($units, assoziatives Array)
	function getTimePerField($units, $server=6)
	{
		$time=0;
		$runtimes=getRuntimes((string) $server);
		
		// die langsamste Einheit ermitteln
		foreach($units as $name => $einheit)
		{
			if($einheit > 0)
			{
				if($runtimes[$name] > $time)
					$time = $runtimes[$name];
			}
		}
		
		return $time*60; // sekunden zurückgeben
	}
	
	// diese Funktion liefert ein assoziatives Array mit den Laufzeiten der Einheiten eines Servers zurück
	function getRuntimes($server)
	{
		$runtimes=FALSE;
		
		if($server=='1' or $server=='2' or $server=='c')
		{
				$runtimes=array(
					'spear' => 12,
					'sword' => 15,
					'axe' => 12,
					'spy' => 6,
					'light' => 6.5,
					'heavy' => 7.5,
					'ram' => 20,
					'catapult' => 24,
					'snob' => 30
				);
		}
		elseif($server=='3')
		{
				$runtimes=array(
					'spear' => 12,
					'sword' => 15,
					'axe' => 12,
					'spy' => 6,
					'light' => 6.5,
					'heavy' => 7.5,
					'ram' => 20,
					'catapult' => 20,
					'snob' => 30
				);
		}
		elseif($server=='4')
		{
				$runtimes=array(
					'spear' => 12,
					'sword' => 15,
					'axe' => 12,
					'spy' => 6,
					'light' => 6.5,
					'heavy' => 7.5,
					'ram' => 20,
					'catapult' => 24,
					'priest' => 15,
					'snob' => 30
				);
		}
		elseif($server=='5' or $server=='6' or $server=='7' or $server=='8')
		{
				$runtimes=array(
					'spear' => 18,
					'sword' => 22,
					'axe' => 18,
					'spy' => 9,
					'light' => 10,
					'heavy' => 11,
					'ram' => 30,
					'catapult' => 30,
					'snob' => 35
				);
		}
		elseif($server=='9')
		{
				$runtimes=array(
					'spear' => 12,
					'sword' => 15,
					'axe' => 12,
					'spy' => 6,
					'light' => 6.5,
					'heavy' => 7.5,
					'ram' => 20,
					'catapult' => 20,
					'snob' => 30
				);
		}
		
		return $runtimes;
	}
	
	
	// diese Funktion berechnet die Entfernung zwischen 2 Dörfern
	// sie erwartet als Argumente zwei X/Y-Koordinaten!!
	function calcDistance($from, $to)
	{
		// die Start- und Zielkoordinate können wahlweise als String oder bereits als "Arraykoordinate" übergeben werden...
		if(!is_array($from))
			$from=getCoord($from);
		if(!is_array($to))
			$to=getCoord($to);
			
		$distance=sqrt(pow($from['x']-$to['x'],2) + pow($from['y']-$to['y'],2));
		
		return $distance;
	}
	
	// diese Funktion gibt ein assoziatives Array mit den Keys 'x' und 'y' zurück. x und y werden aus einer Koordinate (String) extrahiert
	function getCoord($str)
	{
		$matches=FALSE;
		$result=array('x' => 0, 'y' => 0);
		
		if(preg_match('/([0-9]{1,3})\|([0-9]{1,3})/',$str,$matches))
		{
			$result['x']=$matches[1];
			$result['y']=$matches[2];
		}
		elseif(preg_match('/([0-9]{1,3}):([0-9]{1,3}):([0-9]{1,3})/',$str,$matches))
		{
			$result = convert_coords_to_xy($matches[1], $matches[2], $matches[3]);
			/*
			if($cfg["debugmode"])
			{
				echo 'converted xy-coordinates: x => '.$result['x'].', y => '.$result['y'];
			}
			*/
		}
		else
		{
			trigger_error('invalid coordinate: '.$str);
			return FALSE;
		}
		
		return $result;
	}
	
	// diese Funktion gibt ein assoziatives Array mit den Keys 'x' und 'y' zurück. x und y werden aus einer Kontinentalkoordinate berechnet
	// (leicht abgeändert übernommen aus http://wiki.die-staemme.de/wiki/Koordinatenberechnungen#Kontinent-System_.28Server_3.2C_Server_4.2C_Server_5.29_zum_xy-System_.28Server_1.2C_Server_2.29)
	function convert_coords_to_xy($con, $sec, $sub)
	{
		if($con < 0 || $con > 99 || $sec < 0 || $sec > 99 || $sub < 0 || $sub > 24) {
			trigger_error('invalid x:y:z coordinate: '.$str);
			return false;
		}
		
		$x = ($con % 10) * 50 + ($sec % 10) * 5 + ($sub % 5);
		$y = floor($con / 10) * 50 + floor($sec / 10) * 5 + floor($sub / 5);
		return array('x' => $x, 'y' => $y);
	}
?>