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
* @since         BabiPHP v 0.8.2
* @license       http://www.gnu.org/licenses/ GNU License
*
* 
* Not edit this file
*
*/

namespace BabiPHP\Component\Translation;

use BabiPHP\Component\Config\Config;
use BabiPHP\Component\Translation\Gettext\Translator;
use BabiPHP\Component\Translation\Gettext\Translations;

class Localization
{
	protected $locale;
	protected $domain;
	protected $encoding;

	// Instance of this class
	private static $_instance;

	public function __construct()
	{
		$this->locale = Config::get('locale_default');
		$this->supported_locales = Config::get('locale_supported');
		$this->encoding = Config::get('locale_encoding');
	}

	public function Setup()
	{
		$translate_file = APPPATH.'locales'.DS.$this->locale.DS.'LC_MESSAGES'.DS.$this->domain.'.mo';

		if(file_exists($translate_file) && in_array($this->locale, $this->supported_locales))
		{
			$translations = Translations::fromMoFile($translate_file);
			$t = new Translator();
			$t->loadTranslations($translations);
			$t->register();
		} else {
			require_once 'translate.inc';
		}
	}

	/**
	* GetInstance
	*/
	public static function getInstance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new Localization();
		}

		return self::$_instance;
	}

	public function Domain($domain)
	{
		$this->domain = $domain;
	}

	public function Encoding($encoding)
	{
		$this->encoding = $encoding;
	}

	public function Locale($locale)
	{
		$this->locale = $locale;
	}
}