<?php namespace Harmony\Client;

use Symfony\Component\Debug\Exception\FlattenException as BaseFlattenException;

class FlattenException extends BaseFlattenException
{
	public function getTrace($previewLines = 3)
	{
		$trace = parent::getTrace();

		if (! is_array($trace)) {
			return;
		}

		$trace[1]['file'] = $this->getFile();
		$trace[1]['line'] = $this->getLine();

		if ($previewLines === false) {
			return $trace;
		}

		foreach ($trace as &$item) {
			if (! $item['file'] || ! $item['line']) {
				continue;
			}

			$item['file_preview'] = [];

			$fileContents = file_get_contents($item['file']);
			$fileContents = explode("\n", $fileContents);

			$firstLine = max($item['line'] - $previewLines, 1);
			$lastLine  = min($item['line'] + $previewLines, count($fileContents));

			for ($line = $firstLine; $line <= $lastLine; $line++) {
				$item['file_preview'][$line] = $fileContents[$line - 1];
			}
		}

		return $trace;
	}
}
