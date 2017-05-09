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
*/

/**
* Not edit this file
*/

    namespace BabiPHP\Core;

    use \BabiPHP\Component\Config\Config;
    use \BabiPHP\Component\Exception\BpException;
    use \BabiPHP\Component\Helper\Markup;
    use \BabiPHP\Component\Helper\Html;
    use \BabiPHP\Component\Http\Response;
    use \BabiPHP\Component\Locale\Localization;
    use \BabiPHP\Component\Misc\Session;
    use \BabiPHP\Component\Misc\Cookie;
    use \BabiPHP\Component\Misc\Set;
    use \BabiPHP\Component\Misc\Auth;
    use \BabiPHP\Component\Misc\Debugbar;
    use \BabiPHP\Component\View\Compilers\BladeCompiler as ViewCompiler;
    use \BabiPHP\Component\View\Engines\CompilerEngine as ViewCompilerEngine;
    use \BabiPHP\Component\View\FileViewFinder as ViewFinder;
    use \BabiPHP\Component\View\Factory as ViewFactory;

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
        private $layoutPath;

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
        protected $Request;

        /**
         * current responce instance.
         * 
         * @var \BabiPHP\Component\Http\Response
         */
        protected $Response;

        
        /**
         * Controller Constructor
         * @param $request
         * @param $response
         */
        public function __construct($request, $response)
        {
            // Set request
            $this->Request = $request;

            // set response
            $this->Response = $response;

            // Set page charset
            $this->charset = Config::get('charset');

            // Set layout path
            $this->layoutPath = APPPATH.'layout';

            // set view path
            $this->viewPath = APPPATH.'views';

            // Set Current layout
            $this->layout = APP_TEMPLATE;

            // Set View extension
            $this->viewExt = Config::Get('view_ext');

            /* View compiler*/
            $this->compiler = new ViewCompiler(APPPATH.'cache'.DS.'view');

            // View finder
            $this->finder = new ViewFinder([$this->viewPath, $this->layoutPath]);

            $engine = new ViewCompilerEngine($this->compiler);

            // View factory
            $this->factory = new ViewFactory($engine, $this->finder);

            // Initialization
            $this->Auth = new Auth($this->Request);
            $this->Session = Session::GetInstance();
            $this->Cookie = Cookie::GetInstance();
            $this->Markup = new Markup();
            $this->Html = new Html($this->data, $this->factory);
            $this->Set = new Set();

            // Localization
            if(Config::get('localization') === true) {
                $this->Translate = Localization::getInstance();
                $this->Translate->Setup();
            }

            // Include the accesscontrol module
            require_once COMPONENT.'Security'.DS.'Firewall'.EXT;

            // Include the Hooks
            if (Config::get('enable_hooks') === true) {
                require_once APPPATH.'hooks'.DS.'Hooks'.EXT;
            }
        }

        /**
         * setContentType
         * @param string $value
         * @return $this
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
         * setContentType
         * @param string $value
         * @return $this
         */
        public function setContentType($value)
        {
            if(is_string($value)) {
                $this->contentType = $value;
            } else {
                Debugbar::addError('This ContentType "'.$value.'" is not valid, should be an string');
            }

            return $this;
        }

        /**
         * setCharset
         * @param string $charset
         * @return $this
         */
        public function setCharset($charset)
        {
            if(is_string($charset)) {
                $this->charset = $charset;
            } else {
                Debugbar::addError('This charset "'.$charset.'" is not valid, should be an string');
            }

            return $this;
        }

        /**
         * setLayout
         * @param string $layout
         * @return $this
         */
        public function setLayout($layout)
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
         * setLayoutPath
         * @param string $path
         * @return $this
         */
        public function setLayoutPath($path)
        {
            $path = trim($path, '/');

            if (is_dir(APPPATH.$path)) {
                $this->layoutPath = APPPATH.$path.DS;
            } else {
                Debugbar::addError('This layout path "'.$path.'" is not a directory');
            }

            return $this;
        }

        /**
        * share the variables into view
        * @param array
        * @return $this
        */
        public function share($key, $value = null)
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
         * Get the evaluated view contents for the given view.
         *
         * @param  string  $view
         * @param  array   $data
         * @return View
         */
        public function make($view, $data = [])
        {
            $this->view = $view;
            $this->data = array_merge($this->data, $data);
            $this->data['html'] = $this->Html;
            $this->data['session'] = $this->Session;

            $header = ($this->currentHeader) ? $this->currentHeader : 'Content-type: '.$this->contentType.'; charset='.$this->charset;
            $mergeData['layout_content'] = $this->factory->make($this->view, $this->data)->render();

            $this->header($header);

            echo $this->factory->make($this->layout, $this->data, $mergeData)->render();
        }

        /**
         * getView
         * @param  string $view
         * @param  array $data
         * @return mixed
         */
        public function getView($view, $data = [])
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
         * @param  string  $name
         * @param  callable  $handler
         * @return void
         */
        public function addViewDirective($name , callable $handler)
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
                echo $this->Response->jsonEncode($data);
            } else {
                Debugbar::addError('JsonRender value "'.$data.'" is not valid, should be an array');
                echo json_encode(null);
            }
        }

        /**
        * setAutoRender
        * @param boolean $value
        */
        public function setAutoRender($value)
        {
            if(is_bool($value)) {
                $this->autoRender = $value;
            } else {
                Debugbar::addError('setAutoRender() param "'.$value.'" is not valid, should be a boolean');
            }
        }

        /**
         * header
         * @param string $header
         */
        public function header($header)
        {
            $this->Response->header($header);
        }

        /**
         * Redirects the request to the current URL
         *
         * @return ServiceProvider
         */
        public function refresh()
        {
            $this->Response->redirect($this->Request->uri());

            return $this;
        }

        /**
         * Redirects the request back to the referrer
         *
         * @return ServiceProvider
         */
        public function back()
        {
            $referer = $this->Request->server()->get('HTTP_REFERER');

            if (null !== $referer) {
                $this->Response->redirect($referer);
            } else {
                $this->refresh();
            }

            return $this;
        }

        /**
         * Model
         * @param string $model
         */
        public function model($model)
        {
            $model_path = APPPATH.'models'.DS.$model.EXT;

            if(file_exists($model_path)) {
                require_once $model_path;
                return new $model();
            } else {
                return null;
            }
        }

        /**
        * LoadHelper
        */
        public function helper()
        {
            $nb_arg = func_num_args();
            if($nb_arg > 0)
            {
                for ($i = 0; $i < $nb_arg; $i++)
                {
                    try {
                        $this->find(func_get_arg($i));
                    } catch(BPException $e) {
                        echo $e->OutputError();
                    }
                }
            }
        }

        /**
         * Find
         * @param string $file
         * @param array $data
         */
        private function find($file, $data = null, $return = false)
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

        /**
         * getArrayKeys
         * @param array $array
         */
        public function getArrayKeys($array)
        {
            foreach ($array as $k => $v)
            {
                if(is_array($v))
                    $k = array_keys($v);

                $d[] = $k;
            }

            return $d;
        }

    }
    
?>