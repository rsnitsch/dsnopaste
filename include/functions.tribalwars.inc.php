<?php
    class Gameworld {
        public $id;
        public $name;
        public $config;
        public $runtimes;
        public $unitnames;

        public function __construct($id)
        {
            if(!serverExists($id))
                throw new Exception("Server with id '$id' does not exist!");

            $this->id = $id;

            global $root_path, $cfg;
            $this->dir = $root_path."data/server/{$cfg['language']}/{$this->id}";

            $this->getData();
        }

        protected function getData()
        {
            $units = simplexml_load_file($this->dir."/units.xml");

            $this->runtimes = array();
            $this->unitnames = array();

            foreach($units as $unitname => $data) {
                if ($unitname == "militia") {
                    continue;
                }

                $this->runtimes[$unitname] = floatval($data->speed);
                $this->unitnames[] = $unitname;
            }

            $this->config = simplexml_load_file($this->dir."/config.xml");

            if($this->hasMetafile()) {
                $meta = simplexml_load_file($this->dir."/meta.xml");
                $this->name = $meta->name;
            } else {
                $this->name = "Welt {$this->id}";
            }
        }

        public function getConfig() {
            return $this->config;
        }

        public function hasMetafile()
        {
            return file_exists($this->dir."/meta.xml");
        }

        public function coordSystem()
        {
            return 'modern';
        }

        public function bonusNew() {
            return $this->config->coord->bonus_new == 1;
        }

        public function getSpeed() {
            return floatval($this->config->speed);
        }

        // gibt das Fassungsverm�gen des Verstecks zur�ck auf der jeweiligen Stufe
        public function hideMax($level) {
            switch($this->id) {
                case 'de34':
                case 'de52':
                    $data = array(0, 100, 135, 183, 247, 333, 450, 608, 822,  1110, 1500);
                    break;
                default:
                    $data = array(0, 150, 200, 267, 356, 474, 632, 843, 1125, 1500, 2000);
            }

            return $data[$level];
        }

        // berechnet das Fassungsverm�gen des Speichers
        function calcStorageMax($level) {
            switch($this->id) {
                case "de34":
                case "de52":
                    $data = array(1000,   1230,   1513,   1861,  2289,  2815,   3463,   4259, 5239, 6444,
                                  7926,   9749,   11991,  14749, 18141, 22314,  27446,  33759,
                                  41523,  51074,  62821,  77269, 95041, 116901, 143788, 176859,
                                  217537, 267570, 329112, 404807);
                    break;
                default:
                    $data = array(1000,   1229,   1512,   1859,  2285,  2810,   3454,   4247, 5222, 6420,
                                  7893,   9705,   11932,  14670, 18037, 22177,  27266,  33523,
                                  41217,  50675,  62305,  76604, 94184, 115798, 142373, 175047,
                                  215219, 264611, 325337, 400000);
            }

            return $data[$level-1];
        }

        // gibt die Minenproduktion zur�ck auf der jeweiligen Stufe
        function calcMineProduction($level) {
            switch($this->id) {
                case 'de34':
                    $data = array(5,    30,  34,  40,  46,  52,  60,  69,   80,  92, 106,
                                  121,  140, 161, 185, 212, 244, 281, 323,  371,
                                  427,  491, 565, 649, 747, 859, 988, 1136, 1306,
                                  1502, 1727);
                    break;
                case 'de52':
                    $data = array(5,    15,  18,  21,  25,  30,   35,   42,  49, 58, 69, 82,
                                  97,   115, 136, 161, 191, 227,  269,  318, 377,
                                  447,  530, 628, 744, 882, 1045, 1238, 1467,
                                  1739, 2060);
                    break;
                default:
                    $data = array(5,    30,   35,  41,  47,  55,   64,   74,  86, 100, 117,
                                  136,  158,  184, 214, 249, 289,  337,  391, 455,
                                  530,  616,  717, 833, 969, 1127, 1311, 1525,
                                  1774, 2063, 2400);
            }

            return $data[$level];
        }
    }

    // diese Funktion berechnet die Entfernung zwischen 2 D�rfern
    // sie erwartet als Argumente zwei X/Y-Koordinaten!!
    function calcDistance($from, $to)
    {
        // die Start- und Zielkoordinate k�nnen wahlweise als String oder bereits als "Arraykoordinate" �bergeben werden...
        if(!is_array($from))
            $from=getCoord($from);
        if(!is_array($to))
            $to=getCoord($to);

        $distance=sqrt(pow($from['x']-$to['x'],2) + pow($from['y']-$to['y'],2));

        return $distance;
    }

    // berechnet die Minenproduktion
    function calcMineProduction($level) {
        if($level == 0)
            return 5;
        return 30 * (pow(1.1631180425542682,($level-1)));
    }

    // diese Funktion berechnet, wie lange ein Trupp von einem Dorf zu einem anderen braucht
    function calcRuntime($from, $to, $timeperfield, $speed=1)
    {
        // die Start- und Zielkoordinate k�nnen wahlweise als String oder bereits als "Arraykoordinate" �bergeben werden...
        if(!is_array($from))
            $from=getCoord($from);
        if(!is_array($to))
            $to=getCoord($to);

        $distance=calcDistance($from, $to);

        $time=$distance * $timeperfield;

        return $time * $speed;
    }

    // berechnet das Fassungsverm�gen des Speichers
    function calcStorageMax($level) {
        $data = array(1000,   1229,   1512,   1859,  2285,  2810,   3454,   4247, 5222, 6420,
                      7893,   9705,   11932,  14670, 18037, 22177,  27266,  33523,
                      41217,  50675,  62305,  76604, 94184, 115798, 142373, 175047,
                      215219, 264611, 325337, 400000);
        return $data[$level-1];
    }

    function cleanCoord($str)
    {
        $coord = getCoord($str);
        return $coord['orig'];
    }

    // diese Funktion gibt ein assoziatives Array mit den Keys 'x' und 'y' zur�ck. x und y werden aus einer Kontinentalkoordinate berechnet
    // (leicht abge�ndert �bernommen aus http://wiki.die-staemme.de/wiki/Koordinatenberechnungen#Kontinent-System_.28Server_3.2C_Server_4.2C_Server_5.29_zum_xy-System_.28Server_1.2C_Server_2.29)
    function convert_coords_to_xy($con, $sec, $sub)
    {
        if($con < 0 || $con > 99 || $sec < 0 || $sec > 99 || $sub < 0 || $sub > 24) {
            trigger_error('invalid x:y:z coordinate: '.$con.':'.$sec.':'.$sub);
            return false;
        }

        $x = ($con % 10) * 50 + ($sec % 10) * 5 + ($sub % 5);
        $y = floor($con / 10) * 50 + floor($sec / 10) * 5 + floor($sub / 5);
        return array('x' => $x, 'y' => $y);
    }

    // diese Funktion gibt ein assoziatives Array mit den Keys 'x' und 'y' zur�ck. x und y werden aus einer Koordinate (String) extrahiert
    function getCoord($str)
    {
        $str = trim($str);

        $matches=FALSE;
        $result=array('x' => 0, 'y' => 0);

        if(preg_match('/(\-?[0-9]{1,3})\|(\-?[0-9]{1,3})/',$str,$matches))
        {
            if(is_numeric($matches[1]) and is_numeric($matches[2]))
            {
                $result['orig'] = $matches[0];
                $result['x']=$matches[1];
                $result['y']=$matches[2];
            }
        }
        elseif(preg_match('/([0-9]{1,3}):([0-9]{1,3}):([0-9]{1,3})/',$str,$matches))
        {
            $result = convert_coords_to_xy($matches[1], $matches[2], $matches[3]);
            $result['orig'] = $matches[0];
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

    // diese Funktion liefert ein assoziatives Array mit den Laufzeiten der Einheiten eines Servers zur�ck
    function getRuntimes($server)
    {
        global $root_path, $cfg;

        $xml = simplexml_load_file($root_path."data/server/{$cfg['language']}/$server/units.xml");

        $runtimes = array();

        foreach($xml as $unitname => $data) {
            $runtimes[$unitname] = $data['speed'];
        }

        return $runtimes;
    }

    // diese Funktion berechnet die Laufzeit f�r ein Feld anhand der Truppen... ($units, assoziatives Array)
    function getTimePerField($units, $server)
    {
        $time=0;
        $runtimes = $server->runtimes;

        // die langsamste Einheit ermitteln
        foreach($units as $name => $einheit)
        {
            if($einheit > 0) // wenn von der Einheitensorte �berhaupt welche dabei sind
            {
                if($runtimes[$name] > $time)
                    $time = $runtimes[$name];
            }
        }

        return $time*60; // sekunden zur�ckgeben
    }

    // gibt das Fassungsverm�gen des Verstecks zur�ck
    function hideMax($level) {
        $data = array(0, 150, 200, 267, 356, 474, 632, 843, 1125, 1500, 2000);
        return $data[$level];
    }

    /**
     * �berpr�ft das Format einer ServerID.
     */
    function isValidServerID($server)
    {
        return preg_match('/^[a-z0-9]+$/', $server);
    }

    // �berpr�ft ob es sich um eine korrekte DieSt�mme - Koordinate handelt
    function validCoord($str)
    {
        $str = trim($str);

        if(preg_match('/\-?[0-9]{1,3}\|\-?[0-9]{1,3}/',$str))
            return TRUE;
        elseif(preg_match('/([0-9]{1,3}):([0-9]{1,3}):([0-9]{1,3})/',$str))
            return TRUE;
        else
            return FALSE;
    }