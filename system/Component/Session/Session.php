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
 * @author 		  Lambirou <lambirou225@gmail.com>
 * @link          http://babiphp.org BabiPHP Project
 * @since         BabiPHP v 0.3
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * 
 * Not edit this file
 *
 */

namespace BabiPHP\Component\Session;

use \BabiPHP\Component\Config\Config;

class Session implements SessionInterface, \ArrayAccess
{
	private $started = false;

	private $null = null;

	protected $flash_name = 'default';

	protected $flash_template = array();

	protected $current_flash_template;

	protected $flash_slug = array('type', 'icon', 'message');

	private static $_instance;

	/**
		* Constructor
		*/
	public function __construct()
	{
		$this->ensureStarted();
		$this->setFlashName(Config::Get('name'));
		$this->flash_template = Config::Get('flash_template');
		$this->current_flash_template = $this->flash_template['default'];
	}

    /**
     * Permet de s'assurer que la session est démarrée.
     */
    private function ensureStarted()
    {
        if ($this->started === false && session_status() === PHP_SESSION_NONE) {
            session_start();
            $this->started = true;
        }
    }

	/**
	* GetInstance
	*/
	public static function getInstance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new Session();
		}

		return self::$_instance;
	}

	/**
	* {@inheritdoc}
	*/
	public function set(string $key, $value)
	{
		$this->ensureStarted();
        $_SESSION[$key] = $value;
	}

	/**
	* {@inheritdoc}
	*/
	public function get(string $key = null)
	{
		$this->ensureStarted();

		if($key) {
			$result = (array_key_exists($key, $_SESSION)) ? $_SESSION[$key] : $this->null;
		} else {
			$result = $_SESSION;
		}

        return $result;
	}

	/**
	* {@inheritdoc}
	*/
	public function check(string $key, $_child = null)
	{
		if($_child) {
			return isset($_SESSION[$key][$_child]);
		}

		return isset($_SESSION[$key]);
	}

	/**
	* {@inheritdoc}
	*/
	public function delete(string $key)
	{
		unset($_SESSION[$key]);
	}

	/**
	* {@inheritdoc}
	*/
	public function destroy()
	{
		if ($this->started === true) {
            session_destroy();
            $this->started = false;
        }
	}

	/**
	* setFlash
	* @param array $flash
	*/
	public function flash($flash)
	{
		$this->flash_slug = array();
		
		foreach ($flash as $key => $value)
		{
			$this->flash_slug[] = $key;
		}
		
		$this->set($this->flash_name, $flash);
	}

	/**
	* setFlash
	* @param $message, $type
	*/
	public function setSimpleFlash($icon, $message, $type = 'info')
	{
		$data = array(
			'message' => $message,
			'type' => $type,
			'icon' => $icon
		);

		$this->set($this->flash_name, $data);
	}

	/**
	* checkFlash
	*/
	public function checkFlash()
	{
		return $this->check($this->flash_name);
	}

	/**
	* Flash
	* @return flash message
	*/
	public function getFlash()
	{
		$flash = $this->get($this->flash_name);
		$template = $this->current_flash_template;
		$slugs = $this->flash_slug;

		if(!empty($flash))
		{
			foreach ($slugs as $key => $slug)
			{
				$template = str_replace('{{'.$slug.'}}', $flash[$slug], $template);
			}

			$this->delete($this->flash_name);

			return $template;
		}
	}

	/**
		* addFlashTemplate
		* @param string $tpl
		*/
	public function addFlashTemplate($name, $tpl)
	{
		$this->flash_template[$name] = $tpl;
	}

	/**
		* setFlashTemplate
		* @param string $tpl
		*/
	public function setFlashTemplate($name)
	{
		$this->current_flash_template = $this->flash_template[$name];
	}

	/**
		* setFlashName
		* @param string $app_name
		*/
	private function setFlashName($app_name)
	{
		$app_name = strtolower($app_name);
		$app_name = str_replace(' ', '_', $app_name);

		$this->flash_name = $app_name.'_flash';
	}

    public function offsetExists($offset)
    {
        return $this->get($offset);
    }

    public function &offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }
}