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
* @since         BabiPHP v 0.8.8
* @license       http://www.gnu.org/licenses/ GNU License
*/

/**
* 
* Not edit this file
*
*/

    namespace BabiPHP\Component\Parser\HtmlParser;

    /**
     * Class Options
     *
     * @package PHPHtmlParser
     * @property bool whitespaceTextNode
     * @property bool strict
     * @property bool enforceEncoding
     */
    class Options
    {

        /**
         * The default options array
         *
         * @param array
         */
        protected $defaults = [
            'whitespaceTextNode' => true,
            'strict'             => false,
            'enforceEncoding'    => null,
            'cleanupInput'       => true,
            'removeScripts'      => true,
            'removeStyles'       => true,
            'preserveLineBreaks' => false,
        ];

        /**
         * The list of all current options set.
         *
         * @param array
         */
        protected $options = [];

        /**
         * Sets the default options in the options array
         */
        public function __construct()
        {
            $this->options = $this->defaults;
        }

        /**
         * A magic get to call the get() method.
         *
         * @param string $key
         * @return mixed
         * @uses $this->get()
         */
        public function __get($key)
        {
            return $this->get($key);
        }

        /**
         * Sets a new options param to override the current option array.
         *
         * @param array $options
         * @return $this
         */
        public function setOptions(array $options)
        {
            foreach ($options as $key => $option) {
                $this->options[$key] = $option;
            }

            return $this;
        }

        /**
         * Gets the value associated to the key, or null if the key is not
         * found.
         *
         * @param string
         * @return mixed
         */
        public function get($key)
        {
            if (isset($this->options[$key])) {
                return $this->options[$key];
            }

            return null;
        }
    }
