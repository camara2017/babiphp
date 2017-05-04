<?php
/**
 * BabiPHP : The Simple and Fast Development Framework (http://babiphp.org)
 * Copyright (c) BabiPHP. (http://babiphp.org)
 *
 * Licensed under The GNU General Public License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) BabiPHP. (http://babiphp.org)
 * @link          http://babiphp.org BabiPHP Project
 * @package       system.core
 * @since         BabiPHP v 0.1
 * @license       http://www.gnu.org/licenses/ GNU License
 */

	/**
	 * Dump a variable
     *
     * @param mixed $obj    The variable to dump
	 * @return string
	 */
	function debug($var)
	{
		if (is_string($var) === true)
        {
            $print = htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
        }
        else if (is_numeric($var) === true || is_float($var) === true || is_int($var) === true)
        {
            $print = $var == 0 ? '0' : $var;
        }
        else if (is_bool($var) === true)
        {
            $print = $var === false ? 'false' : 'true';
        }
        else if (is_null($var) === true)
        {
            $print = 'NULL';
        }
        else
        {
            $print = print_r($var, true);

			if ((strstr($print, '<') !== false) || (strstr($print, '>') !== false))
	        {
	            $print = htmlspecialchars($print, ENT_QUOTES, 'UTF-8');
	        }
        }

        $stack = debug_backtrace();
        $trace = array_shift($stack);

		$output = '<div style="background:#f5f5f5;margin: 15px 10px;font: 12px "Helvetica Neue", helvetica, arial, sans-serif;">'.
			'<div style="background:#d5d5d5;padding:10px;font-size:12px;">'.
				'File : <a href="#" style="color:#7300a0;"><b>'.$trace['file'].'</b></a>'.
				'<div style="float: right;">Line : <b style="color: #7300a0;">'.$trace['line'].'</b></div>'.
			'</div>'.
			'<div style="padding-bottom:5px;border:1px solid #eeeeee;">'.
				'<pre style="text-transform:none;margin-bottom:5px;">'.
					'<div style="padding:10px;margin:10px;margin-bottom:5px;border: 1px solid #eeeeee;direction: ltr;background:#ffffff;color:#000;max-height:400px;overflow:auto;text-transform:none;">'.
						'<code>'.$print.'</code>'.
					'</div>'.
				'</pre>'.
				'<div style="display: table;width:100%;color:#999999;font-size:12px;">'.
					'<div style="display:table-cell;width:50%;padding-left:10px;">'.date('Y/m/d H:i:s').'</div>'.
					'<div style="display:table-cell;width:50%;padding-right:10px;text-align:right">'.strlen($print).' characters</div>'.
				'</div>'.
			'</div>'.
			'</div>';

		echo $output;
	}

	/**
	* create one void class
	*
	* @param array
	* @return object
	*/
	function voidClass($array = [])
	{
		$class = new \stdClass;

		foreach ($array as $key => $value) {
			$class->$key = $value;
		}

		return $class;
	}

	/**
    * ArrayToObject
    * @param $array
    * @return Object
    */
    function arrayToObject($array)
    {
        if(is_array($array) && !empty($array))
        {
            $d = new stdClass();

            foreach ($array as $k => $v)
            {
                if(!empty($v) && is_array($v))
                	$v = arrayToObject($v);

                $d->$k = $v;
            }

            return $d;
        }
    }

    /**
    * ObjectToArray
    * @param $object
    * @return Array
    */
    function objectToArray($object)
    {
        if(is_object($object)) {
        	return get_object_vars($object);
		}
    }

	/**
	* GetIp
	* @return ip adress of user
	*/
	function getIp()
	{
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else {
			return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
		}
	}

	function remove_dir($path)
    {
        $handle = opendir($strDirectory);
        while(false !== ($entry = readdir($handle))) {
            if($entry != '.' && $entry != '..') {
                if(is_dir($strDirectory.'/'.$entry)) {
                    rmAllDir($strDirectory.'/'.$entry);
                } elseif (is_file($strDirectory.'/'.$entry)) {
                    unlink($strDirectory.'/'.$entry);
                }
            }
        }
        rmdir($strDirectory.'/'.$entry);
        closedir($handle);

        if (is_dir($path))
        {
        	if ($dh = opendir($path)) {
        		while (($file = readdir($dh)) !== false) {
        			if (is_dir($path.$file) && $file != '..' && $file != '.') {
						remove_dir($path.$file);
        			} elseif ($file != '..' && $file != '.') {
						unlink($path.$file);
					}
        		}
        	}
        	else return 'Can\'t open dir "'.$path.'"';
        }
        else return 'Is not a dir "'.$path.'"';
    }

    function rrmdir($dir)
    {
		if (is_dir($dir)) {
			$objects = scandir($dir);

			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") {
						rmdir($dir."/".$object); 
					} else {
						unlink($dir."/".$object);
					}
				}
			}

			reset($objects);
			rmdir($dir);
		}
	}

	/**
	 * CopyDir
	 * @param string $dir2copy
	 * @param string $dir_paste
	 */
	function copyDir($dir2copy, $dir_paste)
	{
		$dir2copy = trim($dir2copy, '/').'/';
		$dir_paste = trim($dir_paste, '/').'/';

		if (is_dir($dir2copy))
		{
			if ($dh = opendir($dir2copy))
			{
				while (($file = readdir($dh)) !== false)
				{
					if (!is_dir($dir_paste)) {
						mkdir($dir_paste, 0777);
					}

					if(is_dir($dir2copy.$file) && $file != '..' && $file != '.')
						CopyDir($dir2copy.$file.'/', $dir_paste.$file.'/');
					elseif($file != '..' && $file != '.')
						copy($dir2copy.$file, $dir_paste.$file);
				}

				closedir($dh);
				return true;
			}
			else return 'can\'t open dir "'.$dir2copy.'"';
		}
		else return 'is not a dir "'.$dir2copy.'"';
	}

	/**
	 * count_file [count number of file in a folder]
	 * @param  string $dir [folder to count]
	 * @return integer or false [number of file in this folder or false if the dir can't be opened]
	 */
	function count_folder_file($dir)
	{
		if($folder = opendir($dir))
		{
			$nb = 0;
			$ds = DIRECTORY_SEPARATOR;

			while(false !== ($file = readdir($folder)))
			{
				if($file != '.' && $file != '..')
				{
					if(filetype($dir.$ds.$file) == 'dir') {
						$nb += count_folder_file($dir.$ds.$file);
					} else {
						$nb++;
					}
				}
			}

			closedir($folder);

			return $nb;
		}
		else {
			return false;
		}
	}

	function get_folder_file($folder, $nb_file = null, $recursive = false)
	{
		if($dir = opendir($folder))
		{
			$ds = DIRECTORY_SEPARATOR;
			$files = array();
			$albums = array();

			while(false !== ($file = readdir($dir)))
			{
				if($file != '.' && $file != '..')
				{
					if(filetype($folder.$ds.$file) == 'dir')
					{
						$files[$file] = get_folder_file($folder.$ds.$file);
						$albums[] = $file;
					}
					else
						$files[] = $file;
						
					// limit nb file
					if($nb_file !== null && (int)$nb_file == count($files))
						break;
				}
			}

			closedir($dir);

			if($recursive)
				return array('albums' => $albums, 'files' => $files);
			else
				return $files;
		}
		else
			return false;
	}

	/**
	 * cut_txt
	 * @param  string $chaine
	 * @param  interger $max
	 * @param  boolean $exact
	 * @return string
	 */
	function cut_txt($chaine, $max_length, $exact = true, $separ = '...')
	{
		if(strlen($chaine) >= $max_length)
		{
			$chaine = substr($chaine, 0, $max_length);

			if($exact) {
				$chaine = substr($chaine, 0, strrpos($chaine, " "));
			}

			$chaine .= $separ;
		}
		return $chaine;
	}

	function makeSlug($text, $separ = '-', $do_accent = true, $do_determ = true)
	{
		$letters = array('a','e','o','i','u','y','n','ae','c','s','');
		$accents = array(
			array('à','â','ä','å','ã','á'),
			array('é','è','ê','ë'),
			array('ø','ō','ð','ò','ó','ô','õ','ö'),
			array('ì','í','î','ï'),
			array('ù','ú','û','ü'),
			array('ÿ','ý'),
			array('ñ'),
			array('æ'),
			array('ç'),
			array('ß'),
			array('[','*',':','/','\\',',',';','.','+','<','>','+','^','}','$','{','£','"',')','¤','§','=','°','@','\'','~','#','(','&','%',']','!','?')
		);

		$determinents = array();

		$text = str_replace(' ', $separ, strtolower($text));

		if($do_accent === true)
		{
			for ($a=0; $a < count($accents); $a++)
			{
				for ($i=0; $i < count($accents[$a]); $i++)
				{ 
					$text = str_replace($accents[$a][$i], $letters[$a], $text);
				}
			}
		}

		return trim($text, $separ);
	}

	/**
	 * webvideo
	 * @param  string $lien
	 * @param  integer $largeur
	 * @param  integer $hauteur
	 * @return string
	 */
	function webvideo($lien, $largeur=null, $hauteur=null)
	{
	    //on récupère le nom de domaine.
	    $domaine = parse_url ($lien, PHP_URL_HOST);

	    //on définit la largeur et la hauteur.
	    ($largeur != null)? $largeur : $largeur = 425;
	    ($hauteur != null)? $hauteur : $hauteur = 344;
	    
	    switch ($domaine)
	    {
	        case 'www.dailymotion.com':
	            //on récupère la requête.
	            $req = parse_url ($lien, PHP_URL_PATH);
	            $separation = explode ('/',$req);

	            //on récupère l'id de la vidéo.
	            $dernierepartie = array_pop($separation);
	            $id_brute = explode ('_',$dernierepartie);
	            $id = array_shift($id_brute);
	            $valeur = 'http://www.dailymotion.com/swf/'.$id;  
	        break;
	        case 'www.youtube.com':
	            //on récupère la requête.
	            $req = parse_url ($lien, PHP_URL_QUERY);
	            
	            //on récupère l'id de la vidéo.
	            $premièrepartie = strtok($req, '&');
	            $id = strtok($premièrepartie, 'v=');
	            $valeur = 'http://www.youtube.com/v/'.$id.'&hl=fr&fs=1&';
	        break;
	    }

		//on assigne au lecteur la valeur.
		$lecteur = '<object width="'.$largeur.'" height="'.$hauteur.'">
		            <param name="movie" value="'.$valeur.'">
		            </param>
		            <param name="allowFullScreen" value="true">
		            </param>
		            <param name="allowscriptaccess" value="always">
		            </param>
		            <embed src="'.$valeur.'"
		            type="application/x-shockwave-flash"
		            allowscriptaccess="always"
		            allowfullscreen="true"
		            width="'.$largeur.'"
		            height="'.$hauteur.'">
		            </embed></object>';

		return $lecteur;
	}

	/**
	 * Détection automatique de la langue du navigateur
	 *
	 * @param string $locale Langue à choisir par défaut si aucune n'est trouvée
	 * @return string La langue du navigateur ou bien la langue par défaut
	 * @author Lambirou
	 * @version 0.2
	 */
	function getBrowserLocale($locale = 'en_US')
	{
		if(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$d = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$locale = str_replace('-', '_', $d[0]);
		}

		return $locale;
	}

	function get_accepted_languages()
	{
        $httplanguages = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $languages = array();

        if (empty($httplanguages))
            return $languages;
 
        foreach (preg_split('/,\s*/', $httplanguages) as $accept)
        {
            $result = preg_match('/^([a-z]{1,8}(?:[-_][a-z]{1,8})*)(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i', $accept, $match);
 
            if (!$result)
                continue;

            $quality = (isset($match[2])) ? (float)$match[2] : 1.0 ;
            $countries = explode('-', $match[1]);
            $region = array_shift($countries);
            $country_sub = explode('_', $region);
            $region = array_shift($country_sub);
 
            foreach($countries as $country)
                $languages[$region . '_' . strtoupper($country)] = $quality;
 
            foreach($country_sub as $country)
                $languages[$region . '_' . strtoupper($country)] = $quality;
 
            $languages[$region] = $quality;
        }
 
        return $languages;
    }

    /**
    * Returns a DateTime object initialized at the $time param and using UTC
    * as timezone
    *
    * @param string|integer|DateTime $time
    * @return DateTime
    */
    function getUTCDate($time = null)
    {
        if ($time instanceof DateTime) {
            $result = clone $time;
        } elseif (is_int($time)) {
            $result = new DateTime(date('Y-m-d H:i:s', $time));
        } else {
            $result = new DateTime($time);
        }

        $result->setTimeZone(new DateTimeZone('UTC'));

        return $result;
    }

    function isAjaxRequest()
    {
    	return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;
    }

?>