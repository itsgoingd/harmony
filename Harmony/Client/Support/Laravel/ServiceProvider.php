<?php namespace Harmony\Client\Support\Laravel;

use Harmony\Client\CrashReporter;
use Harmony\Client\DataSources\LaravelDataSource;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
	public function register()
	{
		$this->publishes([ __DIR__ . '/config/harmony.php' => config_path('harmony.php') ]);

		$this->app->singleton('harmony.laravel', function($app)
		{
			return new LaravelDataSource($app);
		});

		$this->app->singleton('harmony', function($app)
		{
			$crashReporter = new CrashReporter(
				$app['config']->get('harmony.apiKey'),
				$app['config']->get('harmony.server')
			);

			$crashReporter->setDataSource($app['harmony.laravel']);

			return $crashReporter;
		});

		$this->app->alias('harmony.laravel', 'Harmony\Client\DataSources\LaravelDataSource');
		$this->app->alias('harmony', 'Harmony\Client\CrashReporter');
	}

	public function provides()
	{
		return [ 'harmony.laravel', 'harmony' ];
	}
}
