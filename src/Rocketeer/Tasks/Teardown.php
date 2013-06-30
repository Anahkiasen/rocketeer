<?php
namespace Rocketeer\Tasks;

use Rocketeer\Tasks\Abstracts\Task;

/**
 * Remove the remote applications and existing caches
 */
class Teardown extends Task
{

	 /**
	 * A description of what the Task does
	 *
	 * @var string
	 */
	protected $description = 'Remove the remote applications and existing caches';

	/**
	 * Run the Task
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Remove remote folders
		$this->removeFolder();

		// Remove deployments file
		$this->deploymentsManager->deleteDeploymentsFile();

		$this->command->info('The application was successfully removed from the remote servers');
	}

}
