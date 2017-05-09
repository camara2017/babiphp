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
* @since         BabiPHP v 0.1 (Simple file before v 0.7.5)
* @license       http://www.gnu.org/licenses/ GNU License
*/

    use BabiPHP\Core\Application;

    require_once __DIR__.'/../system/Bootstrap.php';

	// register autoloader class
	Application::registerAutoloader();

	// Instantiate a BabiPHP application
	$app = new Application();

	// Run application
	$app->run();