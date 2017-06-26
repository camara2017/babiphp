<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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
 * @package       app.config
 * @since         BabiPHP v 0.1
 * @license       http://www.gnu.org/licenses/ GNU License
 *
 * 
 * Not edit this file
 *
 */

use \BabiPHP\Component\Config\Config;
	

/*
|--------------------------------------------------------------------------------------------|
| Database Configuration : Réglages SQL - Votre hébergeur doit vous fournir ces informations |
|--------------------------------------------------------------------------------------------|
*/

// Databases availables
Config::set('databases', array(
		'local' => array(
			'driver' 		=> 'mysql',
			'persistent' 	=> true,
			'host' 			=> 'localhost',
			'port' 			=> '3306',
			'name' 			=> '',
			'user' 			=> '',
			'pass' 			=> '',
			'charset'		=> 'utf8',
			'prefix' 		=> ''
		)
	)
);

// Default Database
Config::set('default_database', 'local');