<?php
	define('CFG_ENABLED', false);
	define('INC_CHECK',true);
	$root_path='../';
	include($root_path.'include/config.inc.php');
	
	// Output-Control anlegen
	$output = new nopSmarty();

	// den Header inkludieren
	$output->assign('root_path',$root_path);
	$output->assign('title','Map Combiner');
	
	// Inhalt
	if(!CFG_ENABLED)
	{
		$output->display('offline.tpl');
	}
	else
	{
        $pic_hash = md5($_SERVER['REMOTE_ADDR']);
        $pic_exists = file_exists($root_path.'cache/map_combine/dsplus_map_'.$pic_hash.'.png');
        
        if(isset($_GET['merge']) and !$pic_exists)
        {
            $errors=array();
            $debuginfo[]=array();
            
            if(checkUploadedFile('karte1') && checkUploadedFile('karte2'))
            {
                    $karte1 = imagecreatefrompng($_FILES['karte1']['tmp_name']);
                    $karte2 = imagecreatefrompng($_FILES['karte2']['tmp_name']);
                    $karte3 = checkUploadedFile('karte3') ? (imagecreatefrompng($_FILES['karte3']['tmp_name'])) : false;

                    $karte2 = makeTransparent(&$karte2);
                    $resultimg = $karte1;
                    imagecopymerge($resultimg, $karte2, 0, 0, 0, 0, 1000, 1000, 100);

                    
                    // ggf. auch noch karte 3 reinkopieren
                    if($karte3 !== false)
                    {
                        $karte3 = makeTransparent(&$karte3);
                        imagecopymerge($resultimg, $karte3, 0, 0, 0, 0, 1000, 1000, 100);
                    }
                    
                    imagepng($resultimg, $root_path.'cache/map_combine/dsplus_map_'.$pic_hash.'.png');
            }
            else
            {
                $errors[] = 'Es müssen mindestens 2 Karten im Format 1000x1000 Pixel hochgeladen werden!';
            }
            
            if(count($errors) > 0)
            {
                $output->assign('error', $errors);
                $output->display('display_errors.tpl');
                exit;
            }
            
            // weiterleiten zur anzeige
            header('Location: map_combine.php?show');
            exit;
        }
        elseif(isset($_GET['show']) or $pic_exists)
        {
            if(!$pic_exists)
            {
                $errors[] = 'Du hast keine Karte kombiniert oder die Karte konnte nicht erstellt werden!';
                $output->assign('error', $errors);
                $output->display('display_errors.tpl');
                exit;
            }
            
            $output->assign('pic_hash', $pic_hash);
            if($pic_exists and !isset($_GET['show']))
                $output->assign('show_limit', true);
            $output->display('map_combine_show.tpl');
        }
        else
        {
            $output->display('map_combine.tpl');
        }
	}
    
    function checkUploadedFile($name)
    {
        global $errors;
        if(isset($_FILES[$name]) && $_FILES[$name]['size'] > 0)
        {
            $size = getimagesize($_FILES[$name]['tmp_name']);
            if($size[0] == 1000 && $size[1] == 1000)
                return true;
            else
                $errors[] = 'Die Maße der hochgeladenen Karten müssen 1000x1000 Pixel entsprechen!';
        }
        
        return false;
    }
    
    function makeTransparent($img)
    {
        $new = imagecreatetruecolor(1000,1000);
        $trans = imagecolorallocate($new, 180, 0, 0);
        imagecolortransparent($new, $trans);

        imagecopy($new, $img, 0, 0, 0, 0, 1000, 1000);
        
        return $new;
    }
?>