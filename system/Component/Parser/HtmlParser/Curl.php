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

    use BabiPHP\Component\Parser\HtmlParser\Exceptions\CurlException;

    /**
     * Class Curl
     *
     * @package PHPHtmlParser
     */
    class Curl implements CurlInterface
    {

        /**
         * A simple curl implementation to get the content of the url.
         *
         * @param string $url
         * @return string
         * @throws CurlException
         */
        public function get($url)
        {
            $ch = curl_init($url);

            if ( ! ini_get('open_basedir')) {
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

            $content = curl_exec($ch);
            if ($content === false) {
                // there was a problem
                $error = curl_error($ch);
                throw new CurlException('Error retrieving "'.$url.'" ('.$error.')');
            }

            return $content;
        }
    }
