<?php
    // copyright by Robert Nitsch, 2006
    
    /*
        Description:
        This script is only for testing purposes. Here you can test everything what our "reportparser" class (class.dsBericht.php) does...
    */
    error_reporting(E_ALL);
    
    define('INC_CHECK_DSBERICHT',TRUE);
    define('DSBERICHT_DEBUG',TRUE); // activates the class' own debug mode
    require('include/class.dsBericht.php');
    require('include/class.DSUnit.php');
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<form action="" method="post">
    Bericht:<br />
    <textarea name="report" cols="70" rows="20"><?php if(isset($_POST['report'])) echo $_POST['report']; ?></textarea>
    <br />
    <select name="server">
       <option value="normal">normal</option>
       <option value="modern">modern</option>
       <option value="s4">s4</option>
    </select>
    <input type="submit" value="Einlesen (testen)" />
</form>

<hr />
<p>Result with dsBericht class version <?php echo DSBERICHT_VERSION; ?> of <?php echo DSBERICHT_DATE; ?>:</p>
<pre>
<?php
    if(isset($_POST['report']))
    {
        $units = array();
        switch($_POST['server'])
        {
            case 'normal':
                $units = array(
                    new DSUnit('spear', 'Speerträger'),
                    new DSUnit('sword', 'Schwertkämpfer'),
                    new DSUnit('axe', 'Axtkämpfer'),
                    new DSUnit('spy', 'Späher'),
                    new DSUnit('light', 'Leichte Kavallerie'),
                    new DSUnit('heavy', 'Schwere Kavallerie'),
                    new DSUnit('ram', 'Rammbock'),
                    new DSUnit('catapult', 'Katapult'),
                    new DSUnit('snob', 'Adelsgeschlecht'));
                break;
            case 'modern':
                $units = array(
                    new DSUnit('spear', 'Speerträger'),
                    new DSUnit('sword', 'Schwertkämpfer'),
                    new DSUnit('axe', 'Axtkämpfer'),
                    new DSUnit('archer', 'Bogenschütze'),
                    new DSUnit('spy', 'Späher'),
                    new DSUnit('light', 'Leichte Kavallerie'),
                    new DSUnit('marcher', 'Berittener Bogenschütze'),
                    new DSUnit('heavy', 'Schwere Kavallerie'),
                    new DSUnit('ram', 'Rammbock'),
                    new DSUnit('catapult', 'Katapult'),
                    new DSUnit('knight', 'Paladin'),
                    new DSUnit('snob', 'Adelsgeschlecht'));
                break;
            case 's4':
                $units = array(
                            new DSUnit('spear', 'Speerträger'),
                            new DSUnit('sword', 'Schwertkämpfer'),
                            new DSUnit('axe', 'Axtkämpfer'),
                            new DSUnit('spy', 'Späher'),
                            new DSUnit('light', 'Leichte Kavallerie'),
                            new DSUnit('heavy', 'Schwere Kavallerie'),
                            new DSUnit('ram', 'Rammbock'),
                            new DSUnit('catapult', 'Katapult'),
                            new DSUnit('knight', 'Priester'),
                            new DSUnit('snob', 'Adelsgeschlecht'));
            default:
                die("ungültiger server typ");
        }
        
        $parser=new dsBericht($units);
        $parser->parse($_POST['report']);
    }
?>
</pre>


</body>
</html>