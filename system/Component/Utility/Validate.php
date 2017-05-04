<?php
/**
 * BabiPHP : The Simple and Fast Development Framework (http://babiphp.org)
 * Copyright (c) BabiPHP. (http://babiphp.org)
 *
 * Licensed under The GNU General Public License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author        Lambirou (http://www.facebook.com/lambirou)
 * @copyright     Copyright (c) BabiPHP. (http://babiphp.org)
 * @link          http://babiphp.org BabiPHP Project
 * @package       system.core
 * @since         BabiPHP v 0.7.5
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * BabiPHP Validate Form Class.
 * 
 * Not edit this file
 *
 */

	namespace BabiPHP\Component\Utility;

    use BabiPHP\Component\Utility\Gump;

	class Validate extends Gump
	{
		/**
		* @param $data
		* @return array
		*/
		public static function GetData($_d = null)
		{
			$data = null;

			if(is_null($_d))
			{
				$data = $_POST;
			}
			elseif(is_array($_d))
			{
				foreach ($_d as $k => $v)
				{
					$data[$k] = $v;
				}
			}
			elseif(is_object($_d))
			{
				$data = get_object_vars($_d);
			}

			return $data;
		}

		/**
		 * IsValid
		 * @param $value            [multiple type]
		 * @param $validation_rules [validation rules]
		 * @return boolean 'true' if is valid or array Errors messages
		 */
		public static function IsValid($value, $validation_rules)
		{
			$value = self::GetData($value);
			return self::is_valid($value, $validation_rules);
		}
	}

?>