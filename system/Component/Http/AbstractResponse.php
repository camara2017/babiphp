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
 * @since         BabiPHP v 0.8.5
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * Not edit this file
 */

    namespace BabiPHP\Component\Http;

	use BabiPHP\Component\Http\ResponseCookie;
    use BabiPHP\Component\Http\Collection\HeaderDataCollection;
	use BabiPHP\Component\Http\Collection\ResponseCookieDataCollection;
	use BabiPHP\Component\Exception\LockedResponseException;
	use BabiPHP\Component\Exception\ResponseAlreadySentException;

    /**
    * AbstractResponse
    */
    abstract class AbstractResponse
    {
    	/**
         * HTTP request
         *
         * @type Request
         */
        protected $request;

        /**
         * The default response HTTP status code
         *
         * @type int
         */
        protected static $default_status_code = 200;

        /**
         * The HTTP version of the response
         *
         * @type string
         */
        protected $protocol_version = '1.1';

        /**
         * The response body
         *
         * @type string
         */
        protected $body;

        /**
         * HTTP response status
         *
         * @type HttpStatus
         */
        protected $status;

        /**
         * HTTP response headers
         *
         * @type HeaderDataCollection
         */
        protected $headers;

        /**
         * HTTP response cookies
         *
         * @type ResponseCookieDataCollection
         */
        protected $cookies;

	    /**
	     * Whether or not the response is "locked" from
	     * any further modification
	     *
	     * @type boolean
	     */
	    protected $locked = false;

	    /**
	     * Whether or not the response has been sent
	     *
	     * @type boolean
	     */
	    protected $sent = false;

	    /**
	     * Whether the response has been chunked or not
	     *
	     * @type boolean
	     */
	    public $chunked = false;


        /**
	     * Constructor
	     *
	     * Create a new AbstractResponse object with a dependency injected Headers instance
	     *
	     * @param string $body          The response body's content
	     * @param int $status_code      The status code
	     * @param array $headers        The response header "hash"
	     */
	    public function __construct($body = '', $status_code = null, array $headers = array())
	    {
	        $status_code   = $status_code ?: static::$default_status_code;

	        // Set our body and code using our internal methods
	        $this->body($body);
	        $this->code($status_code);

	        $this->headers = new HeaderDataCollection($headers);
	        $this->cookies = new ResponseCookieDataCollection();
	    }

    	/**
	     * Get (or set) the HTTP protocol version
	     *
	     * Simply calling this method without any arguments returns the current protocol version.
	     * Calling with an integer argument, however, attempts to set the protocol version to what
	     * was provided by the argument.
	     *
	     * @param string $protocol_version
	     * @return string|AbstractResponse
	     */
	    public function protocolVersion($protocol_version = null)
	    {
	        if (null !== $protocol_version) {
	            // Require that the response be unlocked before changing it
	            $this->requireUnlocked();

	            $this->protocol_version = (string) $protocol_version;

	            return $this;
	        }

	        return $this->protocol_version;
	    }

	    /**
	     * Get (or set) the response's body content
	     *
	     * Simply calling this method without any arguments returns the current response body.
	     * Calling with an argument, however, sets the response body to what was provided by the argument.
	     *
	     * @param string $body  The body content string
	     * @return string|AbstractResponse
	     */
	    public function body($body = null)
	    {
	        if (null !== $body) {
	            // Require that the response be unlocked before changing it
	            $this->requireUnlocked();

	            $this->body = (string) $body;

	            return $this;
	        }

	        return $this->body;
	    }

	    /**
	     * Returns the status object
	     *
	     * @return \Klein\HttpStatus
	     */
	    public function status()
	    {
	        return $this->status;
	    }

	    /**
	     * Returns the cookies collection
	     *
	     * @return ResponseCookieDataCollection
	     */
	    public function cookies()
	    {
	        return $this->cookies;
	    }

	    /**
	     * Get (or set) the HTTP response code
	     *
	     * Simply calling this method without any arguments returns the current response code.
	     * Calling with an integer argument, however, attempts to set the response code to what
	     * was provided by the argument.
	     *
	     * @param int $code     The HTTP status code to send
	     * @return int|AbstractResponse
	     */
	    public function code($code = null)
	    {
	        if (null !== $code) {
	            // Require that the response be unlocked before changing it
	            $this->requireUnlocked();

	            $this->status = new HttpStatus($code);

	            return $this;
	        }

	        return $this->status->getCode();
	    }

	    /**
	     * Prepend a string to the response's content body
	     *
	     * @param string $content   The string to prepend
	     * @return AbstractResponse
	     */
	    public function prepend($content)
	    {
	        // Require that the response be unlocked before changing it
	        $this->requireUnlocked();

	        $this->body = $content . $this->body;

	        return $this;
	    }

	    /**
	     * Append a string to the response's content body
	     *
	     * @param string $content   The string to append
	     * @return AbstractResponse
	     */
	    public function append($content)
	    {
	        // Require that the response be unlocked before changing it
	        $this->requireUnlocked();

	        $this->body .= $content;

	        return $this;
	    }

	    /**
	     * Check if the response is locked
	     *
	     * @return boolean
	     */
	    public function isLocked()
	    {
	        return $this->locked;
	    }

	    /**
	     * Require that the response is unlocked
	     *
	     * Throws an exception if the response is locked,
	     * preventing any methods from mutating the response
	     * when its locked
	     *
	     * @throws LockedResponseException  If the response is locked
	     * @return AbstractResponse
	     */
	    public function requireUnlocked()
	    {
	        if ($this->isLocked()) {
	            throw new LockedResponseException('Response is locked');
	        }

	        return $this;
	    }

	    /**
	     * Lock the response from further modification
	     *
	     * @return AbstractResponse
	     */
	    public function lock()
	    {
	        $this->locked = true;

	        return $this;
	    }

	    /**
	     * Unlock the response from further modification
	     *
	     * @return AbstractResponse
	     */
	    public function unlock()
	    {
	        $this->locked = false;

	        return $this;
	    }

	    /**
	     * Generates an HTTP compatible status header line string
	     *
	     * Creates the string based off of the response's properties
	     *
	     * @return string
	     */
	    protected function httpStatusLine()
	    {
	        return sprintf('HTTP/%s %s', $this->protocol_version, $this->status);
	    }

	    /**
	     * Send our HTTP headers
	     *
	     * @param boolean $cookies_also Whether or not to also send the cookies after sending the normal headers
	     * @param boolean $override     Whether or not to override the check if headers have already been sent
	     * @return AbstractResponse
	     */
	    public function sendHeaders($cookies_also = true, $override = false)
	    {
	        if (headers_sent() && !$override) {
	            return $this;
	        }

	        // Send our HTTP status line
	        $this->header($this->httpStatusLine());

	        // Iterate through our Headers data collection and send each header
	        foreach ($this->headers as $key => $value) {
	            $this->header($key .': '. $value, false);
	        }

	        if ($cookies_also) {
	            $this->sendCookies($override);
	        }

	        return $this;
	    }

	    /**
	     * Send our HTTP response cookies
	     *
	     * @param boolean $override     Whether or not to override the check if headers have already been sent
	     * @return AbstractResponse
	     */
	    public function sendCookies($override = false)
	    {
	        if (headers_sent() && !$override) {
	            return $this;
	        }

	        // Iterate through our Cookies data collection and set each cookie natively
	        foreach ($this->cookies as $cookie) {
	            // Use the built-in PHP "setcookie" function
	            setcookie(
	                $cookie->getName(),
	                $cookie->getValue(),
	                $cookie->getExpire(),
	                $cookie->getPath(),
	                $cookie->getDomain(),
	                $cookie->getSecure(),
	                $cookie->getHttpOnly()
	            );
	        }

	        return $this;
	    }

	    /**
	     * Send our body's contents
	     *
	     * @return AbstractResponse
	     */
	    public function sendBody()
	    {
	        echo (string) $this->body;

	        return $this;
	    }

	    /**
	     * Send the response and lock it
	     *
	     * @param boolean $override             Whether or not to override the check if the response has already been sent
	     * @throws ResponseAlreadySentException If the response has already been sent
	     * @return AbstractResponse
	     */
	    public function send($override = false)
	    {
	        if ($this->sent && !$override) {
	            throw new ResponseAlreadySentException('Response has already been sent');
	        }

	        // Send our response data
	        $this->sendHeaders();
	        $this->sendBody();

	        // Lock the response from further modification
	        $this->lock();

	        // Mark as sent
	        $this->sent = true;

	        // If there running FPM, tell the process manager to finish the server request/response handling
	        if (function_exists('fastcgi_finish_request')) {
	            fastcgi_finish_request();
	        }

	        return $this;
	    }

	    /**
	     * Check if the response has been sent
	     *
	     * @return boolean
	     */
	    public function isSent()
	    {
	        return $this->sent;
	    }

	    /**
	     * Enable response chunking
	     *
	     * @link https://github.com/chriso/klein.php/wiki/Response-Chunking
	     * @link http://bit.ly/hg3gHb
	     * @return AbstractResponse
	     */
	    public function chunk()
	    {
	        if (false === $this->chunked) {
	            $this->chunked = true;
	            $this->headers('Transfer-encoding', 'chunked');
	            flush();
	        }

	        if (($body_length = strlen($this->body)) > 0) {
	            printf("%x\r\n", $body_length);
	            $this->sendBody();
	            $this->body('');
	            echo "\r\n";
	            flush();
	        }

	        return $this;
	    }

	    /**
	     * Set header
	     *
	     * @param string $name       The name of the HTTP response header
	     * @param mixed $value      The value to set the header with
	     * @return void
	     */
	    public function header($name, $value = null)
	    {
	    	if (!headers_sent()) {
                ($value) ? header("{$name}: {$value}") : header($name);
            }
	    }

	    /**
	     * Returns the headers collection or sets a response header
	     *
	     * @param string $key       The name of the HTTP response header
	     * @param mixed $value      The value to set the header with
	     * @return AbstractResponse or HeaderDataCollection
	     */
	    public function headers($key = null, $value)
	    {
	    	if (!$key) {
	    		return $this->headers;
	    	} else {
	        	$this->headers->set($key, $value);
	        	return $this;
	    	}
	    }

	    /**
	     * Sets a response cookie
	     *
	     * @param string $key           The name of the cookie
	     * @param string $value         The value to set the cookie with
	     * @param int $expiry           The time that the cookie should expire
	     * @param string $path          The path of which to restrict the cookie
	     * @param string $domain        The domain of which to restrict the cookie
	     * @param boolean $secure       Flag of whether the cookie should only be sent over a HTTPS connection
	     * @param boolean $httponly     Flag of whether the cookie should only be accessible over the HTTP protocol
	     * @return AbstractResponse
	     */
	    public function cookie(
	        $key,
	        $value = '',
	        $expiry = null,
	        $path = '/',
	        $domain = null,
	        $secure = false,
	        $httponly = false
	    ) {
	        if (null === $expiry) {
	            $expiry = time() + (3600 * 24 * 30);
	        }

	        $this->cookies->set($key, new ResponseCookie($key, $value, $expiry, $path, $domain, $secure, $httponly));

	        return $this;
	    }

	    /**
	     * Tell the browser not to cache the response
	     *
	     * @return AbstractResponse
	     */
	    public function noCache()
	    {
	        $this->headers('Pragma', 'no-cache');
	        $this->headers('Cache-Control', 'no-store, no-cache');

	        return $this;
	    }

	    /**
	     * Redirects the request to another URL
	     *
	     * @param string $url   The URL to redirect to
	     * @param int $code     The HTTP status code to use for redirection
	     * @return AbstractResponse
	     */
	    public function redirect($url = null, $extern = false, $code = 302)
	    {
	        $this->code($code);

	        if($extern) {
				$this->header('Location', $url);
	        } else {
                if (is_null($url)) {
                    $this->header('Location', APP_BASE_URL);
                } else {
					$this->header('Location', APP_BASE_URL.'/'.trim($url, '/'));
                }
            }

	        $this->lock();

	        return $this;
	    }
    }
?>