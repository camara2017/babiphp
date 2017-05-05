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
     * This mock object is used solely for testing the abstract
     * class Node with out any potential side effects caused
     * by testing a supper class of Node.
     *
     * This object is not to be used for any other reason.
     */
    class MockNode extends InnerNode
    {

        /**
         * Mock of innner html.
         */
        public function innerHtml()
        {
        }

        /**
         * Mock of outer html.
         */
        public function outerHtml()
        {
        }

        /**
         * Mock of text.
         */
        public function text()
        {
        }

        /**
         * Clear content of this node
         */
        protected function clear()
        {
            $this->innerHtml = null;
            $this->outerHtml = null;
            $this->text      = null;
        }

        /**
         * Returns all children of this html node.
         *
         * @return array
         */
        protected function getIteratorArray()
        {
            return $this->getChildren();
        }
    }
