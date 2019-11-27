<?php

namespace Hiraeth\Middleware;

use Hiraeth;
use Middlewares\Firewall;

/**
 * {@inheritDoc}
 */
class FirewallDelegate implements Hiraeth\Delegate
{
	/**
	 * {@inheritDoc}
	 */
	static public function getClass(): string
	{
		return Firewall::class;
	}


	/**
	 * {@inheritDoc}
	 */
	public function __invoke(Hiraeth\Application $app): object
	{
		$middleware = $app->getConfig('*', 'middleware.class', NULL);
		$collection = array_search(Firewall::class, $middleware);
		$options    = $app->getConfig($collection, 'middleware', [
			'attribute' => '_client-ip',
			'whitelist' => [],
			'blacklist' => []
		]);

		$instance = new Firewall($options['whitelist']);

		if ($options['blacklist']) {
			$instance->blacklist($options['blacklist']);
		}

		$instance->ipAttribute($options['attribute']);
		$instance->responseFactory($app->get('Psr\Http\Message\ResponseFactoryInterface'));

		return $instance;
	}
}
