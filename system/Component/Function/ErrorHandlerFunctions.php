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
 * @package       system.component.function
 * @since         BabiPHP v 0.8.8
 * @license       http://www.gnu.org/licenses/ GNU License
 */

	use BabiPHP\Component\Handlers\Error as HandlerError;
    use BabiPHP\Component\Http\Request;
    use BabiPHP\Component\Utility\Debugbar;

	/**
	* Checks for a fatal error, work around for set_error_handler not working on fatal errors.
	*/
	function check_for_fatal()
	{
	    $error = error_get_last();
	    
	    if ( $error["type"] == E_ERROR ) {
	    	$error["type"] = 1024;
	        log_error( $error["type"], $error["message"], $error["file"], $error["line"] );
	    }
	}

	/**
	* Error handler, passes flow over the exception logger with new ErrorException.
	*/
	function log_error( $num, $str, $file, $line, $context = null )
	{
		log_exception( new ErrorException($str, 500, $num, $file, $line) );
	}

	/**
	* Uncaught exception handler.
	*/
	function log_exception( Exception $e )
	{
		$error_handler = new HandlerError();
		$request = Request::getInstance();

	    echo $error_handler->write($request, $e);

	    if(Debugbar::$activate) {
			echo Debugbar::Render($request);
		}

	    exit();
	}

?>