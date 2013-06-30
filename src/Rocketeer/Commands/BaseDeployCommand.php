<?php
namespace Rocketeer\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * A basic deploy command with helpers
 */
abstract class BaseDeployCommand extends Command
{

	/**
	 * Run the tasks
	 *
	 * @return void
	 */
	abstract public function fire();

	/**
	 * Fire a Tasks Queue
	 *
	 * @param  string|array $tasks
	 *
	 * @return mixed
	 */
	protected function fireTasksQueue($tasks)
	{
		// Start timer
		$timerStart = microtime(true);

		// Convert tasks to array if necessary
		if (!is_array($tasks)) {
			$tasks = array($tasks);
		}

		// Run tasks and display timer
		$output = $this->laravel['rocketeer.tasks']->run($tasks, $this);
		$this->line('Execution time: <comment>'.round(microtime(true) - $timerStart, 4). 's</comment>');
	}

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    return array(
      array('pretend', 'p', InputOption::VALUE_NONE, 'Returns an array of commands to be executed instead of actually executing them')
    );
  }

}
