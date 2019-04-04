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
	 * Get the instance of the class for which the delegate operates.
	 *
	 * @access public
	 * @param Hiraeth\Application $app The application instance for which the delegate operates
	 * @return object The instance of the class for which the delegate operates
	 */
	public function __invoke(Hiraeth\Application $app): object
	{
		$middleware = $app->getConfig('*', 'middleware.class', NULL);
		$collection = array_search(Firewall::class, $middleware);
		$options    = $app->getConfig($collection, 'middleware', [
			'whitelist' => [],
			'blacklist' => []
		]);

		$firewall = new Firewall($options['whitelist']);

		if ($options['blacklist']) {
			$firewall->blacklist($options['blacklist']);
		}

		$firewall->ipAttribute('client-ip');
		$firewall->responseFactory($app->get('Psr\Http\Message\ResponseFactoryInterface'));

		return $firewall;
	}
}
