<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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
 * @package       app.config
 * @since         BabiPHP v 0.7.9
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * BabiPHP Application Security Configuration.
 * 
 * Not edit this file
 *
 */

	use BabiPHP\Component\Config\Security;

/*
|--------------------------------------------------------------------------
| The password encoder Algorythm "default = sha512"
|--------------------------------------------------------------------------
*/
	Security::set('Password_encoder', 'sha512');

/*
|--------------------------------------------------------------------------
| A random string used in security hashing methods
|--------------------------------------------------------------------------
*/
	Security::Set('Security_salt', 'DYsfqqghshh5+45sh4h=4h6hykyk--466FFqD8çCOy9Uuçgggf675FHFHxfs2+=ni0FgaC9mi');

/*
|--------------------------------------------------------------------------
| A random numeric string (digits only) used to encrypt/decrypt strings.
|--------------------------------------------------------------------------
*/
	Security::Set('Security_cipherSeed', '7663464293096559434827935824967473456727');

/*
|---------------------------------------------------------------------------------
| A random string used to create random password with 8 alphanumerical characters.
|---------------------------------------------------------------------------------
*/
	Security::Set('Random_string', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
	
//-------------------------------------------------------------------------


?>