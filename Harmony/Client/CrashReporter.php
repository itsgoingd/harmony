<?php namespace Harmony\Client;

use Harmony\Client\DataSources\DataSourceInterface;
use Harmony\Client\DataSources\PhpDataSource;

use GuzzleHttp\Client;

class CrashReporter
{
	protected $apiKey;

	protected $server;

	public function __construct($apiKey, $server)
	{
		$this->apiKey = $apiKey;
		$this->server = $server;

		$this->dataSource = new PhpDataSource();
	}

	public function report($exception)
	{
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
	}

	public function setDataSource(DataSourceInterface $dataSource)
	{
		$this->dataSource = $dataSource;
	}

	protected function getClient()
	{
		return new Client([ 'base_uri' => "http://{$this->server}" ]);
	}
}
