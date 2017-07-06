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
*/

namespace BabiPHP\Core;

use BabiPHP\Component\Config\Config;
use BabiPHP\Component\Exception\BpException;
use BabiPHP\Component\Http\Response;
use BabiPHP\Component\Translation\Localization;
use BabiPHP\Component\Session\Session;
use BabiPHP\Component\Auth\Authentication;
use BabiPHP\Component\Misc\Cookie;
use BabiPHP\Component\Misc\Set;
use BabiPHP\Component\Misc\Debugbar;
use BabiPHP\Component\View\Compilers\BladeCompiler as ViewCompiler;
use BabiPHP\Component\View\Compilers\BladeDirectivesExtended;
use BabiPHP\Component\View\Engines\CompilerEngine as ViewCompilerEngine;
use BabiPHP\Component\View\FileViewFinder as ViewFinder;
use BabiPHP\Component\View\Factory as ViewFactory;
use BabiPHP\Component\View\Template as ViewTemplate;
use BabiPHP\Component\Routing\Router;

class Controller
{
    /**
        * active layout.
        * 
        * @var string
        */
    private $layout;

    /**
        * The page Content type
        * 
        * @var string
        */
    private $contentType = 'text/html';

    /**
        * The encoding charset
        * 
        * @var string
        */
    private $charset = 'UTF-8';

    /**
        * active layout paths.
        * 
        * @var string
        */
    private $templatePath;

    /**
        * active view paths.
        * 
        * @var string
        */
    private $viewPath;

    /**
        * active view extension
        * 
        * @var string
        */
    private $viewExt;

    /**
        * active view.
        * 
        * @var string
        */
    private $view;

    /**
        * variables send to view
        * 
        * @var array
        */
    private $data = [];

    /**
        * View compiler instance
        * 
        * @var \BabiPHP\Component\View\Compilers\BladeCompiler
        */
    private $compiler;

    /**
        * file view loader instance
        * 
        * @var \BabiPHP\Component\View\Filesystem
        */
    private $finder;

    /**
        * factory view loader instance
        * 
        * @var \BabiPHP\Component\View\Factory
        */
    private $factory;

    /**
        * current request header.
        * 
        * @var string
        */
    private $currentHeader = '';

    /**
    * current request instance.
    * 
    * @var \BabiPHP\Component\Http\Request
    */
    protected $request;

    /**
    * current responce instance.
    * 
    * @var \BabiPHP\Component\Http\Response
    */
    protected $Response;

    /**
    * @var \BabiPHP\Component\Routing\Router
    */
    protected $router;
    
    /**
     * Constructor
     *
     * @param Request $request
     * @param Response $response
     */
    public function __construct($request, $response)
    {
        // Set request
        $this->request = $request;

        // set response
        $this->response = $response;

        // Set router
        $this->router = Router::getInstance();

        // Set page charset
        $this->charset = Config::get('charset');

        // Set layout path
        $this->templatePath = APPPATH.'templates';

        // set view path
        $this->viewPath = APPPATH.'views';

        // Set Current layout
        $this->layout = APP_TEMPLATE;

        // Set View extension
        $this->viewExt = Config::get('view_ext');

        // View cache path
        $view_cache_path = APPPATH.'cache'.DS.'view';

        // Create view cache path if not exist
        if (!is_dir($view_cache_path)) {
            mkdir($view_cache_path, 0777);
        }

        /* View compiler & directives extended */
        $this->compiler = new ViewCompiler($view_cache_path);
        $directives_extended = new BladeDirectivesExtended($this->compiler);
        $directives_extended->boot();

        // View finder
        $this->finder = new ViewFinder([$this->viewPath, $this->templatePath]);

        $engine = new ViewCompilerEngine($this->compiler);

        // View factory
        $this->factory = new ViewFactory($engine, $this->finder);

        // Initialization
        $this->auth = new Authentication($this->request);
        $this->session = Session::GetInstance();
        $this->cookie = Cookie::GetInstance();
        $this->set = new Set();

        // Localization
        if(Config::get('localization') === true) {
            $this->translate = Localization::getInstance();
            $this->translate->Setup();
        }

        // Include the accesscontrol module
        require_once COMPONENT.'Security'.DS.'Firewall'.EXT;

        // Include the Hooks
        if (Config::get('enable_hooks') === true) {
            require_once APPPATH.'hooks'.DS.'Hooks'.EXT;
        }
    }

    /**
     * Permet de définir l'entête de la page demandée 
     *
     * @param string $header
     * @return this
     */
    public function setHeader($header)
    {
        if(is_string($header)) {
            $this->currentHeader = $header;
        } else {
            Debugbar::addError('This ContentType "'.$value.'" is not valid, should be an string');
        }

        return $this;
    }

    /**
     * Permet de définir le type de contenu
     *
     * @param string $value
     * @return Controller
     */
    public function setContentType(string $value)
    {
        if(is_string($value)) {
            $this->contentType = $value;
        } else {
            Debugbar::addError('This ContentType "'.$value.'" is not valid, should be an string');
        }

        return $this;
    }

    /**
     * Permet de définir l'encodage de l'application
     *
     * @param string $charset
     * @return Controller
     */
    public function setCharset(string $charset)
    {
        if(is_string($charset)) {
            $this->charset = $charset;
        } else {
            Debugbar::addError('This charset "'.$charset.'" is not valid, should be an string');
        }

        return $this;
    }

    /**
     * Permet de définir le template à utiliser
     *
     * @param string $layout
     * @return Controller
     */
    public function setTemplate(string $layout)
    {
        $files = $this->finder->getFilesystem();

        if($files->exists(APPPATH.'layout/'.$layout.'.layout.tpl')) {
            $this->layout = $layout;
        } else {
            Debugbar::addError('This layout "'.$layout.'" does not exist');
        }

        return $this;
    }

    /**
     * Permet de définir le repertoire des Templates
     *
     * @param string $path
     * @return Controller
     */
    public function setTemplatePath(string $path)
    {
        $path = trim($path, '/');

        if (is_dir(APPPATH.$path)) {
            $this->templatePath = APPPATH.$path.DS;
        } else {
            Debugbar::addError('This layout path "'.$path.'" is not a directory');
        }

        return $this;
    }

    /**
     * Permet de partager des variables avec la vue
     *
     * @param string $key
     * @param mixed $value
     * @return Controller
     */
    public function share(string $key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
    * @see $this->make()
    */
    public function render($view, $data = [])
    {
        $this->make($view, $data);
    }

    /**
     * Permet de générer la page demandée
     *
     * @param string $view
     * @param array $data
     * @return void
     */
    public function make(string $view, array $data = [])
    {
        $this->view = $view;
        $this->data = array_merge($this->data, $data);
        $this->data['session'] = $this->session;

        $header = ($this->currentHeader) ? $this->currentHeader : 'Content-type: '.$this->contentType.'; charset='.$this->charset;
        $content = $this->factory->make($this->view, $this->data)->render();
        ViewTemplate::setOutput('view_content', $content);

        $this->header($header);

        echo $this->factory->make($this->layout, $this->data, [])->render();
    }

    /**
        * getView
        * @param  string $view
        * @param  array $data
        * @return mixed
        */
    public function getView(string $view, array $data = [])
    {
        $exist = false;

        foreach ($this->viewExt as $key => $ext) {
            if(file_exists(APPPATH.'views/'.$view.'.'.$ext)) {
                $exist = true;
                $data = array_merge($this->data, $data);
                break;
            }
        }

        return ($exist) ? $this->factory->make($view, $data)->render() : null;
    }

    /**
     * Register a handler for custom directives.
     *
     * @param  string $name
     * @param  callable $handler
     * @return void
     */
    public function addViewDirective(string $name , callable $handler)
    {
        $this->compiler->directive($name, $handler);
    }

    /**
        * Register an extension with the view finder.
        *
        * @param  string  $extension
        * @return void
        */
    public function addViewExtensions($extensions = [])
    {
        if (!empty($extensions)) {
            return false;
        }
        
        foreach ($extensions as $key => $extension) {
            $this->finder->addExtension($extension);
        }
    }

    /**
        * jsonRender
        * @param array $data
        */
    public function jsonRender($data)
    {
        if(is_array($data)) {
            echo $this->response->jsonEncode($data);
        } else {
            Debugbar::addError('JsonRender value "'.$data.'" is not valid, should be an array');
            echo json_encode(null);
        }
    }

    /**
     * setAutoRender
     * 
     * @param boolean $value
     */
    public function setAutoRender(bool $value)
    {
        if(is_bool($value)) {
            $this->autoRender = $value;
        } else {
            Debugbar::addError('setAutoRender() param "'.$value.'" is not valid, should be a boolean');
        }
    }

    /**
     * header
     * 
     * @param string $header
     */
    public function header(string $header)
    {
        $this->response->header($header);
    }

    /**
     * Redirects the request to the current URL
     *
     * @return Controller
     */
    public function refresh()
    {
        $this->response->redirect($this->request->uri());

        return $this;
    }

    /**
     * Redirects the request back to the referrer
     *
     * @return Controller
     */
    public function back()
    {
        $referer = $this->request->server()->get('HTTP_REFERER');

        if (null !== $referer) {
            $this->response->redirect($referer);
        } else {
            $this->refresh();
        }

        return $this;
    }

    /**
     * Permet de récuperer un model
     * 
     * @param string $model
     */
    public function model(string $model)
    {
        $model_file = APPPATH.'models'.DS.ucfirst($model).EXT;

        if(file_exists($model_file)) {
            require_once $model_file;
            return new $model();
        } else {
            throw new BpException('Try to instanciate not existing '.ucfirst($model).' Model.');
        }
    }

    /**
     * Find
     * 
     * @param string $file
     * @param array $data
     * @param bool $return
     */
    private function find(string $file, $data = null, $return = false)
    {
        $helper = ucfirst(strtolower($file));
        $file = $helper.EXT;
        $path1 = APPPATH.'Helpers'.DS.$file;
        $path2 = COMPONENT.'Helper'.DS.$file;

        if(file_exists($path1)) {
            require $path1;
            $helperClass = $helper;
        }
        elseif(file_exists($path2)) {
            require $path2;
            $helperClass = '\\BabiPHP\Component\Helper\\'.$helper;
        }
        else {
            throw new BpException('Helper '.$file.' file not found.', 4);
        }

        if ($return) {
            return new $helperClass($data);
        } else {
            $this->$helper = new $helperClass($data);
        }
    }
}