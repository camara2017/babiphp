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
 * @package       system.component.database
 * @since         BabiPHP v 0.8.10
 * @license       http://www.gnu.org/licenses/ GNU License
 *
 * 
 * Not edit this file
 *
 */

namespace BabiPHP\Component\Database;

interface Tableinterface {

    /**
	 * Permet d'exécuter une requête
	 *
	 * @param string $sql
	 * @param array $bind
	 * @return mixed
	 */
	public function query(string $sql, array $bind);

	/**
	 * Compte le nombre d'enregistrement.
	 *
	 * @param string $param
	 * @return Database
	 */
	public function count($param = 'count(*)');

	/**
	 * Récupère des enregistrements
	 *
	 * @param string $param
	 * @return Database
	 */
	public function select($param = '*');

	/**
	 * Crée un nouvel enregistrement.
	 *
	 * @param array $param
	 * @return Database
	 */
	public function insert(array $param);

	/**
	 * Met à jour un enregistrement
	 *
	 * @param array $param
	 * @return Database
	 */
	public function update(array $param);

	/**
	 * Supprime un enregistrement.
	 *
	 * @return Database
	 */
	public function delete();

}