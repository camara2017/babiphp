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

    namespace BabiPHP\Component\Parser\HtmlParser\Dom;

    /**
     * Class TextNode
     *
     * @package BabiPHP\Component\Parser\HtmlParser\Dom
     */
    class TextNode extends LeafNode
    {

        /**
         * This is a text node.
         *
         * @var Tag
         */
        protected $tag;

        /**
         * This is the text in this node.
         *
         * @var string
         */
        protected $text;

        /**
         * This is the converted version of the text.
         *
         * @var string
         */
        protected $convertedText = null;

        /**
         * Sets the text for this node.
         *
         * @param string $text
         */
        public function __construct($text)
        {
            // remove double spaces
            $text = mb_ereg_replace('\s+', ' ', $text);

            // restore line breaks
            $text = str_replace('&#10;', "\n", $text);

            $this->text = $text;
            $this->tag  = new Tag('text');
            parent::__construct();
        }

        /**
         * Returns the text of this node.
         *
         * @return string
         */
        public function text()
        {
            // convert charset
            if ( ! is_null($this->encode)) {
                if ( ! is_null($this->convertedText)) {
                    // we already know the converted value
                    return $this->convertedText;
                }
                $text = $this->encode->convert($this->text);

                // remember the conversion
                $this->convertedText = $text;

                return $text;
            } else {
                return $this->text;
            }
        }

        /**
         * This node has no html, just return the text.
         *
         * @return string
         * @uses $this->text()
         */
        public function innerHtml()
        {
            return $this->text();
        }

        /**
         * This node has no html, just return the text.
         *
         * @return string
         * @uses $this->text()
         */
        public function outerHtml()
        {
            return $this->text();
        }

        /**
         * Call this when something in the node tree has changed. Like a child has been added
         * or a parent has been changed.
         */
        protected function clear()
        {
            $this->convertedText = null;
        }
    }
