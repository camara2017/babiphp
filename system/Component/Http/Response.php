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
 * @since         BabiPHP v 0.1
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * BabiPHP Response Class.
 * 
 * Not edit this file
 *
 */

    namespace BabiPHP\Component\Http;

    use BabiPHP\Component\Config\Config;
    use BabiPHP\Component\Misc\Set;
    use BabiPHP\Component\Misc\Debugbar;
    use BabiPHP\Component\Exception\BpException;
    use BabiPHP\Core\Controller;

    class Response extends AbstractResponse
    {
        /**
         * HTTP request
         *
         * @type Request
         */
        protected $request = null;

        /**
         * Set tool
         *
         * @type Set
         */
        protected $set;

        /**
         * Status code
         *
         * @var int
         */
        protected $status = 200;

        /**
         * Reason phrase
         *
         * @var string
         */
        protected $reasonPhrase = '';

        /**
         * Status codes and reason phrases
         *
         * @var array
         */
        protected $messages = [
            //Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            //Successful 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            208 => 'Already Reported',
            226 => 'IM Used',
            //Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
            //Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            421 => 'Misdirected Request',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            444 => 'Connection Closed Without Response',
            451 => 'Unavailable For Legal Reasons',
            499 => 'Client Closed Request',
            //Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            510 => 'Not Extended',
            511 => 'Network Authentication Required',
            599 => 'Network Connect Timeout Error',
        ];

        /**
         * Constructor
         */
        public function __construct($body = '', $status_code = null)
        {
            $this->hydrateSelfRequest();
            
            parent::__construct($body, $status_code, $this->request->server()->getHeaders());

            $this->set = new Set();
        }

        public function hydrateSelfRequest()
        {
            $this->request = Request::getInstance();

            return $this->request;
        }

        /**
        * JsonEncode
        */
        public function jsonEncode($value)
        {
            $this->header('Content-type: application/json');
            return json_encode($value);
        }

        /**
        * Forbidden
        */
        public function Forbidden($message = null)
        {
            $this->redirect403($message);
        }

        /**
        * NotFound
        * @param string $message
        */
        public function notFound($message = null)
        {
            if (Config::get('handled_errors')) {
                $this->redirect404($message);
            } else {
                $this->defaultNotFound();
            }
        }

        /**
        * Redirect403
        * @param string $message
        */
        public function redirect403($message = null)
        {
            $d['page'] = new \stdClass;
            $d['page']->title = $this->status()->getMessageFromCode(404);
            $d['message'] = ($message) ? $message : $d['page']->title;
            
            $error = $this->set->ArrayToObject(Config::get('template_error'));
            $ctl = new Controller($this->request, $this);
            $ctl->setHeader('HTTP/1.0 403 Forbidden')
                ->setLayout($error->layout)
                ->render($error->view->error_403, $d);

            if(Debugbar::$activate) {
                echo Debugbar::Render(Request::getInstance());
            }

            exit;
        }

        /**
        * Redirect404
        * @param string $message
        */
        public function redirect404($message = null)
        {
            $d['page'] = new \stdClass;
            $d['page']->title = $this->status()->getMessageFromCode(404);
            $d['message'] = ($message) ? $message : $d['page']->title;
            
            $error = $this->set->ArrayToObject(Config::get('template_error'));
            $ctl = new Controller($this->request, $this);
            $ctl->setHeader('HTTP/1.0 404 Not Found')
                ->setLayout($error->layout)
                ->render($error->view->error_404, $d);

            if(Debugbar::$activate) {
                echo Debugbar::Render(Request::getInstance());
            }

            exit;
        }

        /**
        * Redirect404
        * Deprecated since 0.8.0
        * @param string $message
        */
        public function redirect410($message = null)
        {
            $d['page'] = new \stdClass;
            $d['page']->title = $this->status()->getMessageFromCode(410);
            $d['message'] = ($message) ? $message : $d['page']->title;
            
            $error = $this->set->ArrayToObject(Config::get('template_error'));
            $ctl = new Controller($this->request, $this);
            $ctl->setHeader('HTTP/1.0 410 Gone')
                ->setLayout($error->layout)
                ->render($error->view->error_410, $d);

            if(Debugbar::$activate) {
                echo Debugbar::Render(Request::getInstance());
            }

            exit;
        }

        /**
        * Erreur
        * @param $message to show
        */
        public function Error($message = null)
        {
            $d['page'] = new \stdClass;
            $d['page']->title = $this->status()->getMessageFromCode(10);
            $d['message'] = ($message) ? $message : $d['page']->title;

            $error = $this->set->ArrayToObject(Config::get('template_error'));
            $ctl = new Controller($this->request, $this);
            $ctl->setLayout($error->layout)->render($error->view->error_app, $d);

            if(Debugbar::$activate) {
                echo Debugbar::Render(Request::getInstance());
            }

            exit;
        }

        /**
         * Sends a file
         *
         * It should be noted that this method disables caching
         * of the response by default, as dynamically created
         * files responses are usually downloads of some type
         * and rarely make sense to be HTTP cached
         *
         * Also, this method removes any data/content that is
         * currently in the response body and replaces it with
         * the file's data
         *
         * @param string $path      The path of the file to send
         * @param string $filename  The file's name
         * @param string $mimetype  The MIME type of the file
         * @return Response
         */
        public function file($path, $filename = null, $mimetype = null)
        {
            $this->body('');
            $this->noCache();

            if (null === $filename) {
                $filename = basename($path);
            }
            if (null === $mimetype) {
                $mimetype = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
            }

            $this->header('Content-type', $mimetype);
            $this->header('Content-length', filesize($path));
            $this->header('Content-Disposition', 'attachment; filename="'.$filename.'"');

            $this->send();

            readfile($path);

            return $this;
        }

        /**
         * Enable response chunking
         *
         * @link https://github.com/chriso/klein.php/wiki/Response-Chunking
         * @link http://bit.ly/hg3gHb
         * @param string $str   An optional string to send as a response "chunk"
         * @return Response
         */
        public function chunk($str = null)
        {
            parent::chunk();

            if (null !== $str) {
                printf("%x\r\n", strlen($str));
                echo "$str\r\n";
                flush();
            }

            return $this;
        }

        /**
         * Dump a variable
         *
         * @param mixed $obj    The variable to dump
         * @return Response
         */
        public function dump($obj)
        {
            if (is_array($obj) || is_object($obj)) {
                $obj = print_r($obj, true);
            }

            $this->append('<pre>' .  htmlentities($obj, ENT_QUOTES) . "</pre><br />\n");

            return $this;
        }

        /**
         * Filter HTTP status code.
         *
         * @param  int $status HTTP status code.
         * @return int
         * @throws \InvalidArgumentException If an invalid HTTP status code is provided.
         */
        protected function filterStatus($status)
        {
            if (!is_integer($status) || $status<100 || $status>599) {
                throw new \InvalidArgumentException('Invalid HTTP status code');
            }

            return $status;
        }

        /**
         * Gets the response reason phrase associated with the status code.
         *
         * Because a reason phrase is not a required element in a response
         * status line, the reason phrase value MAY be null. Implementations MAY
         * choose to return the default RFC 7231 recommended reason phrase (or those
         * listed in the IANA HTTP Status Code Registry) for the response's
         * status code.
         *
         * @link http://tools.ietf.org/html/rfc7231#section-6
         * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
         * @return string Reason phrase; must return an empty string if none present.
         */
        public function getReasonPhrase()
        {
            if ($this->reasonPhrase) {
                return $this->reasonPhrase;
            }
            if (isset($this->$messages[$this->status])) {
                return $this->$messages[$this->status];
            }
            return '';
        }

        public function defaultNotFound()
        {
            $request = $this->hydrateSelfRequest();
        	$contentType = $request->contentType();

        	switch ($contentType) {
                case 'application/json':
                    $output = self::renderJsonNotFound();
                break;
                case 'text/xml':
                case 'application/xml':
                    $output = self::renderXmlNotFound();
                break;
                case 'text/html':
                    $output = self::renderHtmlNotFound($request->getBaseUrl());
                break;
                default:
                    throw new BpException('Cannot render unknown content type '.$contentType);
            }
            
            $this->header('HTTP/1.0 404 Not Found');
            $this->header('Content-type', $contentType);
        	echo $output;

            exit;
        }

        private function renderHtmlNotFound($base_url)
        {
        	$title = 'Page Not Found';
            $html = '<p>The page you are looking for could not be found. Check the address bar to ensure your URL is spelled correctly. If all else fails, you can visit our home page at the link below.</p>';
            $url = '<a href="'.$base_url.'">Visit the Home Page</a>';

            $output = sprintf(
                "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
                "<title>%s</title><style>body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana," .
                "sans-serif;}h1{margin:0;font-size:48px;font-weight:normal;line-height:48px;}strong{" .
                "display:inline-block;width:65px;}</style></head><body><h1>%s</h1>%s<p>%s</p></body></html>",
                $title,
                $title,
                $html,
                $url
            );

            return $output;
        }

        private function renderJsonNotFound()
        {
        	$error = [
                'error' => voidClass([
                	'title' => 'Page Not Found',
                	'message' => 'The page you are looking for could not be found.'
                ])
            ];

            return json_encode($error, JSON_PRETTY_PRINT);
        }

        private function renderXmlNotFound()
        {
        	$xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $xml .= "<error>\n";
            $xml .= "	<title>Page Not Found</title>\n";
            $xml .= "	<message>The page you are looking for could not be found.</message>\n";
            $xml .= "</error>";

            return $xml;
        }

        public function notAllowed ($request_method ,$methods_allowed)
        {
            if (!in_array($request_method ,$methods_allowed))
            {
            	$contentType = $this->request->contentType();

            	switch ($contentType) {
	                case 'application/json':
	                    $output = self::renderJsonNotAllowedMessage($methods_allowed);
	                break;
	                case 'text/xml':
	                case 'application/xml':
	                    $output = self::renderXmlNotAllowedMessage($methods_allowed);
	                break;
	                case 'text/html':
	                    $output = self::renderHtmlNotAllowedMessage($methods_allowed);
	                break;
	                default:
	                    throw new BpException('Cannot render unknown content type '.$contentType);
	            }

                $this->header('HTTP/1.0 405 Method Not Allowed');
                $this->header('Content-type', $contentType);
                echo $output;

                exit;
            }
        }

        private function renderHtmlNotAllowedMessage($methods)
        {
            $title = 'Method not allowed';
            $html = '<p>Method not allowed. Must be one of: <strong>'.implode(', ', $methods).'</strong></p>';

            $output = sprintf(
                "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
                "<title>%s</title><style>body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana," .
                "sans-serif;}h1{margin:0;font-size:48px;font-weight:normal;line-height:48px;}strong{" .
                "display:inline-block;width:65px;}</style></head><body><h1>%s</h1>%s</body></html>",
                $title,
                $title,
                $html
            );

           return $output;
        }

        private function renderJsonNotAllowedMessage($methods)
        {
            $error = [
                'error' => voidClass([
                	'type' => 'RouterException',
                	'title' => 'Method not allowed',
                	'message' => 'Method not allowed. Must be one of: '.implode(', ', $methods)
                ])
            ];

            return json_encode($error, JSON_PRETTY_PRINT);
        }

        private function renderXmlNotAllowedMessage($methods)
        {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $xml .= "<error>\n";
            $xml .= "  <type>RouterException</type>\n";
            $xml .= "  <title>Method not allowed</title>\n";
            $xml .= "  <message>Method not allowed. Must be one of: ".implode(', ', $methods)."</message>\n";
            $xml .= "</error>";

            return $xml;
        }
    }
    
?>