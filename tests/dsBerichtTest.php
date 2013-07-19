<?php
define('INC_CHECK_DSBERICHT', true);
require_once('../include/class.dsBericht.php');

class dsBerichtTest extends PHPUnit_Framework_TestCase
{
    public function testParse1()
    {
        $units_attacker = array('spear', 'sword', 'axe', 'archer', 'spy', 'light', 'marcher', 'heavy', 'ram', 'catapult', 'knight', 'snob');
        $units_defender = $units_attacker;
        $units_defender[] = 'militia';
        $units_spied = $units_attacker;

        $files = array('report1.txt', 'report1_nl.txt');
        foreach($files as $file) {
            $dsBericht = new dsBericht(array('attacker' => $units_attacker, 'defender' => $units_defender));
            $raw = file_get_contents($file);
            $dsBericht->parse($raw);
            $r = $dsBericht->getReport();

            $this->assertSame(date('d.m.y H:i:s', $r['time']), '14.07.13 17:44:33');
            $this->assertSame(date('d.m.y H:i:s', $r['forwarded']['time']), '14.07.13 18:09:20');

            $this->assertSame(-13.5, $r['luck']);
            $this->assertSame(100.0, $r['moral']);

            $this->assertSame('fenrir123', $r['attacker']['nick']);
            $this->assertSame('Wotans Wille', $r['attacker']['village']);
            $this->assertSame('270|437', $r['attacker']['coords']);
            $this->assertSame(42, $r['attacker']['continent']);

            $this->assertSame('---', $r['defender']['nick']);
            $this->assertSame('Barbarendorf', $r['defender']['village']);
            $this->assertSame('270|435', $r['defender']['coords']);
            $this->assertSame(42, $r['defender']['continent']);

            foreach ($units_attacker as $i => $unit) {
                $this->assertSame($i + 1, $r['troops']['att_'.$unit]);
                $this->assertSame($i + 13, $r['troops']['attl_'.$unit]);
            }
            foreach ($units_defender as $i => $unit) {
                $this->assertSame($i + 25, $r['troops']['def_'.$unit]);
                $this->assertSame($i + 38, $r['troops']['defl_'.$unit]);
            }

            $this->assertSame(111, $r['spied_resources']['wood']);
            $this->assertSame(222, $r['spied_resources']['loam']);
            $this->assertSame(333, $r['spied_resources']['iron']);

            $this->assertSame(5, $r['buildings']['main']);
            $this->assertSame(1, $r['buildings']['barracks']);
            $this->assertSame(1, $r['buildings']['smith']);
            $this->assertSame(1, $r['buildings']['place']);
            $this->assertSame(1, $r['buildings']['statue']);
            $this->assertSame(2, $r['buildings']['market']);
            $this->assertSame(10, $r['buildings']['wood']);
            $this->assertSame(9, $r['buildings']['loam']);
            $this->assertSame(6, $r['buildings']['iron']);
            $this->assertSame(3, $r['buildings']['farm']);
            $this->assertSame(5, $r['buildings']['storage']);
            $this->assertSame(1, $r['buildings']['hide']);
            $this->assertSame(4, $r['buildings']['wall']);

            $this->assertTrue(!empty($r['spied_troops_out']), 'spied_troops_out not present');
            foreach ($units_spied as $i => $unit) {
                $this->assertSame($i + 51, $r['spied_troops_out'][$unit]);
            }

            $this->assertSame(537, $r['booty']['wood']);
            $this->assertSame(461, $r['booty']['loam']);
            $this->assertSame(293, $r['booty']['iron']);
            $this->assertSame(1291, $r['booty']['all']);
            $this->assertSame(5810, $r['booty']['max']);
        }
    }
    
    function testRegressionSpiedTroopsWithoutNewline() {
        $units_attacker = array('spear', 'sword', 'axe', 'archer', 'spy', 'light', 'marcher', 'heavy', 'ram', 'catapult', 'knight', 'snob');
        $units_defender = $units_attacker;
        $units_defender[] = 'militia';
        $units_spied = $units_attacker;
        $dsBericht = new dsBericht(array('attacker' => $units_attacker, 'defender' => $units_defender));

        $raw = <<<REPORT
Spionage
Einheiten außerhalb:
10	20	30	40	50	60	70	80	90	100	110	120
REPORT;
        $raw = trim($raw); // No newline or whitespace at the end!
        
        // Test for the regression.
        try {
            $dsBericht->parse($raw);
        } catch (RuntimeException $e) {
            if ($e->getMessage() == 'Number of spied units differs from number of expected units.') {
                $this->fail('Regression test has failed, i.e. a bug has reappeared.');
            }
        }
        
        // Test that the fixed regex correctly parses unit numbers with
        // more than 1 digit (like 10, 20 and so on).
        $r = $dsBericht->getReport();
        foreach ($units_spied as $i => $unit) {
            $this->assertSame(($i + 1) * 10, $r['spied_troops_out'][$unit]);
        }
    }
}
