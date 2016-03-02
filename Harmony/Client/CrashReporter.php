<?php namespace Harmony\Client;

use Harmony\Client\DataSources\DataSourceInterface;
use Harmony\Client\DataSources\PhpDataSource;

use GuzzleHttp\Client;

class CrashReporter
{
	protected $apiKey;

	protected $server;

	protected $previewLines;

	protected $dataSource;

	protected $renderer;

	protected $errorCallback;

	protected $lastReported;

	public function __construct($apiKey, $server, $previewLines = false)
	{
		$this->apiKey = $apiKey;
		$this->server = $server;

		$this->previewLines = $previewLines;

		$this->dataSource = new PhpDataSource();
		$this->renderer   = new ExceptionRenderer();
	}

	public function report($exception)
	{
		try {
			$exception = FlattenException::create($exception);

			$data = [
				'exception'      => $exception->getClass(),
				'message'        => $exception->getMessage(),
				'fileName'       => $exception->getFile(),
				'lineNumber'     => $exception->getLine(),
				'callStack'      => $exception->getTrace($this->previewLines),
				'requestHeaders' => $this->dataSource->getRequestHeaders(),
				'requestData'    => $this->dataSource->getRequestData(),
				'queryLog'       => $this->dataSource->getQueryLog()
			];

			$client = $this->getClient();

			$response = $client->request('POST', '/api/crashes', [ 'json' => [ 'apiKey' => $this->apiKey, 'data' => $data ] ]);

			$this->lastReported = $data;
			$this->lastReported['response'] = json_decode($response->getBody(), true);
		} catch (\Exception $e) {
			$message = $e->getMessage();

			if ($this->errorCallback) {
				call_user_func_array($this->errorCallback, [ $e ]);
			} else {
				error_log("Harmony - failed to report an exception ({$message})");
			}
		}
	}

	public function renderLastReported()
	{
		return $this->renderer->render($this->lastReported);
	}

	public function setDataSource(DataSourceInterface $dataSource)
	{
		$this->dataSource = $dataSource;
	}

	public function setErrorCallback(callable $errorCallback)
	{
		$this->errorCallback = $errorCallback;
	}

	public function setRenderer(ExceptionRenderer $renderer)
	{
		$this->renderer = $renderer;
	}

	protected function getClient()
	{
		$server = $this->server;

		if (strpos($server, 'http://') !== 0 && strpos($server, 'https://') !== 0) {
			$server = "https://{$server}";
		}

		return new Client([ 'base_uri' => $server ]);
	}
}
