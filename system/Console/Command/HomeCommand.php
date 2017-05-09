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
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;

	class HomeCommand extends Command
	{
	    protected function configure()
	    {
	        $this->setName('home')
	            ->setDescription('The default command');
	    }

	    protected function execute(InputInterface $input, OutputInterface $output)
	    {
	        $output->writeln('<bg=blue>                                          </>');
	        $output->writeln('<bg=blue>   +----------------------------------+   </>');
	        $output->writeln('<bg=blue>   +                                  +   </>');
	        $output->writeln('<bg=blue>   +              BabiPHP             +   </>');
	        $output->writeln('<bg=blue>   +                                  +   </>');
	        $output->writeln('<bg=blue>   +----------------------------------+   </>');
	        $output->writeln('<bg=blue>                                          </>');
	        $output->writeln('');
	        $output->writeln('<fg=yellow>Usage:</>');
	        $output->writeln('    command [options] [arguments]');
	        $output->writeln('');
	        $output->writeln('<fg=yellow>Options:</>');
	        $output->writeln('    <fg=green>--help</>		Display this help message');
	        $output->writeln('    <fg=green>--version</>		Display this application version');
	        $output->writeln('    <fg=green>--verbose</>		Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug');
	        $output->writeln('');
	        $output->writeln('<fg=yellow>Available commands:</>');
	        $output->writeln('    <fg=green>home</>		The default command');
	        $output->writeln('    <fg=green>database-convert</>	Build SQL files');
	        $output->writeln('  <fg=yellow>sql</>');
	        $output->writeln('    <fg=green>sql:build</>		Build SQL files');
	        $output->writeln('    <fg=green>sql:insert</>		Build SQL files');
	    }
	}