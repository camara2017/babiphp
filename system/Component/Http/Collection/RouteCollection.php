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

    use BabiPHP\Component\Routing\Route;

    /**
     * RouteCollection
     *
     * A DataCollection for Routes
     */
    class RouteCollection extends DataCollection
    {

        /**
         * Methods
         */

        /**
         * Constructor
         *
         * @override (doesn't call our parent)
         * @param array $routes The routes of this collection
         */
        public function __construct(array $routes = array())
        {
            foreach ($routes as $value) {
                $this->add($value);
            }
        }

        /**
         * Set a route
         *
         * {@inheritdoc}
         *
         * A value may either be a callable or a Route instance
         * Callable values will be converted into a Route with
         * the "name" of the route being set from the "key"
         *
         * A developer may add a named route to the collection
         * by passing the name of the route as the "$key" and an
         * instance of a Route as the "$value"
         *
         * @see DataCollection::set()
         * @param string $key                   The name of the route to set
         * @param Route|callable $value         The value of the route to set
         * @return RouteCollection
         */
        public function set($key, $value)
        {
            if (!$value instanceof Route) {
                $value = new Route($value);
            }

            return parent::set($key, $value);
        }

        /**
         * Add a route instance to the collection
         *
         * This will auto-generate a name
         *
         * @param Route $route
         * @return RouteCollection
         */
        public function addRoute(Route $route)
        {
            /**
             * Auto-generate a name from the object's hash
             * This makes it so that we can autogenerate names
             * that ensure duplicate route instances are overridden
             */
            $name = spl_object_hash($route);

            return $this->set($name, $route);
        }

        /**
         * Add a route to the collection
         *
         * This allows a more generic form that
         * will take a Route instance, string callable
         * or any other Route class compatible callback
         *
         * @param Route|callable $route
         * @return RouteCollection
         */
        public function add($route)
        {
            if (!$route instanceof Route) {
                $route = new Route($route);
            }

            return $this->addRoute($route);
        }

        /**
         * Prepare the named routes in the collection
         *
         * This loops through every route to set the collection's
         * key name for that route to equal the routes name, if
         * its changed
         *
         * Thankfully, because routes are all objects, this doesn't
         * take much memory as its simply moving references around
         *
         * @return RouteCollection
         */
        public function prepareNamed()
        {
            // Create a new collection so we can keep our order
            $prepared = new static();

            foreach ($this as $key => $route) {
                $route_name = $route->getName();

                if (null !== $route_name) {
                    // Add the route to the new set with the new name
                    $prepared->set($route_name, $route);
                } else {
                    $prepared->add($route);
                }
            }

            // Replace our collection's items with our newly prepared collection's items
            $this->replace($prepared->all());

            return $this;
        }
    }
