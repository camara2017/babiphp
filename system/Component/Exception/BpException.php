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
 * @since         BabiPHP v 0.7
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * BabiPHP BpException Class.
 * 
 * Not edit this file
 *
 */
	
	namespace BabiPHP\Component\Exception;

	class BpException extends \Exception
	{
		function __construct($message='', $code=0, Exception $previous=null)
		{
			$message = (empty($message)) ? 'A website error has occurred.' : $message;

			parent::__construct($message, $code, $previous);
		}

		/**
		 * Var_dum your var Pretty
		 * @param  mixed 	$vars What you want to debug
		 * @return mixed
		 */
		public function debug($vars)
		{
			debug($vars);
		}
	}

?>