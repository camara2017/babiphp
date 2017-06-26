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
 * Emitter
 *
 * @package BabiPHP\Component\Event
 */
class Emitter
{

	/**
	 * Enregistre l'instance de l'émetteur (singleton)
	 *
	 * @var [type]
	 */
	private static $_instance;


	/**
	 * Enregistre la liste des écouteurs
	 *
	 * @var Listener[][]
	 */
	private $listeners = [];


	/**
	 * Permet de récupérer l'instance de l'émetteur (singleton)
	 *
	 * @return Emitter
	 */
	public static function getInstance()
	{
		if (!self::$_instance) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}


	/**
	 * Envoie un évènement
	 *
	 * @param string $event Nom de l'évènement
	 * @param array ...$args
	 */
	public function emit(string $event, ...$args)
	{
		if ($this->hasListener($event)) {
			foreach ($this->listeners[$event] as $listener) {
				$listener->handle($args);

				if ($listener->stopPropagation) {
					break;
				}
			}
		}
	}


	/**
     * Permet d'ecouter un évènement
     *
     * @param string $event
     * @param callable $callable
     * @param int $priority
     * @return Listener
     */
	public function on(string $event, callable $callable, int $priority = 0)
	{
		if (!$this->hasListener($event)) {
			$this->listeners[$event] = [];
		}
		
		$this->checkDoubleCallableForEvent($event, $callable);
		$listener = new Listener($callable, $priority);
		$this->listeners[$event][] = $listener;
		$this->sortListeners($event);
		return $listener;
	}
	

	/**
     * Permet d'ecouter un évènement et de lancer le listener une seule fois
     *
     * @param string $event
     * @param callable $callable
     * @param int $priority
     * @return Listener
     */
	public function once(string $event, callable $callback, int $priority = 0)
	{
		return $this->on($event, $callback, $priority)->once();
	}


	/**
	 * Permet d'ajouter un subscriber qui va écouter plusieurs évènements
	 *
	 * @param SubscriberInterface $subscriber
	 */
	public function addSubscriber(SubscriberInterface $subscriber)
	{
		$events = $subscriber->getEvents();

		foreach ($events as $event => $method) {
			$this->on($event, [$subscriber, $method]);
		}
	}
	
	
	/**
	 * Permet de vérifier si un évènement existe déjà
	 *
	 * @param string $event
	 * @return boolean
	 */
	private function hasListener(string $event)
	{
		return array_key_exists($event, $this->listeners);
	}
	

	/**
	 * Permet de trier les évènements par priorité
	 *
	 * @param string $event
	 * @return void
	 */
	private function sortListeners(string $event)
	{
		uasort($this->listeners[$event], function ($a, $b) {
			return $a->priority < $b->priority;
		});
	}


	/**
	 * Permet de vérifier un double appel pour un évènement
	 *
	 * @param string $event
	 * @param callable $callback
	 * @return boolean
	 */
	private function checkDoubleCallableForEvent(string $event, callable $callback)
	{
		foreach ($this->listeners[$event] as $listener) {
			if ($listener->callback === $callback) {
				throw new DoubleEventException();
			}
		}
		
		return false;
	}
	
}


