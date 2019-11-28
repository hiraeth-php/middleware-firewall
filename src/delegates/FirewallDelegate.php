<?php

namespace Hiraeth\Middleware;

use Hiraeth;
use Middlewares\Firewall;

/**
 * {@inheritDoc}
 */
class FirewallDelegate extends AbstractDelegate
{
	/**
	 * {@inheritDoc}
	 */
	static protected $defaultOptions = [
		'attribute' => '_client-ip',
		'whitelist' => [],
		'blacklist' => []
	];


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
		$options  = $this->getOptions();
		$instance = new Firewall($options['whitelist']);

		if ($options['blacklist']) {
			$instance->blacklist($options['blacklist']);
		}

		$instance->ipAttribute($options['attribute']);
		$instance->responseFactory($app->get('Psr\Http\Message\ResponseFactoryInterface'));

		return $instance;
	}
}
