<?php namespace Harmony\Client\DataSources;

class PhpDataSource implements DataSourceInterface
{
	public function getRequestData()
	{
		return array_merge($_GET, $_POST, $_FILES);
	}

	public function getRequestHeaders()
	{
		$headers = [];

		foreach ($_SERVER as $key => $value) {
			if (substr($key, 0, 5) !== 'HTTP_') {
				continue;
			}

			$header = substr($key, 5);
			$header = str_replace('_', ' ', $header);
			$header = ucwords(strtolower($header));
			$header = str_replace(' ', '-', $header);

			$headers[] = [ 'header' => $header, 'value' => $value ];
		}

		return $headers;
	}

	public function getQueryLog()
	{
		return [];
	}
}
