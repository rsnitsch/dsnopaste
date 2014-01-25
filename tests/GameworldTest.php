<?php
require_once('../include/functions.tribalwars.inc.php');

class GameworldTest extends PHPUnit_Framework_TestCase
{
    public function testNameForID()
    {
        $this->assertSame("dep", Gameworld::nameForID("dep"));
        $this->assertSame("Casual 1", Gameworld::nameForID("dep1"));
        $this->assertSame("Casual 42", Gameworld::nameForID("dep42"));

        $this->assertSame("de", Gameworld::nameForID("de"));
        $this->assertSame("Welt 1", Gameworld::nameForID("de1"));
        $this->assertSame("Welt 42", Gameworld::nameForID("de42"));
    }

    public function testRegressionGetTimePerField()
    {
        $gw = new Gameworld("de95");
        
        $units = array(
            "spear" => 18.000000000504,
            "sword" => 21.999999999296,
            "axe" => 18.000000000504,
            "spy" => 8.99999999928,
            "light" => 9.999999998,
            "heavy" => 11.0000000011,
            "ram" => 29.9999999976,
            "catapult" => 29.9999999976,
            "knight" => 9.999999998,
            "snob" => 34.9999999993
        );

        foreach ($units as $unit => $runtime) {
            $this->assertSame($runtime * 60.0, $gw->getTimePerField(array($unit => 1)));
        }

        $this->assertSame($units["snob"] * 60.0, $gw->getTimePerField(array("spy" => 1, "snob" => 1)));
        $this->assertSame($units["snob"] * 60.0, $gw->getTimePerField(array("snob" => 1, "spy" => 1)));
    }
    
    public function testGetTimePerFieldNegative()
    {
        $this->setExpectedException("InvalidArgumentException", "Negative unit numbers are not allowed.");

        $gw = new Gameworld("de95");
        $gw->getTimePerField(array("snob" => 3, "spy" => -1));
    }

    public function testGetTimePerFieldSumZero()
    {
        $this->setExpectedException("InvalidArgumentException", "Total unit count must be greater than zero.");

        $gw = new Gameworld("de95");
        $gw->getTimePerField(array("snob" => 0, "spy" => 0));
    }
}
