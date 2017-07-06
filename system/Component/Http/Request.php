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
 * @package       system.component.http
 * @since         BabiPHP v 0.3
 * @license       http://www.gnu.org/licenses/ GNU License
 *
 * 
 * Not edit this file
 */

namespace BabiPHP\Component\Http;

use BabiPHP\Component\Config\Config;
use BabiPHP\Component\Misc\Set;
use BabiPHP\Component\Exception\BpException;
use BabiPHP\Component\Http\Collection\DataCollection;
use BabiPHP\Component\Http\Collection\HeaderDataCollection;
use BabiPHP\Component\Http\Collection\ServerDataCollection;

class Request
{
    /**
     * Unique identifier for the request
     *
     * @type string
     */
    protected $id;

    /**
     * GET (query) parameters
     *
     * @type DataCollection
     */
    protected $gets;

    /**
     * POST parameters
     *
     * @type DataCollection
     */
    protected $posts;

    /**
     * Named parameters
     *
     * @type DataCollection
     */
    protected $params_named;

    /**
     * Client cookie data
     *
     * @type DataCollection
     */
    protected $cookies;

    /**
     * Server created attributes
     *
     * @type ServerDataCollection
     */
    protected $server;

    /**
     * HTTP request headers
     *
     * @type HeaderDataCollection
     */
    protected $headers;

    /**
     * Uploaded temporary files
     *
     * @type DataCollection
     */
    protected $files;

    /**
     * The request body
     *
     * @type string
     */
    protected $body;

    protected $host;
    protected $post;
    protected $get;
    protected $base_url;
    protected $route;
    protected $route_name;
    protected $url;
    protected $controller;
    protected $action;
    protected $params = array();

    /**
     * @var Request
     */
    private static $_instance;

    /**
     * Known handled content types
     *
     * @var array
     */
    protected $knownContentTypes = [
        'application/json',
        'application/xml',
        'text/xml',
        'text/html',
    ];

    /**
     * Constructor
     *
     * Create a new Request object and define all of its request data
     *
     * @param array  $gets
     * @param array  $posts
     * @param array  $cookies
     * @param array  $server
     * @param array  $files
     * @param string $body
     */
    public function __construct(
        array $gets = array(),
        array $posts = array(),
        array $cookies = array(),
        array $server = array(),
        array $files = array(),
        $body = null
    ) {
        // Assignment city...
        $this->gets    = new DataCollection($gets);
        $this->posts   = new DataCollection($posts);
        $this->cookies = new DataCollection($cookies);
        $this->server  = new ServerDataCollection($server);
        $this->headers = new HeaderDataCollection($this->server->getHeaders());
        $this->files   = new DataCollection($files);
        $this->body    = $body ? (string) $body : null;

        // Non-injected assignments
        $this->params_named = new DataCollection();

        $set = new Set();
        $query_params = array();
        $uri_protocol = Config::get('uri_protocol');
        $query_string = $this->server->get($uri_protocol);

        $this->host = $this->server->get('HTTP_HOST', '');
        $this->url = $this->scheme().'://'.$this->host.$this->uri();
        $this->post = (!$this->posts->isEmpty()) ? $set->arrayToObject($this->posts->all()) : '';

        if($this->gets->count() == 1 && $this->gets->exists($query_string) && empty($this->gets->get($query_string)))
        {
            parse_str(parse_url($this->url, PHP_URL_QUERY), $query_params);
            $this->get = $set->arrayToObject($query_params);
        } else {
            $this->get = $set->arrayToObject($this->gets->all());
        }

        $route = ($this->server->exists('PATH_INFO')) ? $this->server->get('PATH_INFO') : $query_string;

        $this->base_url = trim(APP_BASE_URL, '/');
        $this->route = ($route) ? trim($route, '/') : '/';

        self::$_instance = $this;
    }

    /**
    * GetInstance
    * @return mixed Request instance
    */
    public static function getInstance()
    {
        return self::$_instance;
    }

    /**
     * Create a new request object using the built-in "superglobals"
     *
     * @link http://php.net/manual/en/language.variables.superglobals.php
     * @return Request
     */
    public static function createFromGlobals()
    {
        // Create and return a new instance of this
        return new static(
            $_GET,
            $_POST,
            $_COOKIE,
            $_SERVER,
            $_FILES,
            null // Let our content getter take care of the "body"
        );
    }

    /**
     * Gets a unique ID for the request
     *
     * Generates one on the first call
     *
     * @param boolean $hash     Whether or not to hash the ID on creation
     * @return string
     */
    public function id($hash = true)
    {
        if (null === $this->id) {
            $this->id = uniqid();

            if ($hash) {
                $this->id = sha1($this->id);
            }
        }

        return $this->id;
    }

    /**
     * Returns the named parameters collection
     *
     * @return BabiPHP\Component\Http\Collection\DataCollection
     */
    public function paramsNamed()
    {
        return $this->params_named;
    }

    /**
     * Returns the cookies collection
     *
     * @return BabiPHP\Component\Http\Collection\DataCollection
     */
    public function cookies()
    {
        return $this->cookies;
    }

    /**
     * Returns the server collection
     *
     * @return BabiPHP\Component\Http\Collection\DataCollection
     */
    public function server()
    {
        return $this->server;
    }

    /**
     * Returns the headers collection
     *
     * @return BabiPHP\Component\Http\Collection\HeaderDataCollection
     */
    public function headers()
    {
        return $this->headers;
    }

    /**
     * Returns the files collection
     *
     * @return BabiPHP\Component\Http\Collection\DataCollection
     */
    public function files()
    {
        return $this->files;
    }

    /**
     * Gets the request body
     *
     * @return string
     */
    public function body()
    {
        // Only get it once
        if (null === $this->body) {
            $this->body = @file_get_contents('php://input');
        }

        return $this->body;
    }

    /**
     * Returns all parameters (GET, POST, named, and cookies) that match the mask
     *
     * Takes an optional mask param that contains the names of any params
     * you'd like this method to exclude in the returned array
     *
     * @see \Klein\DataCollection\DataCollection::all()
     * @param array $mask               The parameter mask array
     * @param boolean $fill_with_nulls  Whether or not to fill the returned array
     *  with null values to match the given mask
     * @return array
     */
    public function params($mask = null, $fill_with_nulls = true)
    {
        /*
            * Make sure that each key in the mask has at least a
            * null value, since the user will expect the key to exist
            */
        if (null !== $mask && $fill_with_nulls) {
            $attributes = array_fill_keys($mask, null);
        } else {
            $attributes = array();
        }

        // Merge our params in the get, post, cookies, named order
        return array_merge(
            $attributes,
            $this->gets->all($mask, false),
            $this->posts->all($mask, false),
            $this->cookies->all($mask, false),
            $this->params_named->all($mask, false) // Add our named params last
        );
    }

    /**
     * Return a request parameter, or $default if it doesn't exist
     *
     * @param string $key       The name of the parameter to return
     * @param mixed $default    The default value of the parameter if it contains no value
     * @return string
     */
    public function param($key, $default = null)
    {
        // Get all of our request params
        $params = $this->params();

        return isset($params[$key]) ? $params[$key] : $default;
    }

    public function setRouteName($name)
    {
        if(is_string($name)) {
            $this->route_name = $name;  
        } else {
            throw new BpException('Route name not valid');
        }

        return $this;
    }

    public function setController($ctrl)
    {
        if(is_string($ctrl)) {
            $this->controller = $ctrl;  
        } else {
            throw new BpException('Controller not valid');
        }

        return $this;
    }

    public function setAction($action)
    {
        if(is_string($action)) {
            $this->action = $action;  
        } else {
            throw new BpException('Action not valid');
        }

        return $this;
    }

    public function setParams($params)
    {
        if(is_array($params)) {
            $this->params = $params;  
        } else {
            throw new Exception('Params format not valid');
        }

        return $this;
    }

    /**
     * Return the base url
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->base_url;
    }

    /**
     * Return the request url
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Return the request route name
     * @return string
     */
    public function getRouteName()
    {
        return $this->route_name;
    }

    /**
     * Return the request current route
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Return the request current controller
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Return the request current action
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Return the request params
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Returns the POST parameters collection
     * @return BabiPHP\Component\Http\Collection\DataCollection
     */
    public function post()
    {
        return $this->post;
    }

    /**
     * Returns the GET parameters collection
     * @return BabiPHP\Component\Http\Collection\DataCollection
     */
    public function get()
    {
        return $this->get;
    }

    /**
     * Return the request controller
     * @return string
     */
    public function getMethod($is = null, $allow_override = true)
    {
        return $this->method($is, $allow_override);
    }

    /**
     * Return the request header specifiat parts 
     * @return string
     */
    public function getHeaderLine($part)
    {
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        } else {
            $headers = $this->getallheaders();
        }

        return $headers[$part];
    }

    /**
    * Get request URI
    */
    public function getUri()
    {
        return $this->uri();
    }

    /**
    * Referer
    * @return string
    */
    public function getReferer()
    {
        $referer = $this->Request->server()->get('HTTP_REFERER');
        return (null !== $referer) ? $referer : $this->uri();
    }

    /**
     * Gets the request host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Gets the request domain
     *
     * @return string
     */
    public function getDomain($length = 1)
    {
        $segments = explode('.', $this->host);
        $domain = array_slice($segments, -1 * ($length + 1));

        return implode('.', $domain);
    }

    /**
     * Gets the request subdomain
     *
     * @return string
     */
    public function getSubDomains($length = 1)
    {
        $segments = explode('.', $this->host);

        return array_slice($segments, 0, -1 * ($length + 1));
    }

    /**
     * Gets the request IP address
     *
     * @return string
     */
    public function getIp()
    {
        if ($this->server->exists('HTTP_CLIENT_IP')) {
            return $this->server->get('HTTP_CLIENT_IP');
        } elseif ($this->server->exists('HTTP_X_FORWARDED_FOR')) {
            return $this->server->get('HTTP_X_FORWARDED_FOR');
        } else {
            return $this->server->get('REMOTE_ADDR');
        }
    }

    /**
     * Is the request secure?
     *
     * @return boolean
     */
    public function isSecure()
    {
        return ($this->server->get('HTTPS') == true);
    }

    /**
     * Gets the request user agent
     *
     * @return string
     */
    public function userAgent()
    {
        return $this->headers->get('USER_AGENT');
    }

    /**
     * Gets the request URI
     *
     * @return string
     */
    public function uri()
    {
        return $this->server->get('REQUEST_URI', '/');
    }

    /**
     * Gets the request SCHEME
     *
     * @return string
     */
    public function scheme()
    {
        return $this->server->exists('HTTPS') ? 'https' : 'http';
    }

    /**
     * Get the request's pathname
     *
     * @return string
     */
    public function pathname()
    {
        $uri = $this->uri();

        // Strip the query string from the URI
        $uri = strstr($uri, '?', true) ?: $uri;

        return $uri;
    }

    /**
     * Gets the request method, or checks it against $is
     *
     * <code>
     * // POST request example
     * $request->method() // returns 'POST'
     * $request->method('post') // returns true
     * $request->method('get') // returns false
     * </code>
     *
     * @param string $is                The method to check the current request method against
     * @param boolean $allow_override   Whether or not to allow HTTP method overriding via header or params
     * @return string|boolean
     */
    public function method($is = null, $allow_override = true)
    {
        $method = $this->server->get('REQUEST_METHOD', 'GET');

        // Override
        if ($allow_override && $method === 'POST') {
            // For legacy servers, override the HTTP method with the X-HTTP-Method-Override header or _method parameter
            if ($this->server->exists('X_HTTP_METHOD_OVERRIDE')) {
                $method = $this->server->get('X_HTTP_METHOD_OVERRIDE', $method);
            } else {
                $method = $this->param('_method', $method);
            }

            $method = strtoupper($method);
        }

        // We're doing a check
        if (null !== $is) {
            return strcasecmp($method, $is) === 0;
        }

        return $method;
    }

    /**
     * Determine which content type we know about is wanted using Accept header
     *
     * Note: This method is a bare-bones implementation designed specifically for
     * Slim's error handling requirements. Consider a fully-feature solution such
     * as willdurand/negotiation for any other situation.
     *
     * @return string
     */
    public function contentType()
    {
        $acceptHeader = $this->getHeaderLine('Accept');
        $selectedContentTypes = array_intersect(explode(',', $acceptHeader), $this->knownContentTypes);

        if (count($selectedContentTypes)) {
            return current($selectedContentTypes);
        }

        // handle +json and +xml specially
        if (preg_match('/\+(json|xml)/', $acceptHeader, $matches)) {
            $mediaType = 'application/' . $matches[1];
            if (in_array($mediaType, $this->knownContentTypes)) {
                return $mediaType;
            }
        }

        return 'text/html';
    }

    /**
     * Adds to or modifies the current query string
     *
     * @param string $key   The name of the query param
     * @param mixed $value  The value of the query param
     * @return string
     */
    public function query($key, $value = null)
    {
        $query = array();

        parse_str(
            $this->server()->get('QUERY_STRING'),
            $query
        );

        if (is_array($key)) {
            $query = array_merge($query, $key);
        } else {
            $query[$key] = $value;
        }

        $request_uri = $this->uri();

        if (strpos($request_uri, '?') !== false) {
            $request_uri = strstr($request_uri, '?', true);
        }

        return $request_uri . (!empty($query) ? '?' . http_build_query($query) : null);
    }

    /**
     * isGet
     * @param  string  $key
     * @return boolean
     */
    public function isGet($key = null)
    {
        if($key) {
            return isset($this->get->$key) ? true : false;
        } else {
            return (empty($this->get)) ? false : true ;
        }
    }
    
    /**
     * isPost
     * @param  string  $key
     * @return boolean
     */
    public function isPost($key = null)
    {
        if($key) {
            return isset($this->post->$key) ? true : false;
        } else {
            return (empty($this->post)) ? false : true;
        }
    }

    /**
     * isAjaxRequest
     * check if its an ajax request
     * @return boolean
     */
    public function isAjax()
    {
        $rtn = ($this->server->exists('HTTP_X_REQUESTED_WITH') && strtolower($this->server->get('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest') ? true : false;

        return $rtn;
    }

    public function getallheaders() { 
        $headers = [];

        foreach ($_SERVER as $name => $value) 
        { 
            if (substr($name, 0, 5) == 'HTTP_') 
            { 
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
            } 
        } 
        
        return $headers; 
    }
}