<?php
namespace Rocketeer\Tasks;

use Rocketeer\Traits\Task;

/**
 * Deploy the website
 */
class Deploy extends Task
{

	/**
	 * Run the Task
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Setup if necessary
		if (!$this->isSetup()) {
			$this->command->error('Server is not ready, running Setup task');
			$this->executeTask('Setup');
		}

		// Update current release
		$release = date('YmdHis');
		$this->releasesManager->updateCurrentRelease($release);

		// Clone Git repository
		if (!$this->cloneScmRepository()) {
			return $this->cancel();
		}

		// Run Composer
		if (!$this->runComposer()) {
			return $this->cancel();
		}

		// Run tests
		if ($this->command->option('tests')) {
			if (!$this->runTests()) {
				$this->command->error('Tests failed');
				return $this->cancel();
			}
		}

		// Set permissions
		$this->setApplicationPermissions();

		// Run migrations
		if ($this->command->option('migrate')) {
			$this->runMigrations($this->command->option('seed'));
		}

		// Synchronize shared folders and files
		$this->syncSharedFolders();

		// Update symlink
		$this->updateSymlink();

		return $this->command->info('Successfully deployed release '.$release);
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////////// HELPERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Cancel deploy
	 *
	 * @return false
	 */
	protected function cancel()
	{
		$this->executeTask('Rollback');

		return false;
	}

	/**
	 * Sync the requested folders and files
	 *
	 * @return void
	 */
	protected function syncSharedFolders()
	{
		$currentRelease = $this->releasesManager->getCurrentReleasePath();
		foreach ($this->rocketeer->getShared() as $file) {
			$this->share($currentRelease.'/'.$file);
		}
	}

	/**
	 * Clone the repository in a new release folder
	 *
	 * @return void
	 */
	protected function cloneScmRepository()
	{
		// Get SCM credentials
		$repository = $this->rocketeer->getRepository();
		$branch     = $this->rocketeer->getRepositoryBranch();

		// Clone release
		return $this->cloneRepository($repository, $branch);
	}

	/**
	 * Set permissions for the folders used by the application
	 *
	 * @return  void
	 */
	protected function setApplicationPermissions()
	{
		$base    = $this->app['path.base'].DIRECTORY_SEPARATOR ;
		$app     = str_replace($base, null, $this->app['path']);
		$storage = str_replace($base, null, $this->app['path.storage']);
		$public  = str_replace($base, null, $this->app['path.public']);

		$this->setPermissions($app.'/database/production.sqlite');
		$this->setPermissions($storage);
		$this->setPermissions($public);
	}
}
