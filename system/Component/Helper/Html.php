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
 * @package       system.components.helpers
 * @since         BabiPHP v 0.1
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * Not edit this file
 */

    namespace BabiPHP\Component\Helper;

    use BabiPHP\Component\Config\Config;

    class Html
    {
        /**
         * blockName
         * 
         * @var string
         */
        private $blockName;

        /**
         * variables send to view
         * 
         * @var array
         */
        private $data = [];

        /**
         * factory view loader instance
         * 
         * @var \BabiPHP\Component\View\Factory
         */
        private $factory;

        /**
         * asset path
         * 
         * @var string
         */
        private $webroot;

        /**
         * Constructor
         *
         * Create a new Html object
         *
         * @param array  $data
         * @param BabiPHP\Component\View\Factory  $factory
         */
        public function __construct($data = null, $factory)
        {
            if($data) {
                $this->data = $data;
            }

            $this->factory = $factory;

            $this->webroot = ROOT.DS.WWW_ROOT;
        }

        /**
        * Set Data
        */
        private function share($key, $value=null)
        {
            if (is_array($key)) {
                $this->data = array_merge($this->data, $key);
            } else {
                $this->data[$key] = $value;
            }

            return $this;
        }

        /**
        * Doctype
        */
        public function doctype($name)
        {
            $doctypes = array(
                'xhtml11'       => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
                'xhtml1-strict' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
                'xhtml1-trans'  => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
                'xhtml1-frame'  => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
                'html4-strict'  => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
                'html4-trans'   => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
                'html4-frame'   => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
                'html5'         => '<!DOCTYPE html>'
            );

            return $doctypes[$name];
        }

        /**
        * To get an information of Application
        * @param $info
        * @return Info to show on the View
        */
        public function info($info)
        {
            return Config::Get($info);
        }

        /**
        * Css
        */
        public function css($file)
        {
            $file = trim($file, '/');
            if(file_exists($this->webroot.'css/'.$file.'.css')) {
                return '<link href="'.WEBROOT.'css/'.$file.'.css" rel="stylesheet" type="text/css">';
            }
        }

        /**
        * Jscript
        */
        public function jscript($file)
        {
            $file = trim($file, '/');
            if(file_exists($this->webroot.'js/'.$file.'.js')) {
                return '<script src="'.WEBROOT.'js/'.$file.'.js" type="text/javascript"></script>';
            }
        }

        /**
        * Img
        */
        public function img($file)
        {
            if(file_exists($this->webroot.'img/'.$file)) {
                $file = trim($file, '/');
                return '<img src="'.WEBROOT.'img/'.$file.'" alt="'.$file.'">';
            }
        }

        /**
        * Asset
        */
        public function asset($file)
        {
            return WEBROOT.trim($file, '/');
        }

        /**
        * To include an view
        * @param $file
        * @return The content of the view
        */
        public function include($view, $data = [], $ext = 'tpl')
        {
            $exist = false;
            $view = trim($view, '/');
            $ext = trim($ext, '.');

            if(file_exists(APPPATH.'views/'.$view.'.'.$ext))
            {
                $exist = true;

                if (is_array($data)) {
                    $this->data = array_merge($this->data, $data);
                }
            }

            return ($exist) ? $this->factory->make($view, $data)->render() : null;
        }

        /**
        * Url
        */
        public function url($url = null)
        {
            $url = trim($url, '/');
            return ($url) ? APP_BASE_URL.'/'.$url : APP_BASE_URL;
        }
        
        /**
        * assign
        * @param $name
        * @param $content
        */
        public function assign($name, $content)
        {
            $this->share($name, $content);

            return $this;
        }
        
        /**
        * Block
        * @param $name
        */
        public function block($name)
        {
            $this->blockName = $name;
            ob_start();
        }

        /**
        * EndBlock
        */
        public function endBlock()
        {
            $content = ob_get_clean();
            $old_content = ($this->has($this->blockName)) ? $this->data[$this->blockName] : '';

            $this->share($this->blockName, $old_content.$content);
            $this->blockName = null;
        }

        /**
        * Fetch
        * @param $name
        * @return The content of $name
        */
        public function fetch($name, $default = null)
        {
            if ($this->has($name)) {
                $fetch = $this->data[$name];
            } else {
                $fetch = $default;
            }

            return $fetch;
        }

        /**
         * Escapes a string for UTF-8 HTML displaying
         *
         * This is a quick macro for escaping strings designed
         * to be shown in a UTF-8 HTML environment. Its options
         * are otherwise limited by design
         *
         * @param string $str   The string to escape
         * @param int $flags    A bitmask of `htmlentities()` compatible flags
         * @return string
         */
        public function escape($str, $flags = ENT_QUOTES, $encode = 'UTF-8')
        {
            return htmlentities($str, $flags, $encode);
        }

        /**
         * normalizeKey
         * @param  string  $key
         * @return string
         */
        protected function normalizeKey($key)
        {
            return $key;
        }

        /**
         * Does this set contain a key?
         * @param  string  $key The data key
         * @return boolean
         */
        private function has($key)
        {
            return array_key_exists($key, $this->data);
        }
    }

?>