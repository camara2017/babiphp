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
 * @package       system.console
 * @since         BabiPHP v 0.8.4
 * @license       http://www.gnu.org/licenses/ GNU License
 *
 *
 * Not edit this file
 * 
 */

	use BabiPHP\Core\Application as CoreApplication;
	use BabiPHP\Console\Command\HomeCommand;
	use Symfony\Component\Console\Application;
	use Symfony\Component\Finder\Finder;

	// BabiPHP application bootstrap
    require_once __DIR__.'/../../system/Bootstrap.php';

	CoreApplication::registerAutoloader();

	$app = new Application('BabiPHP', CoreApplication::VERSION);
	$home = new HomeCommand();
	$finder = new Finder();
    $finder->files()->name('*.php')->in(BASEPATH.'Console/Command')->depth(0);

    foreach ($finder as $file)
    {
    	$r = new \ReflectionClass('\\BabiPHP\\Console\\Command'.'\\'.$file->getBasename('.php'));

    	if ($r->isSubclassOf('Symfony\\Component\\Console\\Command\\Command') && !$r->isAbstract()) {
            $app->add($r->newInstance());
		}
    }

	$app->setDefaultCommand($home->getName());
	$app->run();