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
 * @package       system.component.routing
 * @since         BabiPHP v 0.7
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * Not edit this file
 */

	namespace BabiPHP\Component\Routing;

	use BabiPHP\Component\Config\Config;
	use BabiPHP\Component\Http\Request;
    use BabiPHP\Component\Http\Response;
    use BabiPHP\Component\Exception\BpException;
	use UnexpectedValueException;

	/**
     * Route
     *
     * Class to represent a router
     */
	class Router
	{
        /**
         * Response instance.
         * 
         * @var \BabiPHP\Component\Http\Response
         */
        private $response;
		/**
		 * @var array Array of all routes (incl. named routes).
		 */
		protected static $routes = array();

		/**
		 * @var array Array of all named routes.
		 */
		protected static $namedRoutes = array();

		/**
		 * @var string Can be used to ignore leading part of the Request URL (if main file lives in subdirectory of host)
		 */
		protected static $basePath = '';

		/**
		 * @var array Array of default match types (regex helpers)
		 */
		protected static $matchTypes = array(
			'i'  => '[0-9]++',
			's'  => '[a-z0-9\-]++',
			'a'  => '[0-9A-Za-z]++',
			'h'  => '[0-9A-Fa-f]++',
			'*'  => '.+?',
			'**' => '.++',
			''   => '[^/\.]++'
		);

		/**
		* Create router in one call from config.
		*
		* @param array $routes
		* @param string $basePath
		* @param array $matchTypes
		*/
		function __construct(Response $response)
		{
			$this->response = $response;
		}

		/**
        * Use to parse an Url
        * @param $Url to parse
        * @return array contain the params
        */
        public function parseRequest (Request $request)
        {
            $route = trim($request->getRoute(), '/');

            if($route)
            {
            	$route_exist = false;

	            foreach (self::$routes as $route_map)
	            {
	            	$c_route = trim($route_map[1], '/');
	            	$regex = self::compileRoute($c_route);

	            	if (preg_match($regex, $route, $match))
                	{
                		$route_exist = true;
                		$this->validateRequestMethod($request, $route_map);

                		$map = explode('@', $route_map[2]);
	            		$controller = $map[0];
	            		$action = isset($map[1]) ? $map[1] : 'index';
	            		$route_name = isset($route_map[3]) ? $route_map[3] : $controller.'_'.$action;

	            		foreach ($match as $k => $route_map) {
	            			$params[$k] = $route_map;
	            		}

	            		if(count(array_slice(array_unique($params), 1)) === 1) {
		            		$a = explode('/', $params[0]);
		            		$params = array_slice(array_unique($params), 1);
		            		$params['page'] = $a[0];
	            		} else {
	            			$params = array_unique(array_slice($params, 1));
	            		}

	            		$request->setRouteName($route_name);
	            		$request->setController($controller);
	            		$request->setAction($action);
	            		$request->setParams($params);
	            	}
	            }

	            if ( !$route_exist )
	            {
					$map = explode('/', $route);
					$controller = $map[0];
					$action = isset($map[1]) ? $map[1] : 'index';

					$request->setRouteName($controller);
					$request->setController($controller);
					$request->setAction($action);
	            }
            } else {
            	$route_map = self::$routes[0];

            	$this->validateRequestMethod($request, $route_map);

            	$map = explode('@', $route_map[2]);
	            $action = isset($map[1]) ? $map[1] : 'index';

	            $request->setRouteName($route_map[3]);
	            $request->setController($map[0]);
	            $request->setAction($action);
            }
            
            return $request;
        }

        /**
         * Validate the current request method
         * @param  BabiPHP\Component\Http\Request
         * @param  array The current route map
         * @return output the error
         */
        private function validateRequestMethod(Request $request, $route)
        {
        	$request_method = strtoupper($request->server()->get('REQUEST_METHOD'));
        	$methods = array_map("strtoupper", explode('|', $route[0]));

            $this->response->notAllowed($request_method ,$methods);
        }

        /**
		 * Return all routes
		 *
		 * @return array
		 */
        public static function getAllRoute()
        {
        	return self::$routes;
        }

        /**
		 * Return all named routes
		 *
		 * @return array
		 */
        public static function getAllnamedRoute()
        {
        	return self::$namedRoutes;
        }

		/**
		 * Add multiple routes at once from array in the following format:
		 *
		 *   $routes = array(
		 *      array($method, $route, $target, $name)
		 *   );
		 *
		 * @param array $routes
		 * @return void
		 * @author Koen Punt
		 */
		public static function addRoutes($routes)
		{
			if(!is_array($routes) && !$routes instanceof Traversable) {
				throw new \Exception('Routes should be an array or an instance of Traversable');
			}

			foreach($routes as $route) {
				call_user_func_array(array(self, 'map'), $route);
			}
		}

		/**
		 * Set the base path.
		 * Useful if you are running your application from a subdirectory.
		 */
		public static function setBasePath($basePath)
		{
			self::$basePath = trim($basePath, '/');
		}

		/**
		 * Add named match types. It uses array_merge so keys can be overwritten.
		 *
		 * @param array $matchTypes The key is the name and the value is the regex.
		 */
		public static function addMatchTypes($matchTypes)
		{
			self::$matchTypes = array_merge(self::$matchTypes, $matchTypes);
		}

		/**
		 * Map a route to a target
		 *
		 * @param string $method One of 5 HTTP Methods, or a pipe-separated list of multiple HTTP Methods (GET|POST|PATCH|PUT|DELETE)
		 * @param string $route The route regex, custom regex must start with an @. You can use multiple pre-set regex filters, like [i:id]
		 * @param mixed $target The target where this route should point to. Can be anything.
		 * @param string $name Optional name of this route. Supply if you want to reverse route this url in your application.
		 */
		public static function Map($method, $route, $target, $name = null)
		{
			self::$routes[] = array($method, $route, $target, $name);

			if($name)
			{
				if(isset(self::$namedRoutes[$name])) {
					throw new BpException("Can not redeclare route '{$name}'");
				} else {
					self::$namedRoutes[$name] = $route;
				}
			}

			return;
		}

		/**
		 * Reversed routing
		 *
		 * Generate the URL for a named route. Replace regexes with supplied parameters
		 *
		 * @param string $routeName The name of the route.
		 * @param array @params Associative array of parameters to replace placeholders with.
		 * @return string The URL of the route with named parameters in place.
		 */
		public static function Url($routeName, array $params = array())
		{
			// Check if named route exists
			if(!isset(self::$namedRoutes[$routeName])) {
				throw new \Exception("Route '{$routeName}' does not exist.");
			}

			// Replace named parameters
			$route = self::$namedRoutes[$routeName];
			
			// prepend base path to route url again
			$url = self::$basePath . $route;

			if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER))
			{
				foreach($matches as $match)
				{
					list($block, $pre, $type, $param, $optional) = $match;

					if ($pre) {
						$block = substr($block, 1);
					}

					if(isset($params[$param])) {
						$url = str_replace($block, $params[$param], $url);
					} elseif ($optional) {
						$url = str_replace($pre . $block, '', $url);
					}
				}
			}

			return $url;
		}

		/**
		 * Match a given Request Url against stored routes
		 * @param string $requestUrl
		 * @param string $requestMethod
		 * @return array|boolean Array with route information on success, false on failure (no match).
		 */
		public static function match($requestUrl = null, $requestMethod = null)
		{
			$params = array();
			$match = false;

			// set Request Url if it isn't passed as parameter
			if($requestUrl === null)
				$requestUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';

			// strip base path from request url
			$requestUrl = substr($requestUrl, strlen(self::$basePath));

			// Strip query string (?a=b) from Request Url
			if (($strpos = strpos($requestUrl, '?')) !== false)
				$requestUrl = substr($requestUrl, 0, $strpos);

			// set Request Method if it isn't passed as a parameter
			if($requestMethod === null)
				$requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';

			// Force request_order to be GP
			// http://www.mail-archive.com/internals@lists.php.net/msg33119.html
			$_REQUEST = array_merge($_GET, $_POST);

			foreach(self::$routes as $handler)
			{
				list($method, $_route, $target, $name) = $handler;

				$methods = explode('|', $method);
				$method_match = false;

				// Check if request method matches. If not, abandon early. (CHEAP)
				foreach($methods as $method)
				{
					if (strcasecmp($requestMethod, $method) === 0)
					{
						$method_match = true;
						break;
					}
				}

				// Method did not match, continue to next route.
				if(!$method_match) continue;

				// Check for a wildcard (matches all)
				if ($_route === '*')
					$match = true;
				elseif (isset($_route[0]) && $_route[0] === '@')
				{
					$pattern = '`' . substr($_route, 1) . '`u';
					$match = preg_match($pattern, $requestUrl, $params);
				}
				else
				{
					$route = null;
					$regex = false;
					$j = 0;
					$n = isset($_route[0]) ? $_route[0] : null;
					$i = 0;

					// Find the longest non-regex substring and match it against the URI
					while (true)
					{
						if (!isset($_route[$i]))
							break;
						elseif (false === $regex)
						{
							$c = $n;
							$regex = $c === '[' || $c === '(' || $c === '.';
							if (false === $regex && false !== isset($_route[$i+1]))
							{
								$n = $_route[$i + 1];
								$regex = $n === '?' || $n === '+' || $n === '*' || $n === '{';
							}
							if (false === $regex && $c !== '/' && (!isset($requestUrl[$j]) || $c !== $requestUrl[$j]))
								continue 2;
							$j++;
						}

						$route .= $_route[$i++];
					}

					$regex = self::compileRoute($route);
					$match = preg_match($regex, $requestUrl, $params);
				}

				if(($match == true || $match > 0))
				{
					if($params)
					{
						foreach($params as $key => $value)
							if(is_numeric($key)) unset($params[$key]);
					}

					return array(
						'target' => $target,
						'params' => $params,
						'name' => $name
					);
				}
			}
			return false;
		}

		/**
		 * Compile the regex for a given route (EXPENSIVE)
		 */
		private static function compileRoute($route)
		{
			if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER))
			{
				$matchTypes = self::$matchTypes;

				foreach($matches as $match)
				{
					list($block, $pre, $type, $param, $optional) = $match;

					if (isset($matchTypes[$type]))
						$type = $matchTypes[$type];
					if ($pre === '.')
						$pre = '\.';

					//Older versions of PCRE require the 'P' in (?P<named>)
					$pattern = '(?:'.($pre !== '' ? $pre : null).'('.($param !== '' ? "?P<$param>" : null).$type.'))'.($optional !== '' ? '?' : null);
					
					$route = str_replace($block, $pattern, $route);
				}

			}
			return "`^$route$`u";
		}
	}
?>