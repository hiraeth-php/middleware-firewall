<?php

namespace Hiraeth\Middleware;

use Hiraeth;
use Middlewares\Firewall;

/**
 *
 */
class FirewallDelegate implements Hiraeth\Delegate
{
	/**
	 * Get the class for which the delegate operates.
	 *
	 * @static
	 * @access public
	 * @return string The class for which the delegate operates
	 */
	static public function getClass(): string
	{
		return Firewall::class;
	}


	/**
	 *
	 */
	public function __construct(Hiraeth\Application $app)
	{
		$this->app = $app;
	}


	/**
	 * Get the instance of the class for which the delegate operates.
	 *
	 * @access public
	 * @param Hiraeth\Broker $broker The dependency injector instance
	 * @return object The instance of the class for which the delegate operates
	 */
	public function __invoke(Hiraeth\Broker $broker): object
	{
		$options  = $this->app->getConfig('web', 'middleware.options', []);

		if (isset($options[static::getClass()])) {
			$options = $options[static::getClass()] + $this->getDefaultOptions();
		} else {
			$options = $this->getDefaultOptions();
		}

		$firewall = new Firewall($options['whitelist']);

		if ($options['blacklist']) {
			$firewall->blacklist($options['blacklist']);
		}

		$firewall->ipAttribute('client-ip');
		$firewall->responseFactory($broker->make('Psr\Http\Message\ResponseFactoryInterface'));

		return $firewall;
	}


	/**
	 *
	 */
	public function getDefaultOptions()
	{
		return [
			'whitelist' => [],
			'blacklist' => []
		];
	}
}
