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
 * @package       system.core
 * @since         BabiPHP v 0.1
 * @license       http://www.gnu.org/licenses/ GNU License
 *
 * 
 * Not edit this file
 *
 */

namespace BabiPHP\Core;

use BabiPHP\Component\Http\Request;
use BabiPHP\Component\Http\Response;
use BabiPHP\Component\Routing\Router;

class Dispatcher
{
    /**
     * current request instance.
     * 
     * @var \BabiPHP\Component\Http\Request
     */
    private static $request;

    /**
     * Response instance.
     * 
     * @var \BabiPHP\Component\Http\Response
     */
    private $response;

    /**
     * Response instance.
     * 
     * @var \BabiPHP\Component\Routing\Router
     */
    private $router;

    /**
     * Constructor
     */
    function __construct()
    {
        // instantiate the request
        self::$request = new Request($_GET, $_POST, $_COOKIE, $_SERVER, $_FILES);

        // instantiate the response
        $this->response = new Response();

        // instantiate the router
        $this->router = new Router($this->response);
    }

    /**
     * Permet d'orienter la requête vers le bon controller et la bonne methode
     *
     * @return void
     */
    public function dispatch()
    {
        // get the request routed
        self::$request = $this->router->parseRequest(self::$request);

        $controller = $this->loadController(self::$request);
        $action = self::$request->getAction();

        if(in_array($action, array_diff(get_class_methods($controller), get_class_methods('AppController'))))
        {
            call_user_func_array(array($controller, $action), self::$request->getParams());
        } else {
            $this->response->notFound('The page you are looking for could not be found. <b>/'.self::$request->getRoute().'</b>');
        }
    }

    /**
     * Permet de récuperer l'instance de Request
     * 
     * @return BabiPHP\Component\Http\Request
     */
    public static function getRequest()
    {
        return static::$request;
    }

    /**
     * Permet de charger le controlleur demandé
     *
     * @param Request $request
     * @return Page Controller
     */
    private function loadController(Request $request)
    {
        $controller = ucfirst($request->getController());
        $controller_file = APPPATH.'controllers'.DS.$controller.EXT;

        if(file_exists($controller_file)) {
            require $controller_file;
            $instance = new $controller($request, $this->response);

            return $instance;
        } else {
            $this->response->notFound('The page you are looking for could not be found. <b>/'.$request->getRoute().'</b>');
        }
    }
}