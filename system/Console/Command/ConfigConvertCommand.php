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
 * @package       system.console.command
 * @since         BabiPHP v 0.8.4
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * BabiPHP Console SqlBuildCommand.
 * 
 * Not edit this file
 *
 */

	namespace BabiPHP\Console\Command;

	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Formatter\OutputFormatterStyle;

	class ConfigConvertCommand extends Command
	{
	    protected function configure()
	    {
	        $this
	            ->setName('config:convert')
	            ->setAliases(array('config-convert'))
	            ->setDescription('Build SQL files')
	            ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
	        ;
	    }

	    protected function execute(InputInterface $input, OutputInterface $output)
	    {
	        $text = 'Hello';

	        $style = new OutputFormatterStyle('red', 'yellow', array('bold', 'blink'));
			$output->getFormatter()->setStyle('fire', $style);
			$output->writeln('<fire>'.$text.'</fire>');
	    }
	}

?>