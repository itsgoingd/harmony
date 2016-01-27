<?php namespace Harmony\Client\DataSources;

interface DataSourceInterface
{
	public function getRequestData();

	public function getRequestHeaders();

	public function getQueryLog();
}
