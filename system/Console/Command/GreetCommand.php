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

	namespace BabiPHP\Console\Command;

	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Formatter\OutputFormatterStyle;

	class GreetCommand extends Command
	{
	    protected function configure()
	    {
	        $this
	            ->setName('demo:greet')
	            ->setDescription('Greet someone')
	            ->addArgument(
	                'name',
	                InputArgument::OPTIONAL,
	                'Who do you want to greet?'
	            )
	            ->addArgument(
			        'last_name',
			        InputArgument::OPTIONAL,
			        'Your last name?'
			    )
	            ->addOption(
	               'yell',
	               null,
	               InputOption::VALUE_NONE,
	               'If set, the task will yell in uppercase letters'
	            )
	        ;
	    }

	    protected function execute(InputInterface $input, OutputInterface $output)
	    {
	        $name = $input->getArgument('name');
	        if ($name) {
	            $text = 'Hello '.$name;
	        } else {
	            $text = 'Hello';
	        }

	        if ($input->getOption('yell')) {
	            $text = strtoupper($text);
	        }

	        if ($lastName = $input->getArgument('last_name')) {
			    $text .= ' '.$lastName;
			}

	        $style = new OutputFormatterStyle('red', 'yellow', array('bold', 'blink'));
			$output->getFormatter()->setStyle('fire', $style);
			$output->writeln('<fire>'.$text.'</fire>');
	    }
	}

?>