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
* @since         BabiPHP v 0.8.9
* @license       http://www.gnu.org/licenses/ GNU License
*
* 
* Not edit this file
*
*/

namespace BabiPHP\Component\Event;

/**
 * Listener
 *
 * @package BabiPHP\Component\Event
 */
class Listener
{

	/**
	 * @var callable
	 */
	public $callback;


	/**
	 * @var int
	 */
	public $priority;


	/**
	 * Définie si le listener peut être appellé plusieurs fois
	 *
	 * @var bool
	 */
	private $once = false;


	/**
	 * Permet de stopper les évènements parents
	 *
	 * @var bool
	 */
	public $stopPropagation = false;


	/**
	 * Permet de savoir combien de fois le listener a été appellé
	 *
	 * @var bool
	 */
	private $calls = 0;


	public function __construct(callable $callback, int $priority)
	{
		$this->callback = $callback;
		$this->priority = $priority;
	}


	/**
	 * Permet de lancer le traitement d'évènement
	 *
	 * @param array $args
	 * @return void
	 */
	public function handle(array $args)
	{
		if ($this->once && $this->calls > 0) {
			return null;
		}

		$this->calls++;
		return call_user_func_array($this->callback, $args);
	}


	/**
	 * Permet d'indiquer que le listener ne peut être appellé qu'une fois
	 *
	 * @return Listener
	 */
	public function once()
	{
		$this->once = true;
		return $this;
	}
	

	/**
	 * Permet de stopper l'exécution des évènements suivants
	 *
	 * @return Listener
	 */
	public function stopPropagation()
	{
		$this->stopPropagation = true;
		return $this;
	}
	
}

