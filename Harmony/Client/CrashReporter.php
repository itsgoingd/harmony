<?php namespace Harmony\Client;

use Harmony\Client\DataSources\DataSourceInterface;
use Harmony\Client\DataSources\PhpDataSource;

use GuzzleHttp\Client;

class CrashReporter
{
	protected $apiKey;

	protected $server;

	protected $dataSource;

	protected $errorCallback;

	public function __construct($apiKey, $server)
	{
		$this->apiKey = $apiKey;
		$this->server = $server;

		$this->dataSource = new PhpDataSource();
	}

	public function report($exception)
	{
		try {
			$data = [
				'exception'      => get_class($exception),
				'message'        => $exception->getMessage(),
				'fileName'       => $exception->getFile(),
				'lineNumber'     => $exception->getLine(),
				'callStack'      => $exception->getTrace(),
				'requestHeaders' => $this->dataSource->getRequestHeaders(),
				'requestData'    => $this->dataSource->getRequestData(),
				'queryLog'       => $this->dataSource->getQueryLog()
			];

			$client = $this->getClient();

			$client->request('POST', '/api/crashes', [ 'json' => [ 'apiKey' => $this->apiKey, 'data' => $data ] ]);
		} catch (\Exception $e) {
			$message = $e->getMessage();

			if ($this->errorCallback) {
				call_user_func_array($this->errorCallback, [ $e ]);
			} else {
				error_log("Harmony - failed to report an exception ({$message})");
			}
		}
	}

	public function setDataSource(DataSourceInterface $dataSource)
	{
		$this->dataSource = $dataSource;
	}

	public function setErrorCallback(callable $errorCallback)
	{
		$this->errorCallback = $errorCallback;
	}

	protected function getClient()
	{
		return new Client([ 'base_uri' => "http://{$this->server}" ]);
	}
}
