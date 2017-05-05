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

    use ArrayAccess;
    use ArrayIterator;
    use Countable;
    use IteratorAggregate;
    use BabiPHP\Component\Parser\HtmlParser\Exceptions\EmptyCollectionException;

    /**
     * Class Collection
     *
     * @package BabiPHP\Component\Parser\HtmlParser\Dom
     */
    class Collection implements IteratorAggregate, ArrayAccess, Countable
    {

        /**
         * The collection of Nodes.
         *
         * @param array
         */
        protected $collection = [];

        /**
         * Attempts to call the method on the first node in
         * the collection.
         *
         * @param string $method
         * @param array $arguments
         * @return mixed;
         * @throws EmptyCollectionException
         */
        public function __call($method, $arguments)
        {
            $node = reset($this->collection);
            if ($node instanceof AbstractNode) {
                return call_user_func_array([$node, $method], $arguments);
            } else {
                throw new EmptyCollectionException('The collection does not contain any Nodes.');
            }
        }

        /**
         * Attempts to apply the magic get to the first node
         * in the collection.
         *
         * @param mixed $key
         * @return mixed
         * @throws EmptyCollectionException
         */
        public function __get($key)
        {
            $node = reset($this->collection);
            if ($node instanceof AbstractNode) {
                return $node->$key;
            } else {
                throw new EmptyCollectionException('The collection does not contain any Nodes.');
            }
        }

        /**
         * Applies the magic string method to the first node in
         * the collection.
         *
         * @return string
         * @throws EmptyCollectionException
         */
        public function __toString()
        {
            $node = reset($this->collection);
            if ($node instanceof AbstractNode) {
                return (string)$node;
            } else {
                throw new EmptyCollectionException('The collection does not contain any Nodes.');
            }
        }

        /**
         * Returns the count of the collection.
         *
         * @return int
         */
        public function count()
        {
            return count($this->collection);
        }

        /**
         * Returns an iterator for the collection.
         *
         * @return ArrayIterator
         */
        public function getIterator()
        {
            return new ArrayIterator($this->collection);
        }

        /**
         * Set an attribute by the given offset
         *
         * @param mixed $offset
         * @param mixed $value
         */
        public function offsetSet($offset, $value)
        {
            if (is_null($offset)) {
                $this->collection[] = $value;
            } else {
                $this->collection[$offset] = $value;
            }
        }

        /**
         * Checks if an offset exists.
         *
         * @param mixed $offset
         * @return bool
         */
        public function offsetExists($offset)
        {
            return isset($this->collection[$offset]);
        }

        /**
         * Unset a collection Node.
         *
         * @param mixed $offset
         */
        public function offsetUnset($offset)
        {
            unset($this->collection[$offset]);
        }

        /**
         * Gets a node at the given offset, or null
         *
         * @param mixed $offset
         * @return mixed
         */
        public function offsetGet($offset)
        {
            return isset($this->collection[$offset]) ? $this->collection[$offset] : null;
        }

        /**
         * Returns this collection as an array.
         *
         * @return array
         */
        public function toArray()
        {
            return $this->collection;
        }

        /**
         * Similar to jQuery "each" method. Calls the callback with each
         * Node in this collection.
         *
         * @param callback $callback
         */
        public function each($callback)
        {
            foreach ($this->collection as $key => $value) {
                $callback($value, $key);
            }
        }
    }
