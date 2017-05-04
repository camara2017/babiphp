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
 * @package       system.component.utility
 * @since         BabiPHP v 0.8.3
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * BabiPHP Image Class.
 * 
 * Not edit this file
 *
 */

	namespace BabiPHP\Component\Utility;

	use BabiPHP\Component\Utility\Image\SimpleImage;

	class Image extends SimpleImage
	{
		function __construct($filename = null, $width = null, $height = null, $color = null)
		{
            parent::__construct($filename, $width, $height, $color);
            return $this;
        }
	}

?>