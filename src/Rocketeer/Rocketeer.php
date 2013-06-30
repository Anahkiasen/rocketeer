<?php
namespace Rocketeer;

use Illuminate\Container\Container;
use Illuminate\Support\Str;

/**
 * Handles interaction between the User provided informations
 * and the various Rocketeer components
 */
class Rocketeer
{

	/**
	 * The IoC Container
	 *
	 * @var Container
	 */
	protected $app;

	/**
	 * The Rocketeer version
	 *
	 * @var string
	 */
	const VERSION = '0.5.0';

	/**
	 * Build a new ReleasesManager
	 *
	 * @param Container $app
	 */
	public function __construct(Container $app)
	{
		$this->app = $app;
	}

	/**
	 * Get an option from the config file
	 *
	 * @param  string $option
	 *
	 * @return mixed
	 */
	public function getOption($option)
	{
		return $this->app['config']->get('rocketeer::'.$option);
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// APPLICATION //////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the name of the application to deploy
	 *
	 * @return string
	 */
	public function getApplicationName()
	{
		return $this->getOption('remote.application_name');
	}

	/**
	 * Whether the repository used is using SSH or HTTPS
	 *
	 * @return boolean
	 */
	public function usesSsh()
	{
		return str_contains($this->getOption('git.repository'), 'git@');
	}

	/**
	 * Whether credentials were provided by the User or if we
	 * need to prompt for them
	 *
	 * @return boolean
	 */
	public function hasCredentials()
	{
		$credentials = $this->getOption('git');

		return $credentials['username'] or $credentials['password'];
	}

	/**
	 * Get the Git repository
	 *
	 * @param  string $username
	 * @param  string $password
	 *
	 * @return string
	 */
	public function getGitRepository($username = null, $password = null)
	{
		// Get credentials
		$repository = $this->getOption('git');
		$username   = $username ?: $repository['username'];
		$password   = $password ?: $repository['password'];
		$repository = $repository['repository'];

		// Add credentials if possible
		if ($username or $password) {

			// Build credentials chain
			$credentials = $username;
			if ($password) $credentials .= ':'.$password;
			$credentials .= '@';

			// Add them in chain
			if (Str::contains($repository, 'https://'.$username)) {
				$repository = str_replace($username.'@', $credentials, $repository);
			} else {
				$repository = str_replace('https://', 'https://'.$credentials, $repository);
			}

		}

		return $repository;
	}

	/**
	 * Get the Git branch
	 *
	 * @return string
	 */
	public function getGitBranch()
	{
		return $this->getOption('git.branch');
	}

	/**
	 * Get an array of folders and files to share between releases
	 *
	 * @return array
	 */
	public function getShared()
	{
		// Get path to logs
		$base = $this->app['path.base'];
		$logs = $this->app['path.storage'].'/logs';
		$logs = str_replace($base, null, $logs);

		// Add logs to shared folders
		$shared = (array) $this->getOption('remote.shared');
		$shared[] = trim($logs, '/');

		return $shared;
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// PATHS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the path to a folder
	 *
	 * @param  string $folder
	 *
	 * @return string
	 */
	public function getFolder($folder = null)
	{
		$base   = $this->getHomeFolder().'/';
		$folder = str_replace($base, null, $folder);

		return $base.$folder;
	}

	/**
	 * Get the path to the remote folder
	 *
	 * @return string
	 */
	public function getHomeFolder()
	{
		$rootDirectory = $this->getOption('remote.root_directory');
		$rootDirectory = Str::finish($rootDirectory, '/');

		return $rootDirectory.$this->getApplicationName();
	}

}
