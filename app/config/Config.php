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
 */

/**
 * BabiPHP Application Configuration File.
 * 
 * Not edit this file
 *
 */

	use \BabiPHP\Component\Config\Config;
	
/*
|--------------------------------------------------------------------------
| Site Title
|--------------------------------------------------------------------------
*/
	Config::set('name', 'BabiPHP 0.8.8');

/*
|--------------------------------------------------------------------------
| Site Description
|--------------------------------------------------------------------------
*/
	Config::set('description', 'The flexible PHP Framework');
	
/*
|--------------------------------------------------------------------------
| Base Site URL (*) without scheme (http:// or https://)
|--------------------------------------------------------------------------
*/

	Config::set('base_url', 'localhost/babiphp/0.8.8/');
	
/*
|--------------------------------------------------------------------------
| URI PROTOCOL
|--------------------------------------------------------------------------
|
| This item determines which server global should be used to retrieve the
| URI string.  The default setting of 'REQUEST_URI' works for most servers.
| If your links do not seem to work, try one of the other delicious flavors:
|
| 'REQUEST_URI'    Uses $_SERVER['REQUEST_URI']
| 'QUERY_STRING'   Uses $_SERVER['QUERY_STRING']
| 'PATH_INFO'      Uses $_SERVER['PATH_INFO']
|
| WARNING: If you set this to 'PATH_INFO', URIs will always be URL-decoded!
*/

	Config::set('uri_protocol', 'QUERY_STRING');
	
/*
|--------------------------------------------------------------------------
| URL suffix
|--------------------------------------------------------------------------
*/

	Config::set('enable_suffix', true);

	Config::set('url_suffix', '.html');
	
/*
|--------------------------------------------------------------------------
| Enable/Disable System Hooks
|--------------------------------------------------------------------------
*/

	Config::set('enable_hooks', true);
	
/*
|--------------------------------------------------------------------------
| Composer auto-loading
|--------------------------------------------------------------------------
*/

	Config::set('composer_autoload', false);
	
/*
|--------------------------------------------------------------------------
| Homepage controller / Default = 'home'
|--------------------------------------------------------------------------
*/
	Config::set('index_controller', 'home');
	
/*
|--------------------------------------------------------------------------
| Site Language / Default = 'en_US'
|--------------------------------------------------------------------------
*/
	Config::set('lang', 'en_US');

/*
|--------------------------------------------------------------------------
| Site Character Set / Default = 'utf-8'
|--------------------------------------------------------------------------
*/
	Config::set('charset', 'utf-8');
	
/*
|--------------------------------------------------------------------------
| Site Template / Default = 'default'
|--------------------------------------------------------------------------
*/
	Config::set('template', 'default');
	
/*
|--------------------------------------------------------------------------
| Error Handling & Templating
|--------------------------------------------------------------------------
*/

	Config::set('handled_errors', false);

	Config::set('template_error', [
		'layout' => 'error', 
		'view' => [
			'error_app' => 'errors/app',
			'error_403' => 'errors/403',
			'error_404' => 'errors/404',
			'error_410' => 'errors/410'
		]
	]);

/*
|--------------------------------------------------------------------------
| View Extension / Default = 'tpl'
|--------------------------------------------------------------------------
*/
	Config::set('view_ext', ['tpl', 'layout.tpl']);

/*
|--------------------------------------------------------------------------
| BabiPHP Application multilanguage
|--------------------------------------------------------------------------
*/
	Config::set('localization', true);

	Config::set('locale_default', 'en_US');

	Config::set('locale_supported', ['en_US', 'fr_FR', 'fr_CI']);

	Config::set('locale_encoding', 'UTF-8');
	
/*
|--------------------------------------------------------------------------
| BabiPHP Application Flash Templates
|--------------------------------------------------------------------------
*/
	Config::set('flash_template', [
		'default' => '<div class="alert alert-{{type}} alert-navbar alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="message">{{icon}} {{message}}</div>
			</div>',
		'attached' => '<div class="abs-notifier abs-notifier-{{type}}">
                <button type="button" class="close dismiss" data-dismiss="abs-notifier" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="icon">{{icon}}</div>
                <div class="message">{{message}}</div>
            </div>'
	]);
	
//-------------------------------------------------------------------------

/*
|--------------------------------------------------------------------------
| BabiPHP Debug Extension / Default = true
|--------------------------------------------------------------------------
*/
	Config::set('disable_auto_render', array(
		'posts#updater/updater_attach',
		'users#profil/message'
	));

/*
| -------------------------------------------------------------------
|  Auto-load Helper Files
| -------------------------------------------------------------------
| Prototype: array('functions');
*/

	Config::set('helpers', array('functions'));

/*
|--------------------------------------------------------------------------
| The login setting
|--------------------------------------------------------------------------
*/
	Config::Set('login_form', 'auth#login');

/*
|--------------------------------------------------------------------------
| A random string used in security hashing methods
|--------------------------------------------------------------------------
*/
	Config::set('access_control', array(
		'ROLE_ANONYM' => '*',
		'ROLE_USER' => 'game',
		'ROLE_ADMIN' => 'admin|cockpit'
	));

/*
|--------------------------------------------------------------------------
| Role level for authentification module
| 
| ROLE_ANONYM, ROLE_USER, ROLE_ADMIN
|--------------------------------------------------------------------------
*/
	Config::set('admin_role', array('ROLE_ADMIN'));
	
/*
|--------------------------------------------------------------------------
| Directory secured bey firewall | example ['application', 'system', 'vendor']
|--------------------------------------------------------------------------
*/
	Config::set('system_firewall', ['app', 'system', 'public', 'vendor']);

/*
|--------------------------------------------------------------------------
| BabiPHP Application environment ('prod' or 'dev') @default: 'dev'
|--------------------------------------------------------------------------
*/
	Config::set('environment', 'dev');

/*
|--------------------------------------------------------------------------
| BabiPHP Application display error details (true or false) @default: false
|--------------------------------------------------------------------------
*/
	Config::set('display_error_details', true);

/*
|--------------------------------------------------------------------------
| BabiPHP Application maintenance state (true or false) @default: false
|--------------------------------------------------------------------------
*/
	Config::set('maintenance', false);
	
//-------------------------------------------------------------------------

?>