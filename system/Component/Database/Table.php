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

class Table implements Tableinterface
{

	/**
     * @var Database
     */
	private $database = null;

	/**
	 * Constructor
	 *
	 * @param string $table
	 * @param array $config Configuration pour la connexion à la base de donnée
	 */
	public function __construct(string $table, array $config = [])
	{
		if ($config) {
			$manager = ConnectionManager::getInstance();

			if (isset($config['data'])) {
				$manager->addConfiguration($config['name'], $config['data']);
			}

			$manager->setCurrentConfigName($config['name']);
		}

		$this->database = new Database($table);
	}

	/**
	 * Permet d'exécuter une requête
	 *
	 * @param string $sql
	 * @param array $bind
	 * @return mixed
	 */
	public function query(string $sql, array $bind)
	{
		return $this->database->query($sql, $bind);
	}

	/**
	 * Compte le nombre d'enregistrement.
	 *
	 * @param string $param
	 * @return Database
	 */
	public function count($param = 'count(*)')
	{
		$this->database->countQuery($param);
		return $this->database;
	}

	/**
	 * Récupère des enregistrements
	 *
	 * @param string $param
	 * @return Database
	 */
	public function select($param = '*')
	{
		$this->database->selectQuery($param);
		return $this->database;
	}

	/**
	 * Crée un nouvel enregistrement.
	 *
	 * @param array $param
	 * @return Database
	 */
	public function insert(array $param)
	{
		$this->database->insertQuery($param);
		return $this->database;
	}

	/**
	 * Met à jour un enregistrement
	 *
	 * @param array $param
	 * @return Database
	 */
	public function update(array $param)
	{
		$this->database->updateQuery($param);
		return $this->database;
	}

	/**
	 * Supprime un enregistrement.
	 *
	 * @return Database
	 */
	public function delete()
	{
		$this->database->deleteQuery();
		return $this->database;
	}
	
}