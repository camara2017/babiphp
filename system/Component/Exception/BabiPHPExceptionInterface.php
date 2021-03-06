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
 * @package       system.component.exception
 * @since         BabiPHP v 0.8.5
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * Not edit this file
 */

    namespace BabiPHP\Component\Exception;

	/**
	 * BabiPHPExceptionInterface
	 *
	 * Exception interface that Klein's exceptions should implement
	 *
	 * This is mostly for having a simple, common Interface class/namespace
	 * that can be type-hinted/instance-checked against, therefore making it
	 * easier to handle Klein exceptions while still allowing the different
	 * exception classes to properly extend the corresponding SPL Exception type
	 */
	interface BabiPHPExceptionInterface
	{
	}
