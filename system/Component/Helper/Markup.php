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
 * @package       system.helpers
 * @since         BabiPHP v 0.1
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * BabiPHP Html Helper Class.
 * 
 * Not edit this file
 *
 */

    namespace BabiPHP\Component\Helper;

    use BabiPHP\Component\Config\Config;

    class Markup
    {
        private $Data = array();
        private $BlockName;
        private $Vars = array();

        public function __construct($vars = null)
        {
            if($vars) $this->Vars = $vars;
        }

        /**
        * To get an information of Application
        * @param $info
        * @return Info to show on the View
        */
        public function Info($info)
        {
            return Config::Get($info);
        }

        /**
        * Asset
        */
        public function Asset($url)
        {
            $file = trim($url, '/');
            return WEBROOT.$file;
        }

        /**
        * To include an view
        * @param $file
        * @return The content of the view
        */
        public function Inc($file, $ext = 'tpl', $vars = array())
        {
            $file = trim($file, '/');
            $ext = trim($ext, '.');
            if(file_exists(APPPATH.'views'.DS.$file.'.'.$ext))
            {
                $this->Vars = array_merge($this->Vars, $vars);
                extract($this->Vars);
                ob_start();
                require APPPATH.'views'.DS.$file.'.'.$ext;
                return ob_get_clean();
            }
        }

        /**
        * Url
        */
        public function Url($u = null)
        {
            $u = trim($u, '/');
            if($u === null) return APP_BASE_URL.'/';
            return APP_BASE_URL.'/'.$u;
        }
    }

?>