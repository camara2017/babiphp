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
 * @since         BabiPHP v 0.8.8
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * BabiPHP Config Class.
 * 
 * Not edit this file
 *
 */

	namespace BabiPHP\Component\Config;

	abstract class AbstractConfig
	{
        /**
         * App configurations
         *
         * @type array
         */
		protected static $configs = array();

        /**
        * NormalizeKey
        */
		protected static function normalizeKey($key)
        {
            return $key;
        }

        /**
        * Set
        */
		public static function set($key, $value)
        {
            self::$configs[self::normalizeKey($key)] = $value;
        }

        /**
        * Get
        */
        public static function get($key, $default = null)
        {
            if (self::exists($key))
            {
                return self::$configs[self::normalizeKey($key)];
            }

            return $default;
        }

        /**
        * All
        */
        public static function all()
        {
            return self::$configs;
        }

        /**
        * exists
        */
        public static function exists($key)
        {
            return array_key_exists(self::normalizeKey($key), self::$configs);
        }

        /**
         * Countable
         */
        public static function count()
        {
            return count(self::$configs);
        }
	}

?>