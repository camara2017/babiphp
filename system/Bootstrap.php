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
* @package       system
* @since         BabiPHP v 0.8.2
* @license       http://www.gnu.org/licenses/ GNU License
*/

/**
* BabiPHP Bootstrap file.
* 
* Not edit and delete this file
*
*/

	if (!defined('DS')) {
		define('DS', DIRECTORY_SEPARATOR);
	}

	if (!defined('ROOT')) {
		define('ROOT', dirname(dirname(__FILE__)));
	}

	// system folder
	$system_path = 'system';

	// application folder
	$app_folder = 'app';

	// assets folder
	$asset_folder = 'public';

	// Is the system path correct?
	if ( ! is_dir(ROOT. DS . $system_path)) {
		exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: ".pathinfo(__FILE__, PATHINFO_BASENAME));
	}

	// Path to the system folder
	define('BASEPATH', ROOT.DS.$system_path.DS);

	// The path to the "application" folder
	if ( !is_dir(ROOT. DS . $app_folder) ) {
		exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: ".SELF);
	}
	
	// Path to the application folder
	define('APPPATH', ROOT.DS.$app_folder.DS);

	// The path to the "assets" folder
	if ( !is_dir(ROOT. DS . $asset_folder) ) {
		exit('Your assets folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF);
	}
	
	// Path to the assets folder
	define('WWW_ROOT', '/'.$asset_folder.'/');

	// Path to the temporary files directory
	define('CORE', BASEPATH.'Core'.DS);

	// The PHP file extension
	define('EXT', '.php');

	// Include application core
	require_once CORE.'Application'.EXT;