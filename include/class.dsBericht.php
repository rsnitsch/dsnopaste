<?php
// Copyright by Robert Nitsch (2006-2007, 2013)

/*
DS_Bericht

DS_Bericht is a PHP class which can parse reports of the german browsergame DieStämme.
*/

define('DSBERICHT_VERSION','0.3.0.0');

if(!defined('INC_CHECK_DSBERICHT'))
    die('hacking attempt');

if(!defined('DSBERICHT_DEBUG')) define('DSBERICHT_DEBUG',FALSE); // debugging can be activated from outside (before including this file)

class dsBericht {

    private $data;
    private $matches;
    public $report;
    public $units_attacker;
    public $units_defender;
    public $units_spied;
    public $units_out;

    private $lang;
    private $patterns;
    private $all_patterns;

    function __construct($units, $spied_resources=array('wood', 'loam', 'iron'), $lng='de')
    {
        $this->all_patterns = array('de' => array(  'troops_pattern' => '/(?:Anzahl|Verluste):\s+((?:[0-9]+\s+)+)/',
                                                    'spied_troops_pattern' => '/Einheiten au.{1,2}erhalb:\s+((?:[0-9]+\s*)+)/',
                                                    'troops_out_pattern' => '/Truppen des Verteidigers, die unterwegs waren\s+((?:[0-9]+\s+)+)/',
                                                    'time' => '/Kampfzeit\s+([0-9]+)\.([0-9]+)\.([0-9]+)\s+([0-9]+):([0-9]+):([0-9]+)/',
                                                    'forwarded' => '/Weitergeleitet am:\s+([0-9]+)\.([0-9]+)\.([0-9]+)\s+([0-9]+):([0-9]+):([0-9]+)\s+Weitergeleitet von:\s+(.*)\s+Der (Angreifer|Verteidiger) hat gewonnen/',
                                                    'winner' => '/Der (Angreifer|Verteidiger) hat gewonnen/',
                                                    'luck' => '/Gl.{1,2}ck \(aus Sicht des Angreifers\).*\s+([\-0-9]*[0-9]+\.[0-9]+)%/s',
                                                    'moral' => '/Moral:\s+([0-9]+)/',
                                                    'attacker' => '/Angreifer:\s+(.*)\s+Herkunft:\s+(.*)\s+Anzahl:/U',
                                                    'village_con_check' => '/\)\s+K[0-9]{1,3}\s*$/',
                                                    'village_con' => '/^(.*)\(([0-9]{1,3}\|[0-9]{1,3})\)\s+K([0-9]{1,3}).*$/',
                                                    'village_nocon' => '/^(.*)\(([0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2})\).*$/',
                                                    'defender' => '/Verteidiger:\s+(.*)\s+Ziel:\s+(.*)\s+Anzahl:/U',
                                                    'wall' => '/Schaden durch (Rammen|Rammb.{1,2}cke):\s+Wall besch.{1,2}digt von Level ([0-9]+) auf Level ([0-9]+)/',
                                                    'catapult' => '/Schaden durch Katapultbeschuss:\s+([A-Za-zäöü]+) besch.{1,2}digt von Level ([0-9]+) auf Level ([0-9]+)/',
                                                    'espionage' => '/Spionage/',
                                                    'spied_resources_start' => '/Ersp.{1,2}hte Rohstoffe:\s+',
                                                    'buildings' => '/Geb.{1,2}ude/',
                                                    'b_main' => '/Hauptgeb.{1,2}ude\s+\(Stufe ([0-9]+)\)/',
                                                    'b_barracks' => '/Kaserne\s+\(Stufe ([0-9]+)\)/',
                                                    'b_stable' => '/Stall\s+\(Stufe ([0-9]+)\)/',
                                                    'b_garage' => '/Werkstatt\s+\(Stufe ([0-9]+)\)/',
                                                    'b_snob' => '/Adelshof\s+\(Stufe ([0-9]+)\)/',
                                                    'b_smith' => '/Schmiede\s+\(Stufe ([0-9]+)\)/',
                                                    'b_place' => '/Versammlungsplatz\s+\(Stufe ([0-9]+)\)/',
                                                    'b_statue' => '/Statue\s+\(Stufe ([0-9]+)\)/',
                                                    'b_market' => '/Marktplatz\s+\(Stufe ([0-9]+)\)/',
                                                    'b_wood' => '/Holzf.{1,2}ller\s+\(Stufe ([0-9]+)\)/',
                                                    'b_stone' => '/Lehmgrube\s+\(Stufe ([0-9]+)\)/',
                                                    'b_iron' => '/Eisenmine\s+\(Stufe ([0-9]+)\)/',
                                                    'b_farm' => '/Bauernhof\s+\(Stufe ([0-9]+)\)/',
                                                    'b_storage' => '/Speicher\s+\(Stufe ([0-9]+)\)/',
                                                    'b_hide' => '/Versteck\s+\(Stufe ([0-9]+)\)/',
                                                    'b_wall' => '/Wall\s+\(Stufe ([0-9]+)\)/',
                                                    'booty' => '/Beute:\s+([\.0-9]+)\s([\.0-9]+)\s([\.0-9]+)\s+([\.0-9]+)\/([\.0-9]+)/',
                                                    'mood' => '/Zustimmung gesunken von ([0-9]+) auf ([\-0-9]+)/',
                                             )
                               );

        $this->all_patterns["en"] = array(          'troops_pattern' => '/(?:Quantity|Losses):\s+((?:[0-9]+\s+)+)/',
                                                    'spied_troops_start' => 'Units outside of village:\s+',
                                                    'troops_out_start' => "Defender's".' troops, that were in transit\s+',
													// 	Jul 12, 2013 12:12:41
                                                    'time' => '/Sent\s+([a-zA-Z]+\s+[^a-zA-Z]+)/',
                                                    'forwarded' => '/Weitergeleitet am:\s+([0-9]+)\.([0-9]+)\.([0-9]+)\s+([0-9]+):([0-9]+).*Weitergeleitet von:\s+([^\n]*)\n/s', // TODO
                                                    'winner' => '/The (attacker|defender) has won/',
                                                    'luck' => '/Luck \(from attacker\'s point of view\).*\s+([\-0-9]*[0-9]+\.[0-9]+)%/s',
                                                    'moral' => '/Morale:\s+([0-9]+)/',
                                                    'attacker' => '/Attacker:\s+(.*)\s+Origin:\s+(.*)\s+Quantity:/',
                                                    'village_con_check' => '/\)\s+K[0-9]{1,3}\s*$/',
                                                    'village_con' => '/^(.*)\(([0-9]{1,3}\|[0-9]{1,3})\)\s+K([0-9]{1,3}).*$/',
                                                    'village_nocon' => '/^(.*)\(([0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2})\).*$/',
                                                    'defender' => '/Defender:\s+(.*)\s+Destination:\s+(.*)\s+Quantity:/',
                                                    'wall' => '/Damage by rams:\s+The wall has been damaged and downgraded from level ([0-9]+) to level ([0-9]+)/',
                                                    'catapult' => '/Damage by catapult bombardment:\s+([A-Za-zäöü]+) has been damaged and downgraded from level ([0-9]+) to level ([0-9]+)/',
                                                    'espionage' => '/Espionage/', /* TODO: is this regex correct? */
                                                    'spied_resources_start' => '/Resources scouted:\s+',
                                                    'buildings' => '/Buildings/',
                                                    'b_main' => '/Headquarters\s+\(Level ([0-9]+)\)/',
                                                    'b_barracks' => '/Barracks\s+\(Level ([0-9]+)\)/',
                                                    'b_stable' => '/Stable\s+\(Level ([0-9]+)\)/',
                                                    'b_garage' => '/Workshop\s+\(Level ([0-9]+)\)/',
                                                    'b_snob' => '/Academy\s+\(Level ([0-9]+)\)/',
                                                    'b_smith' => '/Smith\s+\(Level ([0-9]+)\)/',
                                                    'b_place' => '/Rally Points\s+\(Level ([0-9]+)\)/',
                                                    'b_statue' => '/Statue\s+\(Level ([0-9]+)\)/',
                                                    'b_market' => '/Market\s+\(Level ([0-9]+)\)/',
                                                    'b_wood' => '/Timber Camp\s+\(Level ([0-9]+)\)/',
                                                    'b_stone' => '/Clay Pit\s+\(Level ([0-9]+)\)/',
                                                    'b_iron' => '/Iron mine\s+\(Level ([0-9]+)\)/',
                                                    'b_farm' => '/Farm\s+\(Level ([0-9]+)\)/',
                                                    'b_storage' => '/Warehouse\s+\(Level ([0-9]+)\)/',
                                                    'b_hide' => '/Hiding place\s+\(Level ([0-9]+)\)/',
                                                    'b_wall' => '/Wall\s+\(Level ([0-9]+)\)/',
                                                    'booty' => '/Haul:\s+([\.0-9]+)\s([\.0-9]+)\s([\.0-9]+)\s+([\.0-9]+)\/([\.0-9]+)/',
                                                    'mood' => '/Loyalty loss from ([0-9]+) to ([\-0-9]+)/',
                                             );
        if(is_array($this->all_patterns[$lng]))
            $this->patterns = $this->all_patterns[$lng];
        else
            $this->patterns = $this->all_patterns['de'];
        $this->lang = $lng;

        $this->reset();

        $this->set_units($units);
        $this->set_spied_resources($spied_resources);
    }

    function reset()
    {
        $this->matches=FALSE;
        $this->data=FALSE;
        $this->server='';
        $this->units_attacker=null;
        $this->units_defender=null;
        $this->units_spied=null;
        $this->units_out=null;

        $this->report=array(
            'time' => FALSE,
            'forwarded' => FALSE,
            'winner' => FALSE,
            'luck' => FALSE,
            'moral' => FALSE,
            'attacker' => FALSE,
            'defender' => FALSE,
            'troops' => FALSE,
            'wall' => FALSE,
            'catapult' => FALSE,
            'spied' => FALSE,
            'buildings' => FALSE,
            'troops_out' => FALSE,
            'booty' => FALSE,
            'mood' => FALSE,
        );
    }

    function set_spied_resources($spied_res)
    {
        if (!is_array($spied_res)) {
            throw new InvalidArgumentException("Expected an array");
        }
        $this->spied_resources = $spied_res;
        $this->build_spied_resources_pattern();
    }

    function set_units($units)
    {
        if (!is_array($units) || empty($units)) {
            throw new InvalidArgumentException("Expected a non-empty array");
        }

        if (array_key_exists('attacker', $units)) {
            $this->units_attacker = $units['attacker'];
            $this->units_defender = array_key_exists('defender', $units) ? $units['defender'] : $this->units_attacker;
            $this->units_spied = array_key_exists('spied', $units) ? $units['spied'] : $this->units_attacker;
            $this->units_out = array_key_exists('out', $units) ? $units['out'] : $this->units_attacker;
        } else {
            $this->units_attacker = $units;
            $this->units_defender = $units;
            $this->units_spied = $units;
            $this->units_out = $units;
        }
    }

    function build_spied_resources_pattern()
    {
        $this->spied_resources_pattern = $this->patterns['spied_resources_start'];
        if (count($this->spied_resources) > 0) {
            for ($i = 0; $i < count($this->spied_resources)-1; ++$i) {
                $this->spied_resources_pattern .= '([0-9\.]+)\s+';
            }
            $this->spied_resources_pattern .= '([0-9\.]+)';
        }
        $this->spied_resources_pattern .= '/';
    }

    // parses a complete report...
    function parse($data, $server='', $required=array('time'))
    {
        $this->data=$data;

        $this->report['time']       = $this->parse_time();
        $this->report['forwarded']  = $this->parse_forwarded();
        $this->report['winner']     = $this->parse_winner();
        $this->report['luck']       = $this->parse_luck();
        $this->report['moral']      = $this->parse_moral();
        $this->report['attacker']   = $this->parse_attacker();
        $this->report['defender']   = $this->parse_defender();
        $this->report['troops']     = $this->parse_troops();
        $this->report['wall']       = $this->parse_wall();
        $this->report['catapult']   = $this->parse_catapult();
        if($this->preg_match_std($this->patterns['espionage']))
        {
            $this->report['spied_resources'] = $this->parse_spied_resources();
            $this->report['buildings']    = $this->parse_buildings();
            $this->report['spied_troops_out'] = $this->parse_spied_troops();
        }
        $this->report['troops_out']     = $this->parse_troops_out();
        $this->report['booty']          = $this->parse_booty();
        $this->report['mood']           = $this->parse_mood();

        if(DSBERICHT_DEBUG)
        {
            echo "\n\n";
            echo '<span style="font-weight: bold;">';
            print_r($this->report);
            echo '</span>';
            echo '<hr /><br />And this is the SQL VALUES part:<br />';
            echo $this->buildSQL('pseudotable');
            echo '<hr /><br />And this one is the associative array generated for the sql statement:<br />';
            print_r($this->buildAssoc());
        }

        foreach ($required as $req) {
            if (!$this->report[$req]) {
                return false;
            }
        }

        return TRUE;
    }

    function getReport()
    {
        return $this->report;
    }

    function setReport($data)
    {
        $this->report=$data;
    }

    // implementation of the standard preg_match call
    function preg_match_std($pattern, $data='')
    {
        $this->currentPattern($pattern);

        $this->matches = FALSE;

        if(preg_match($pattern, (!empty($data) ? $data : $this->data), $this->matches))
        {
            $this->currentPattern_found(true);
            return TRUE;
        }


        $this->currentPattern_found(false);
        return FALSE;
    }

    // this function displays a small HTML code about the currently used pattern ... for debug purposes only.
    function currentPattern($pattern)
    {
        if(DSBERICHT_DEBUG)
            echo "Current regex pattern: <span style='color: #999999;'>".$pattern."</span> ...";
    }

    // this function generates the colored texts "found" or "not found" (so it echos HTML) ... for debug purposes only.
    function currentPattern_found($found)
    {
        if(DSBERICHT_DEBUG)
        {
            if($found)
                echo "<span style='color: #21FF21; font-weight: bold;'>found!</span>\n";
            else
                echo "<span style='color: #FF2121; font-weight: bold;'>not found!</span>\n";
        }
    }

    // returns the value according to one key of the $matches array, which is used to save preg_match data ...
    function match($count)
    {
        if($this->matches != FALSE)
        {
            return $this->matches[$count];
        }
        else
        {
            trigger_error('variable matches doesnt contain any data! returning FALSE', E_USER_WARNING);
            return FALSE;
        }
    }


    // builds an INSERT INTO query automatically
    function buildSQL($table, $extra_columns=false)
    {
        if(!$this->data) return '';

        // alle Daten zunächst in einem Array ablegen
        $data = $this->buildAssoc();
        if($extra_columns !== false)
            $data = array_merge($extra_columns, $data);

        $keys = '';
        $values = '';
        foreach($data as $key => $value)
        {
            $keys .= '`'.$key.'`';
            $keys .= ',';

            $values .= "'$value', ";
            $values .= "\n";
        }

        $values = trim($values);
        $values = trim($values, ",");
        $keys = trim($keys);
        $keys = trim($keys, ",");

        return 'INSERT INTO '.$table.' ('.$keys.') VALUES ('.$values.')';
    }

    // builds an associative array containing all data of the report
    function buildAssoc()
    {
        $assoc = array(
            /* general data */
            'time'         => ($this->report['time'] ? $this->report['time'] : 0),
            'forwarded' => (is_array($this->report['forwarded']) ? 1 : 0),
            'forwarded_time' => (isset($this->report['forwarded']['time']) ? $this->report['forwarded']['time'] : 0),
            'forwarded_sender' => (isset($this->report['forwarded']['sender']) ? $this->report['forwarded']['sender'] : 0),
            'winner'     => ($this->report['winner'] ? $this->report['winner'] : 1),
            'luck'        => ($this->report['luck'] ? $this->report['luck'] : 0.0),
            'moral'        => ($this->report['moral'] ? $this->report['moral'] : 0),
            /* attacker/defender data */
            'attacker_nick'        => (isset($this->report['attacker']['nick']) ? trim($this->report['attacker']['nick']) : 'unknown'),
            'attacker_village'     => (isset($this->report['attacker']['village']) ? trim($this->report['attacker']['village']) : 'unknown'),
            'attacker_coords'     => (isset($this->report['attacker']['coords']) ? trim($this->report['attacker']['coords']) : 'x|y'),
            'attacker_continent'     => (isset($this->report['attacker']['continent']) ? trim($this->report['attacker']['continent']) : -1),
            'defender_nick'     => (isset($this->report['defender']['nick']) ? trim($this->report['defender']['nick']) : 'unknown'),
            'defender_village'     => (isset($this->report['defender']['village']) ? trim($this->report['defender']['village']) : 'unknown'),
            'defender_coords'     => (isset($this->report['defender']['coords']) ? trim($this->report['defender']['coords']) : 'x|y'),
            'defender_continent'     => (isset($this->report['defender']['continent']) ? trim($this->report['defender']['continent']) : -1),
            /* troops */
            'troops'         => (is_array($this->report['troops']) ? 1 : 0),
            /* spied troops out */
            'spied_troops_out' => ((isset($this->report['spied_troops_out']) && is_array($this->report['spied_troops_out'])) ? 1 : 0),
            /* conquer troops out */
            'troops_out' => (is_array($this->report['troops_out']) ? 1 : 0),
            /* wall damage */
            'wall' => ($this->report['wall'] ? 1 : 0),
            'wall_before'     => (isset($this->report['wall']['before']) ? $this->report['wall']['before'] : 0),
            'wall_after'     => (isset($this->report['wall']['after']) ? $this->report['wall']['after'] : 0),
            /* catapult damage */
            'catapult' => ($this->report['catapult'] ? 1 : 0),
            'catapult_before' => (isset($this->report['catapult']['before']) ? $this->report['catapult']['before'] : 0),
            'catapult_after' => (isset($this->report['catapult']['after']) ? $this->report['catapult']['after'] : 0),
            'catapult_building' => (isset($this->report['catapult']['building']) ? $this->report['catapult']['building'] : ''),
            /* spied resources */
            'spied' => ($this->report['spied'] ? 1 : 0),
            'spied_wood' => (isset($this->report['spied']['wood']) ? $this->report['spied']['wood'] : 0),
            'spied_loam' => (isset($this->report['spied']['loam']) ? $this->report['spied']['loam'] : 0),
            'spied_iron' => (isset($this->report['spied']['iron']) ? $this->report['spied']['iron'] : 0),
            /* buildings */
            'buildings' => (is_array($this->report['buildings']) ? 1 : 0),
            'buildings_main' => (isset($this->report['buildings']['main']) ? $this->report['buildings']['main'] : 0),
            'buildings_barracks' => (isset($this->report['buildings']['barracks']) ? $this->report['buildings']['barracks'] : 0),
            'buildings_stable' => (isset($this->report['buildings']['stable']) ? $this->report['buildings']['stable'] : 0),
            'buildings_garage' => (isset($this->report['buildings']['garage']) ? $this->report['buildings']['garage'] : 0),
            'buildings_snob' => (isset($this->report['buildings']['snob']) ? $this->report['buildings']['snob'] : 0),
            'buildings_smith' => (isset($this->report['buildings']['smith']) ? $this->report['buildings']['smith'] : 0),
            'buildings_place' => (isset($this->report['buildings']['place']) ? $this->report['buildings']['place'] : 0),
            'buildings_statue' => (isset($this->report['buildings']['statue']) ? $this->report['buildings']['statue'] : 0),
            'buildings_market' => (isset($this->report['buildings']['market']) ? $this->report['buildings']['market'] : 0),
            'buildings_wood' => (isset($this->report['buildings']['wood']) ? $this->report['buildings']['wood'] : 0),
            'buildings_stone' => (isset($this->report['buildings']['stone']) ? $this->report['buildings']['stone'] : 0),
            'buildings_iron' => (isset($this->report['buildings']['iron']) ? $this->report['buildings']['iron'] : 0),
            'buildings_farm' => (isset($this->report['buildings']['farm']) ? $this->report['buildings']['farm'] : 0),
            'buildings_storage' => (isset($this->report['buildings']['storage']) ? $this->report['buildings']['storage'] : 0),
            'buildings_hide' => (isset($this->report['buildings']['hide']) ? $this->report['buildings']['hide'] : 0),
            'buildings_wall' => (isset($this->report['buildings']['wall']) ? $this->report['buildings']['wall'] : 0),
            /* booty */
            'booty' => ($this->report['booty'] ? 1 : 0),
            'booty_wood' => (isset($this->report['booty']['wood']) ? $this->report['booty']['wood'] : 0),
            'booty_loam' => (isset($this->report['booty']['loam']) ? $this->report['booty']['loam'] : 0),
            'booty_iron' => (isset($this->report['booty']['iron']) ? $this->report['booty']['iron'] : 0),
            'booty_all' => (isset($this->report['booty']['all']) ? $this->report['booty']['all'] : 0),
            'booty_max' => (isset($this->report['booty']['max']) ? $this->report['booty']['max'] : 0),
            /* mood */
            'mood' => ($this->report['mood'] ? 1 : 0),
            'mood_before' => (isset($this->report['mood']['before']) ? $this->report['mood']['before'] : 0),
            'mood_after' => (isset($this->report['mood']['after']) ? $this->report['mood']['after'] : 0)
            );

        foreach($this->units_attacker as $unit)
        {
            $assoc['troops_att_'.$unit] = isset($this->report['troops']['att_'.$unit]) ? $this->report['troops']['att_'.$unit] : 0;
            $assoc['troops_attl_'.$unit] = isset($this->report['troops']['attl_'.$unit]) ? $this->report['troops']['attl_'.$unit] : 0;
        }
		foreach($this->units_defender as $unit)
		{
            $assoc['troops_def_'.$unit] = isset($this->report['troops']['def_'.$unit]) ? $this->report['troops']['def_'.$unit] : 0;
            $assoc['troops_defl_'.$unit] = isset($this->report['troops']['defl_'.$unit]) ? $this->report['troops']['defl_'.$unit] : 0;
		}
        foreach($this->units_spied as $unit)
        {
            $assoc['spied_troops_out_'.$unit] = isset($this->report['spied_troops_out'][$unit]) ? $this->report['spied_troops_out'][$unit] : 0;
        }
        foreach($this->units_out as $unit)
        {
            $assoc['troops_out_'.$unit] = isset($this->report['troops_out'][$unit]) ? $this->report['troops_out'][$unit] : 0;
        }

        return $assoc;
    }

    // #############
    // PARSE FUNCTIONS ... each function parses ONE specific part of the report.
    // #############
    function parse_time()
    {
        $time=FALSE;
        if($this->preg_match_std($this->patterns['time']))
        {
            if ($this->lang == 'de') {
                $time=mktime($this->match(4), $this->match(5), $this->match(6), $this->match(2), $this->match(1), $this->match(3));
            } else if ($this->lang == 'en') {
                // Jul 12, 2013 12:12:41
                $dt = DateTime::createFromFormat("M j, Y H:i:s", trim($this->match(1)));
                if ($dt !== false) {
                    $time = $dt->getTimestamp();
                }
            }
            // int mktime ( [int Stunde [, int Minute [, int Sekunde [, int Monat [, int Tag [, int Jahr [, int is_dst]]]]]]] )
        }

        return $time;
    }

    function parse_forwarded()
    {
        $forwarded = FALSE;
        if($this->preg_match_std($this->patterns['forwarded']))
        {
            $forwarded['time'] = mktime($this->match(4), $this->match(5), $this->match(6), $this->match(2), $this->match(1), $this->match(3));
            $forwarded['sender'] = trim($this->match(7));
        }

        return $forwarded;
    }

    // parses the winner
    function parse_winner()
    {
        $winner=FALSE;
        if($this->preg_match_std($this->patterns['winner']))
        {
            if($this->match(1)=='Angreifer')
                $winner=1; // attacker
            else
                $winner=2; // defender
        }

        return $winner;
    }

    // parses the luck
    function parse_luck()
    {
        $luck=FALSE;
        if($this->preg_match_std($this->patterns['luck']))
        {
            $luck=floatval($this->match(1));
        }

        return $luck;
    }

    // parses the moral
    function parse_moral()
    {
        $moral=FALSE;
        if($this->preg_match_std($this->patterns['moral']))
        {
            $moral=floatval($this->match(1));
        }

        return $moral;
    }

    // parses the attacker's name and village
    function parse_attacker()
    {
        $attacker=FALSE;
        if($this->preg_match_std($this->patterns['attacker']))
        {
            $attacker['nick']=trim($this->match(1));

            if(preg_match($this->patterns['village_con_check'], $this->match(2)))
            {
                $village = $this->preg_match_std($this->patterns['village_con'], $this->match(2));
                $attacker['village'] = trim($this->match(1));
                $attacker['coords'] = $this->match(2);
                $attacker['continent']  = intval($this->match(3));
            }
            else
            {
                $village = $this->preg_match_std($this->patterns['village_nocon'], $this->match(2));
                $attacker['village'] = trim($this->match(1));
                $attacker['coords'] = $this->match(2);
                $attacker['continent']  = -1;
            }
        }

        return $attacker;
    }

    // parses the defender's name and village
    function parse_defender()
    {
        $defender=FALSE;
        if($this->preg_match_std($this->patterns['defender']))
        {
            $defender['nick']=trim($this->match(1));

            if(preg_match('/\)\s+K[0-9]{1,3}\s*$/', $this->match(2)))
            {
                $village = $this->preg_match_std("/^(.*)\(([0-9]{1,3}\|[0-9]{1,3})\)\s+K([0-9]{1,3}).*$/", $this->match(2));
                $defender['village'] = trim($this->match(1));
                $defender['coords'] = $this->match(2);
                $defender['continent']  = intval($this->match(3));
            }
            else
            {
                $village = $this->preg_match_std("/^(.*)\(([0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2})\).*$/", $this->match(2));
                $defender['village'] = trim($this->match(1));
                $defender['coords'] = $this->match(2);
                $defender['continent']  = -1;
            }
        }

        return $defender;
    }

    function parse_troops()
    {
        $troops=FALSE;
        $this->matches=FALSE;

        $this->currentPattern($this->patterns['troops_pattern']);

        if(preg_match_all($this->patterns['troops_pattern'], $this->data, $this->matches, PREG_SET_ORDER)) {
            $this->currentPattern_found(true);
        } else {
            $this->currentPattern_found(false);
            return false;
        }

        $data = $this->matches;

        if (count($data) != 4) {
            throw new RuntimeException("Expected 4 troop sets (2 for attacker and 2 for defender).");
        }

        $att = preg_split('/\s+/', trim($data[0][1]));
        $attl = preg_split('/\s+/', trim($data[1][1]));
        $def = preg_split('/\s+/', trim($data[2][1]));
        $defl = preg_split('/\s+/', trim($data[3][1]));

        if (count($att) != count($this->units_attacker)) {
            throw new RuntimeException("Number of parsed attacking unit quantities differs from expected number of units.");
        } else if (count($attl) != count($this->units_attacker)) {
            throw new RuntimeException("Number of parsed attacking unit losses differs from expected number of units.");
        } else if (count($def) != count($this->units_defender)) {
            throw new RuntimeException("Number of parsed defending unit quantities differs from expected number of units.");
        } else if (count($defl) != count($this->units_defender)) {
            throw new RuntimeException("Number of parsed defending unit losses differs from expected number of units.");
        }

        $troops = array();
        for($i = 0; $i < count($this->units_attacker); ++$i) {
            $troops['att_'.$this->units_attacker[$i]] = intval($att[$i]);
            $troops['attl_'.$this->units_attacker[$i]] =  intval($attl[$i]);
        }
        for($i = 0; $i < count($this->units_defender); ++$i) {
            $troops['def_'.$this->units_defender[$i]] = intval($def[$i]);
            $troops['defl_'.$this->units_defender[$i]] =  intval($defl[$i]);
        }

        return $troops;
    }

    // parses the wall before and after the battle
    function parse_wall()
    {
        $wall=FALSE;
        if($this->preg_match_std($this->patterns['wall']))
        {
            $wall['before']=intval($this->match(2));
            $wall['after']=intval($this->match(3));
        }

        return $wall;
    }

    // parses the catapult's damage
    function parse_catapult()
    {
        $catapult=FALSE;
        if($this->preg_match_std($this->patterns['catapult']))
        {
            $catapult['building']=trim($this->match(1));
            $catapult['before']=intval($this->match(2));
            $catapult['after']=intval($this->match(3));
        }

        return $catapult;
    }

    // spied resources
    function parse_spied_resources()
    {
        if($this->preg_match_std($this->spied_resources_pattern))
        {
            $spied=array('wood' => 0, 'loam' => 0, 'iron' => 0);
            for ($i = 0; $i < count($this->spied_resources); ++$i) {
                $spied[$this->spied_resources[$i]] = intval(str_replace(".", "", $this->match($i+1)));
            }
            return $spied;
        }
        return false;
    }

    // troops, which have been out while spying
    function parse_spied_troops()
    {
        $spied_troops = FALSE;

        if($this->preg_match_std($this->patterns['spied_troops_pattern'])) {
            $spied = preg_split('/\s+/', trim($this->match(1)));
            if (count($spied) != count($this->units_spied)) {
                throw new RuntimeException("Number of spied units differs from number of expected units.");
            }

            $spied_troops = array();
            foreach ($this->units_spied as $i => $unit) {
                $spied_troops[$unit] = intval($spied[$i]);
            }
        }

        return $spied_troops;
    }

    // parses the spied buildings
    function parse_buildings()
    {
        $buildings=FALSE;
        $this->matches=FALSE;

        // only if there are any spied buildings. otherwise this method would waste CPU time...
        if(preg_match($this->patterns['buildings'], $this->data))
        {
            $buildings=array(
            'main'=>0,
            'barracks'=>0,
            'stable'=>0,
            'garage'=>0,
            'snob'=>0,
            'smith'=>0,
            'place'=>0,
            'statue'=>0,
            'market'=>0,
            'wood'=>0,
            'stone'=>0,
            'iron'=>0,
            'farm'=>0,
            'storage'=>0,
            'hide'=>0,
            'wall'=>0
            );

            // parse all buildings...
            foreach ($buildings as $name => $level) {
                if($this->preg_match_std($this->patterns["b_$name"])) {
                    $buildings[$name] = intval($this->match(1));
                }
            }

            return $buildings;
        }
        else
        {
            return FALSE;
        }
    }

    function parse_troops_out()
    {
        $troops_out=FALSE;

        if($this->preg_match_std($this->patterns['troops_out_pattern'])) {
            $out = preg_split('/\s+/', trim($this->match(1)));
            if (count($out) != count($this->units_out)) {
                throw new RuntimeException("Number of out-of-village units differs from number of expected units.");
            }

            $troops_out = array();
            foreach ($this->units_out as $i => $unit) {
                $troops_out[$unit] = intval($out[$i]);
            }
        }

        return $troops_out;
    }

    // parses the attacker's booty
    function parse_booty()
    {
        $booty=FALSE;
        if($this->preg_match_std($this->patterns['booty']))
        {
            $booty['wood']=intval(str_replace(".", "", $this->match(1)));
            $booty['loam']=intval(str_replace(".", "", $this->match(2)));
            $booty['iron']=intval(str_replace(".", "", $this->match(3)));
            $booty['all']=intval(str_replace(".", "", $this->match(4)));
            $booty['max']=intval(str_replace(".", "", $this->match(5)));
        }

        return $booty;
    }

    // parses the mood in the village before and after the battle
    function parse_mood()
    {
        $mood=FALSE;
        if($this->preg_match_std($this->patterns['mood']))
        {
            $mood['before']=intval($this->match(1));
            $mood['after']=intval($this->match(2));
        }

        return $mood;
    }
};
