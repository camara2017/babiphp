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
 * @since         BabiPHP v 0.7.9
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * BabiPHP Config Security Class.
 * 
 * Not edit this file
 *
 */

	namespace BabiPHP\Component\Config;

	class Security extends AbstractConfig
	{
		protected static $configs = array(
            'Password_encoder' => 'sha512',
            'Security_salt' => 'DYsfqqghshh5+45sh4h=4h6hykyk--466FFqD8çCOy9Uuçgggf675FHFHxfs2+=ni0FgaC9mi',
            'Security_cipherSeed' => '7663464293096559434827935824967473456727',
            'Random_string' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        );
	}

?>