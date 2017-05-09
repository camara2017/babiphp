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
 * @package       system.component.http.collection
 * @since         BabiPHP v 0.8.5
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * Not edit this file
 */

    namespace BabiPHP\Component\Http\Collection;

    /**
     * ServerDataCollection
     *
     * A DataCollection for "$_SERVER" like data
     *
     * Look familiar?
     *
     * Inspired by @fabpot's Symfony 2's HttpFoundation
     * @link https://github.com/symfony/HttpFoundation/blob/master/ServerBag.php
     */
    class ServerDataCollection extends DataCollection
    {

        /**
         * Class properties
         */

        /**
         * The prefix of HTTP headers normally
         * stored in the Server data
         *
         * @type string
         */
        protected static $http_header_prefix = 'HTTP_';

        /**
         * The list of HTTP headers that for some
         * reason aren't prefixed in PHP...
         *
         * @type array
         */
        protected static $http_nonprefixed_headers = array(
            'CONTENT_LENGTH',
            'CONTENT_TYPE',
            'CONTENT_MD5',
        );


        /**
         * Methods
         */

        /**
         * Quickly check if a string has a passed prefix
         *
         * @param string $string    The string to check
         * @param string $prefix    The prefix to test
         * @return boolean
         */
        public static function hasPrefix($string, $prefix)
        {
            if (strpos($string, $prefix) === 0) {
                return true;
            }

            return false;
        }

        /**
         * Get our headers from our server data collection
         *
         * PHP is weird... it puts all of the HTTP request
         * headers in the $_SERVER array. This handles that
         *
         * @return array
         */
        public function getHeaders()
        {
            // Define a headers array
            $headers = array();

            foreach ($this->attributes as $key => $value) {
                // Does our server attribute have our header prefix?
                if (self::hasPrefix($key, self::$http_header_prefix)) {
                    // Add our server attribute to our header array
                    $headers[
                        substr($key, strlen(self::$http_header_prefix))
                    ] = $value;

                } elseif (in_array($key, self::$http_nonprefixed_headers)) {
                    // Add our server attribute to our header array
                    $headers[$key] = $value;
                }
            }

            return $headers;
        }
    }
