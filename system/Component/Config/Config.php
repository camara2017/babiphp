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
 * BabiPHP Config Class.
 * 
 * Not edit this file
 *
 */

	namespace BabiPHP\Component\Config;

	class Config extends AbstractConfig
	{
        /**
         * App configurations
         *
         * @type array
         */
		protected static $configs = array(
            'name' => 'BabiPHP',
            'description' => 'The flexible PHP Framework',
            'base_url' => '',
            'uri_protocol' => 'QUERY_STRING',
            'enable_suffix' => false,
            'url_suffix' => '.html',
            'enable_hooks' => true,
            'composer_autoload' => false,
            'index_controller' => 'home',
            'lang' => 'en_US',
            'charset' => 'utf-8',
            'template' => 'default',
            'handled_errors' => false,
            'template_error' => [
                'layout' => 'error', 
                'view' => [
                    'error_app' => 'errors/app',
                    'error_403' => 'errors/403',
                    'error_404' => 'errors/404',
                    'error_410' => 'errors/410'
                ]
            ],
            'view_ext' => ['tpl', 'layout.tpl'],
            'localization' => true,
            'locale_default' => 'en_US',
            'locale_supported' => ['en_US', 'fr_FR', 'fr_CI'],
            'locale_encoding' => 'UTF-8',
            'flash_template' => [],
            'disable_auto_render' => [],
            'helpers' => ['functions'],
            'databases' => [
                'local' => [
                    'driver'        => 'mysql',
                    'persistent'    => true,
                    'host'          => 'localhost',
                    'port'          => '3306',
                    'name'          => '',
                    'user'          => '',
                    'pass'          => '',
                    'charset'       => '',
                    'prefix'        => ''
                ]
            ],
            'default_database' => 'local',
            'login_form' => 'controller#action',
            'access_control' => array(
                'ROLE_ANONYM' => '*',
                'ROLE_ADMIN' => 'controller#action'
            ),
            'admin_role' => ['ROLE_ADMIN'],
            'system_firewall' => ['app', 'system', 'public', 'vendor'],
            'environment' => 'dev',
            'display_error_details' => true,
            'maintenance' => false
        );
	}

?>