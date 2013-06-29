<?php
    // copyright by Robert Nitsch (2006, 2013)
    
    /*
        Description:
        This script is only for testing purposes. Here you can test everything what our "reportparser" class (class.dsBericht.php) does...
    */
    error_reporting(E_ALL);
    
    define('INC_CHECK_DSBERICHT',TRUE);
    define('DSBERICHT_DEBUG',TRUE); // activates the class' own debug mode
    require_once('../include/class.dsBericht.php');
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<form action="" method="post">
    Bericht:<br />
    <textarea name="report" cols="100" rows="20"><?php if(isset($_POST['report'])) echo $_POST['report']; ?></textarea>
    <br />
    <select name="server">
       <option value="normal" <?php if ($_POST['server'] == 'normal') { ?>selected="selected"<?php } ?>>normal</option>
       <option value="modern" <?php if (empty($_POST['server']) || $_POST['server'] == 'modern') { ?>selected="selected"<?php } ?>>modern</option>
       <option value="s4" <?php if ($_POST['server'] == 's4') { ?>selected="selected"<?php } ?>>s4</option>
    </select>
    <input type="checkbox" id="wood" name="wood" value="yes" <?php if (empty($_POST['wood']) || $_POST['wood'] == 'yes') { ?>checked="checked"<?php } ?> /><label for="wood">Holz</label>
    <input type="checkbox" id="loam" name="loam" value="yes" <?php if (empty($_POST['loam']) || $_POST['loam'] == 'yes') { ?>checked="checked"<?php } ?> /><label for="loam">Lehm</label>
    <input type="checkbox" id="iron" name="iron" value="yes" <?php if (empty($_POST['iron']) || $_POST['iron'] == 'yes') { ?>checked="checked"<?php } ?> /><label for="iron">Eisen</label>
    <input type="submit" value="Einlesen" />
</form>

<hr />
<p>Result with dsBericht class version <?php echo DSBERICHT_VERSION; ?>:</p>
<pre>
<?php
    if(isset($_POST['report']))
    {
        $units = array();
        switch($_POST['server'])
        {
            case 'normal':
                $units = array('spear', 'sword', 'axe', 'spy', 'light', 'heavy', 'ram', 'catapult', 'snob');
                break;
            case 'modern':
                $units = array('spear', 'sword', 'axe', 'archer', 'spy', 'light', 'marcher', 'heavy', 'ram', 'catapult', 'knight', 'snob');
                break;
            case 's4':
                $units = array('spear', 'sword', 'axe', 'spy', 'light', 'heavy', 'ram', 'catapult', 'knight', 'snob');
                break;
            default:
                die("ungültiger server typ");
        }
        
        $spied_resources = array();
        if ($_POST['wood'] == 'yes') $spied_resources[] = 'wood';
        if ($_POST['loam'] == 'yes') $spied_resources[] = 'loam';
        if ($_POST['iron'] == 'yes') $spied_resources[] = 'iron';
        $parser=new dsBericht($units, $spied_resources);
        $parser->parse($_POST['report']);
    }
?>
</pre>


</body>
</html>