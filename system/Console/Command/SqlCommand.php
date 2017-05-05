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
 *
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
	use Symfony\Component\Finder\Finder;

	class SqlCommand extends Command
	{
	    protected function configure()
	    {
	        $this
	            ->setName('sql:build')
	            ->setAliases(array('sql-build'))
	            ->setDescription('Build SQL files')
	            ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
	        ;
	    }

	    protected function execute(InputInterface $input, OutputInterface $output)
	    {
	        $finder = new Finder();
			$finder->files()->name('schema.json')->in(APPPATH.'config');
			$data = null;
			$text = 'Database compile successful !';

			foreach ($finder as $file) {
				$data = json_decode($file->getContents());
			}

			var_dump($data);

	        $style = new OutputFormatterStyle('white', 'green');
			$output->getFormatter()->setStyle('fire', $style);
			$output->writeln('');
			$output->writeln('<fire>                                 ');
			$output->writeln('  '.$text.'  ');
			$output->writeln('                                 </fire>');
			$output->writeln('');
	    }
	}

?>