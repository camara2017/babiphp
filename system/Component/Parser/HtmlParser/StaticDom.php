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
* @package       system.core.utility.parser.htmlparser
* @since         BabiPHP v 0.8.8
* @license       http://www.gnu.org/licenses/ GNU License
*/

/**
* 
* Not edit this file
*
*/

    namespace BabiPHP\Component\Parser\HtmlParser;

    use BabiPHP\Component\Parser\HtmlParser\Exceptions\NotLoadedException;

    /**
     * Class StaticDom
     *
     * @package PHPHtmlParser
     */
    final class StaticDom
    {

        private static $dom = null;

        /**
         * Attempts to call the given method on the most recent created dom
         * from bellow.
         *
         * @param string $method
         * @param array $arguments
         * @throws NotLoadedException
         * @return mixed
         */
        public static function __callStatic($method, $arguments)
        {
            if (self::$dom instanceof Dom) {
                return call_user_func_array([self::$dom, $method], $arguments);
            } else {
                throw new NotLoadedException('The dom is not loaded. Can not call a dom method.');
            }
        }

        /**
         * Call this to mount the static facade. The facade allows you to use
         * this object as a $className.
         *
         * @param string $className
         * @param Dom $dom
         * @return bool
         */
        public static function mount($className = 'Dom', Dom $dom = null)
        {
            if (class_exists($className)) {
                return false;
            }
            class_alias(__CLASS__, $className);
            if ($dom instanceof Dom) {
                self::$dom = $dom;
            }

            return true;
        }

        /**
         * Creates a new dom object and calls load() on the
         * new object.
         *
         * @param string $str
         * @return $this
         */
        public static function load($str)
        {
            $dom       = new Dom;
            self::$dom = $dom;

            return $dom->load($str);
        }

        /**
         * Creates a new dom object and calls loadFromFile() on the
         * new object.
         *
         * @param string $file
         * @return $this
         */
        public static function loadFromFile($file)
        {
            $dom       = new Dom;
            self::$dom = $dom;

            return $dom->loadFromFile($file);
        }

        /**
         * Creates a new dom object and calls loadFromUrl() on the
         * new object.
         *
         * @param string $url
         * @param array $options
         * @param CurlInterface $curl
         * @return $this
         */
        public static function loadFromUrl($url, $options = [], CurlInterface $curl = null)
        {
            $dom       = new Dom;
            self::$dom = $dom;
            if (is_null($curl)) {
                // use the default curl interface
                $curl = new Curl;
            }

            return $dom->loadFromUrl($url, $options, $curl);
        }

        /**
         * Sets the $dom variable to null.
         */
        public static function unload()
        {
            self::$dom = null;
        }
    }
