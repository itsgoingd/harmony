<?php namespace Harmony\Client\Support\Laravel;

use Harmony\Client\CrashReporter;
use Harmony\Client\ExceptionRenderer;
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

		$this->app->singleton('harmony.renderer', function($app)
		{
			$config = $app['config'];

			return new ExceptionRenderer(
				$config->get('harmony.debug', $config->get('app.debug')),
				$config->get('harmony.crashTitle'),
				$config->get('harmony.crashMessage')
			);
		});

		$this->app->singleton('harmony', function($app)
		{
			$config = $app['config'];

			$crashReporter = new CrashReporter(
				$config->get('services.harmony.apiKey', $app['config']->get('harmony.apiKey')),
				$config->get('services.harmony.server', $app['config']->get('harmony.server')),
				$config->get('harmony.previewLines')
			);

			$crashReporter->setDataSource($app['harmony.laravel']);

			$crashReporter->setErrorCallback(function($e) use($app)
			{
				$app['log']->error('Harmony - failed to report crash (' . $e->getMessage() . ')');
			});

			$crashReporter->setRenderer($app['harmony.renderer']);

			return $crashReporter;
		});

		$this->app['harmony.laravel']->collectQueries();

		$this->app->alias('harmony.laravel', 'Harmony\Client\DataSources\LaravelDataSource');
		$this->app->alias('harmony', 'Harmony\Client\CrashReporter');
	}

	public function provides()
	{
		return [ 'harmony.laravel', 'harmony' ];
	}
}
