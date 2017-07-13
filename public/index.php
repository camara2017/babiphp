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
* @since         BabiPHP v 0.1
* @license       MIT
*/

use BabiPHP\Core\Application;

// Show warning if a PHP version below 5.6.0 is used, this has to happen here
// because Application.php will already use 5.6 syntax.
if (version_compare(PHP_VERSION, '5.6.0') === -1) {
	echo 'This version of BabiPHP requires at least PHP 5.6.0<br/>';
	echo 'You are currently running ' . PHP_VERSION . '. Please update your PHP version.';
	return;
}

require_once __DIR__.'/../system/Bootstrap.php';

// register autoloader class
Application::registerAutoloader();

// Instantiate a BabiPHP application
$app = new Application();

// Run application
$app->run();