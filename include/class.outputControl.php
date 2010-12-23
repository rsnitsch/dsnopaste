<?php
	/*
	@author: Robert Nitsch
	@copyright: (c) copyright Robert Nitsch, 2006
	@description:
	Eine Klasse zur besseren Steuerung der Ausgabe von PHP-Skripten.
	Anstatt die Daten direkt über echo an den Browser zu schicken, werden sie in diesem Objekt gespeichert.
	Daraus ergeben sich autom. viel mehr Möglichkeiten.
	Außerdem bietet die Klasse erste Ansätze von Templatefunktionen - die Funktionen addFile() und replaceVar() machen es möglich.
	@version: 1.0.0
	*/
	class outputControl {
		
		private $output;
		private $cache;
		private $cachedir;
		private $cachedout;
		private $cachemaxage;
		private $requestdata;
		
		// Konstruktor
		function outputControl()
		{
			$this->output = '';
			$this->cachedir = './';
			$this->cache=FALSE;
			$this->cachedout=FALSE;
			$this->cachemaxage=604800; // 1 Woche
			$this->requestdata='';
		}
		
		// === Methoden ===
		
		// fügt Daten hinzu
		function add($data)
		{
			$this->output .= $data;
		}
		
		// fügt Text in einem Paragraphen <p> hinzu
		function addP($data)
		{
			$this->output .= '<p>'.$data.'</p>';
		}
		
		// fügt die Daten in der Datei hinzu
		function addFile($path)
		{
			$file=fopen($path, 'r');
			
			$this->add(fread($file,filesize($path)));
			
			fclose($file);
		}
		
		// gibt die aktuellen Daten zurück
		function get()
		{
			return $this->output;
		}
		
		// überschreibt die aktuellen Daten mit den angegebenen
		function set($data)
		{
			$this->output = $data;
		}
		
		// gibt alle Daten aus
		// ...und falls das Caching aktiviert wurde, wird das Ergebnis in einer Datei im Cacheordner abgespeichert
		function output()
		{
			// wenn bereits der Cache ausgegeben wurde die Funktion abbrechen...
			if($this->cachedout) return;
			
			// die Datei cachen
			if($this->cache)
			{
				$cfile=fopen($this->cachedir.$this->getCacheFileName(),'w');
				fputs($cfile, $this->output);
				fclose($cfile);
			}
			
			// die Ausgabe ausgeben^^
			echo $this->output;
		}
		
		// setzt den aktuellen Cacheordner
		function setCacheDir($dir)
		{
			$this->cachedir=$dir;
		}
		
		// setzt das maximale Alter der gecachte Dateien
		// ist eine gecachte Datei älter als diese Angabe, dann wird die Ausgabe neu generiert und abgespeichert...
		function setCacheMaxAge($maxage)
		{
			$this->cachemaxage=$maxage;
		}
		
		function setRequestData($data)
		{
			$this->requestdata=$data;
		}
		
		// aktiviert das Cachen
		function enableCache()
		{
			$this->cache=TRUE;
		}
		
		// deaktiviert das Cachen
		function disableCache()
		{
			$this->cache=FALSE;
		}
		
		// Cachefunktion... falls dieser Aufruf bereits einmal getätigt wurde und das Ergebnis gecached wurde, wird dieses Ergebnis direkt ausgegeben...
		function cache()
		{
			if(!$this->cache) return FALSE;
			
			$cfile=$this->getCacheFileName();
			if(file_exists($this->cachedir.$cfile))
			{
				if(filemtime($this->cachedir.$cfile)+$this->cachemaxage > time()) // nur wenn die Datei nicht älter als das zulässige Maximalalter ist...
				{
					$filehandle=fopen($this->cachedir.$cfile,'r');
					echo fread($filehandle,filesize($this->cachedir.$cfile));
					echo "\n<!-- cached version -->";
					
					$this->cachedout=TRUE;
					
					return TRUE;
				}
			}

			return FALSE;
		}
		
		// diese Funktion bestimmt den Dateinamen für ein Cacheergebnis
		function getCacheFileName()
		{
			return basename($_SERVER['PHP_SELF']).'.'.short(md5($this->requestdata),6).'.cache';
		}
		
		// ersetzt einen Platzhalter (Syntax: %{platzhaltername}%) (in den bisherigen Daten!) durch den angegebenen Wert
		// Beispiel: 	$output->replaceVar('titel','OutputControl - Homepage');
		//			dann wird "%{titel}%" => "OutputControl - Homepage"
		function replaceVar($name, $value)
		{
			$this->output = str_replace('%{'.$name.'}%',$value,$this->output);
		}
		
		function replaceVar_File($name, $path)
		{
			$file=fopen($path, 'r');
			
			$this->replaceVar($name, fread($file,filesize($path)));
			
			fclose($file);
		}
		

	};
?>