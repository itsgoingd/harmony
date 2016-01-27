<?php namespace Harmony\Client\Support\PHP;

class ExceptionHandler
{
	protected static $crashReporter;
	protected static $previousHandler;

	public static function handle($exception)
	{
		self::$crashReporter->report($exception);

		call_user_func_array(self::$previousHandler, [ $exception ]);
	}

	public static function register($apiKey, $server)
	{
		self::$crashReporter = new CrashReporter($apiKey, $server);

		self::$previousHandler = set_exception_handler([ static::class, 'handle' ]);
	}
}
