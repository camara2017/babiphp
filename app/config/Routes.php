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
 * BabiPHP Routes File
 *
 */

use BabiPHP\Component\Routing\Router;

/*
|--------------------------------------------------------------------------
| Router Configuration
|--------------------------------------------------------------------------
*/

Router::Map('GET|POST', '/', 'home@index', 'home');
Router::Map('GET', '/demo', 'home@demo', 'demo');
Router::Map('POST', '/ajax', 'home@ajax', 'ajax');