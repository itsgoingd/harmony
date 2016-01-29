<?php namespace Harmony\Client\DataSources;

use Illuminate\Foundation\Application;

class LaravelDataSource implements DataSourceInterface
{
	protected $app;

	protected $queries = [];

	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	public function getRequestData()
	{
		return $this->app['request']->all();
	}

	public function getRequestHeaders()
	{
		return $this->app['request']->headers->all();
	}

	public function getQueryLog()
	{
		return $this->queries;
	}

	/**
	 * Start listening to eloquent queries
	 */
	public function collectQueries()
	{
		if (class_exists('Illuminate\Database\Events\QueryExecuted')) {
			// Laravel 5.2
			$this->app['events']->listen('Illuminate\Database\Events\QueryExecuted', [ $this, 'registerQuery' ]);
		} else {
			// Laravel 4.0 to 5.1
			$this->app['events']->listen('illuminate.query', [ $this, 'registerLegacyQuery' ]);
		}
	}

	/**
	 * Log the query into the internal store
	 */
	public function registerQuery($event)
	{
		$this->queries[] = array(
			'query'      => $event->sql,
			'bindings'   => $event->bindings,
			'time'       => $event->time,
			'connection' => $event->connectionName
		);
	}

	/**
	 * Log a legacy (pre Laravel 5.2) query into the internal store
	 */
	public function registerLegacyQuery($sql, $bindings, $time, $connection)
	{
		return $this->registerQuery((object) array(
			'sql'            => $sql,
			'bindings'       => $bindings,
			'time'           => $time,
			'connectionName' => $connection
		));
	}
}
