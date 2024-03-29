<?php
    class Gameworld {
        public $id;
        public $name;
        public $config;
		
		public $buildings;
        public $units;

        public static function forServerID($id)
        {
            if(!serverExists($id))
                throw new Exception("Server with id '$id' does not exist!");

            global $root_path, $cfg;
            return new Gameworld($root_path."data/server/{$cfg['language']}/{$id}");
        }

        public static function nameForID($id)
        {
            $name = $id;
            $matches = array();
            if (preg_match("/^de([a-zA-Z]*)([0-9]+)$/", $id, $matches)) {
                if ($matches[1] == '') {
                    $name = "Welt ".intval($matches[2]);
                } else if ($matches[1] == 'p') {
                    $name = "Casual ".intval($matches[2]);
                }
            }

            return $name;
        }

        public function __construct($directory)
        {
            if (!is_dir($directory)) {
                throw new Exception("Gameworld directory '$directory' does not exist!");
            }

            $this->dir = $directory;
            $this->id = basename($this->dir);
            $this->name = Gameworld::nameForID($this->id);

            $this->loadData();
        }

        protected function loadData()
        {
            $this->buildings = simplexml_load_file($this->dir."/buildings.xml");
            $this->buildings = $this->xml2array($this->buildings);
			
            $this->units = simplexml_load_file($this->dir."/units.xml");
            $this->units = $this->xml2array($this->units);

            $this->config = simplexml_load_file($this->dir."/config.xml");
            $this->config = $this->xml2array($this->config);
        }

        public function getConfig() {
            return $this->config;
        }

        public function coordSystem()
        {
            return 'modern';
        }

        public function bonusNew() {
            return $this->config["coord"]["bonus_new"] == 1;
        }

        public function bonusesPossible() {
            if ($this->bonusNew()) {
                return array('none', 'all', 'wood', 'loam', 'iron', 'storage');
            } else {
                return array('none', 'all', 'wood', 'loam', 'iron');
            }
        }

        public function bonusResAllFactor() {
            return $this->bonusNew() ? 1.3 : 1.03;
        }

        public function bonusResOneFactor() {
            return $this->bonusNew() ? 2 : 1.1;
        }

        public function getSpeed() {
            return $this->config["speed"];
        }

        public function getUnits($no_militia = false) {
            $units = $this->units;
            if ($no_militia && isset($units["militia"])) {
                unset($units["militia"]);
            }
            return $units;
        }

        public function getUnitNames($no_militia = false) {
            return array_keys($this->getUnits($no_militia));
        }

		public function getBuildings() {
			return $this->buildings;
		}
		
		public function getBuildingNames() {
			return array_keys($this->getBuildings());
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
        function calcMineProductionPerHour($level) {
            $level = intval($level);
            if ($level == 0) {
                return 5;
            }

            if ($level < 0 || $level > 30) {
                throw new InvalidArgumentException("level must be in range [0, 30].");
            }

            /*
            Notes:
              Style 6 is equivalent to style 4 in terms of resource production.
            */
            $styles = array(
                "1" => 1.1849947123642790517084558178612437188691209528184791,
                "3" => 1.1499939473519853323916857783744216678507170787537519,
                "4" => 1.1631180425542681684944206017852942633987886353007667,
            );

            // Parse the world's style.
            $base_config = trim($this->config["game"]["base_config"]);
            if ($base_config == "6") {
                $base_config = "4";
            } else if (!in_array($base_config, array_keys($styles))) {
                $base_config = "4"; // Default.
                trigger_error("calcMineProduction: Style '".urlencode($base_config)."' is unknown. World: '".urlencode($this->id)."'");
            }

            // Get parameters based on the world's style.
            $growth = $styles[$base_config];
            $base_production = $this->config["game"]["base_production"];

            return intval(round($base_production * pow($growth, $level-1) * $this->getSpeed()));
        }

        private function calcTotalMineProductionSingle($level, $type, $bonus, $hours_gone) {
            $prod = $this->calcMineProductionPerHour($level) * $hours_gone;
            if ($bonus == 'all') {
                $prod = round($prod * $this->bonusResAllFactor());
            } elseif ($bonus == $type) {
                $prod = round($prod * $this->bonusResOneFactor());
            }
            return $prod;
        }

        public function calcTotalMineProduction($lvl_wood, $lvl_loam, $lvl_iron, $bonus, $hours_gone=1.0) {
            $wood = $this->calcTotalMineProductionSingle($lvl_wood, 'wood', $bonus, $hours_gone);
            $loam = $this->calcTotalMineProductionSingle($lvl_loam, 'loam', $bonus, $hours_gone);
            $iron = $this->calcTotalMineProductionSingle($lvl_iron, 'iron', $bonus, $hours_gone);
            return array("wood" => $wood, "loam" => $loam, "iron" => $iron);
        }

        // diese Funktion berechnet die Laufzeit f�r ein Feld anhand der Truppen... ($units, assoziatives Array)
        function getTimePerField($units)
        {
            if (array_sum($units) <= 0)
            {
                throw new InvalidArgumentException("Total unit count must be greater than zero.");
            }

            $time=0;

            // die langsamste Einheit ermitteln
            foreach($units as $name => $count)
            {
                if($count > 0)
                {
                    if($this->units[$name]['speed'] > $time)
                        $time = $this->units[$name]['speed'];
                }
                else if ($count < 0)
                {
                    throw new InvalidArgumentException("Negative unit numbers are not allowed.");
                }
            }

            if ($time <= 0)
            {
                throw new LogicException("Time per field should be greater than zero.");
            }

            return $time*60; // sekunden zur�ckgeben
        }

        protected function xml2array($xmlObject, $out = array())
        {
            foreach ((array) $xmlObject as $index => $node) {
                if (is_object($node) || is_array($node)) {
                    $out[$index] = $this->xml2array($node);
                } else if (is_numeric($node)) {
                    $out[$index] = (strval(intval($node)) === $node) ? intval($node) : floatval($node);
                } else {
                    $out[$index] = $node;
                }
            }
            return $out;
        }
    }

    // diese Funktion berechnet die Entfernung zwischen 2 D�rfern
    // sie erwartet als Argumente zwei X/Y-Koordinaten!!
    function calcDistance($from, $to)
    {
        // die Start- und Zielkoordinate k�nnen wahlweise als String oder bereits als "Arraykoordinate" �bergeben werden...
        if(!is_array($from))
            $from=parseCoordinate($from);
        if(!is_array($to))
            $to=parseCoordinate($to);

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
            $from=parseCoordinate($from);
        if(!is_array($to))
            $to=parseCoordinate($to);

        $distance=calcDistance($from, $to);

        $time=$distance * $timeperfield;

        return $time * $speed;
    }

    function cleanCoord($str)
    {
        $coord = parseCoordinate($str);
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

    /**
     * �berpr�ft das Format einer ServerID.
     */
    function isValidServerID($server)
    {
        return preg_match('/^[a-z0-9]+$/', $server);
    }

    // diese Funktion gibt ein assoziatives Array mit den Keys 'x' und 'y' zur�ck. x und y werden aus einer Koordinate (String) extrahiert
    function parseCoordinate($str)
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
