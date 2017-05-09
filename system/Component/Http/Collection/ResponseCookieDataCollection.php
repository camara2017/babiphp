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
 * BabiPHP ResponseCookieDataCollection Class.
 * 
 * Not edit this file
 *
 */

    namespace BabiPHP\Component\Http\Collection;

    use BabiPHP\Component\Http\ResponseCookie;

    /**
     * ResponseCookieDataCollection
     *
     * A DataCollection for HTTP response cookies
     */
    class ResponseCookieDataCollection extends DataCollection
    {

        /**
         * Methods
         */

        /**
         * Constructor
         *
         * @override (doesn't call our parent)
         * @param array $cookies The cookies of this collection
         */
        public function __construct(array $cookies = array())
        {
            foreach ($cookies as $key => $value) {
                $this->set($key, $value);
            }
        }

        /**
         * Set a cookie
         *
         * {@inheritdoc}
         *
         * A value may either be a string or a ResponseCookie instance
         * String values will be converted into a ResponseCookie with
         * the "name" of the cookie being set from the "key"
         *
         * Obviously, the developer is free to organize this collection
         * however they like, and can be more explicit by passing a more
         * suggested "$key" as the cookie's "domain" and passing in an
         * instance of a ResponseCookie as the "$value"
         *
         * @see DataCollection::set()
         * @param string $key                   The name of the cookie to set
         * @param ResponseCookie|string $value  The value of the cookie to set
         * @return ResponseCookieDataCollection
         */
        public function set($key, $value)
        {
            if (!$value instanceof ResponseCookie) {
                $value = new ResponseCookie($key, $value);
            }

            return parent::set($key, $value);
        }
    }
