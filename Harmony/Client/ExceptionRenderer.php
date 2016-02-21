<?php namespace Harmony\Client;

class ExceptionRenderer
{
	protected $debug;

	protected $title;

	protected $message;

	public function __construct($debug = false, $title = null, $message = null)
	{
		$this->debug   = $debug;
		$this->title   = $title ?: 'Oops, looks like something went wrong.';
		$this->message = $message ?: 'Please try again later or contact the customer support.';
	}

	public function render(array $data)
	{
		$data = $this->escapeData($data);

		return $this->getHtml($this->getContent($data), $this->getStylesheet());
	}

	public function getHtml($content, $css)
	{
		return <<< "EOF"
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Oops!</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<style>{$css}</style>
	</head>
	<body>
		{$content}
	</body>
</html>
EOF;
	}

	public function getStylesheet()
	{
		return <<< 'EOF'
		* {
			box-sizing: border-box;
		}

		body {
			font-family: Helvetica, sans-serif;
			font-size: 14px;
			padding: 60px 10px 20px;
		}

		@media (min-width: 768px) {
			body {
				font-size: 16px;
				margin: 0 auto;
				padding: 80px 0 20px;
				width: 80%;
			}
		}

		@media (min-width: 1024px) {
			body {
				width: 70%;
			}
		}

		a {
			color: #1B9FE0;
		}

		h1 {
			border-bottom: 1px solid #eee;
			font-size: 160%;
			font-weight: 300;
			margin-bottom: 15px;
			padding-bottom: 15px;
			width: 100%;
		}

		.crash-info {
			line-height: 1.4;
			margin: 15px 0;
		}

		.crash-info-title {
			margin-bottom: 20px;
			word-break: break-word;
		}

		.crash-info-title strong {
			color: #389BE6;
			font-weight: normal;
		}

		.crash-info-title .crash-info-title-exception {
			margin-bottom: 10px;
		}

		.crash-info-title .crash-info-title-exception-id {
			color: #ccc;
			font-size: 90%;
		}

		.crash-info-title .crash-info-title-file {
			font-size: 90%;
		}
EOF;
	}

	public function getContent(array $data)
	{
		if ($this->debug) {
			return $this->getContentWithDebugInfo($data);
		} else {
			return $this->getContentWithoutDebugInfo($data);
		}
	}

	public function getContentWithoutDebugInfo(array $data)
	{
		return <<< "EOF"
		<h1>{$this->title}</h1>

		<p>{$this->message}</p>
EOF;
	}

	public function getContentWithDebugInfo(array $data)
	{
		return <<< "EOF"
		<h1>Oops, looks like something went wrong.</h1>

		<div class="crash-info">

			<div class="crash-info-title">
				<div class="crash-info-title-exception"><strong>{$data['exception']}</strong> {$data['message']} <span class="crash-info-title-exception-id">(#{$data['response']['crashId']})</span></div>
				<div class="crash-info-title-file"><strong>{$data['fileName']}</strong> line <strong>{$data['lineNumber']}</strong></div>
			</div>

			<div class="crash-info-details">
				<a href="{$data['response']['url']}" target="_blank">
					Show details
				</a>
			</div>

		</div>
EOF;
	}

	protected function escapeData(array $data)
	{
		$escaped = [];

		foreach ($data as $key => $val) {
			if (is_array($val)) {
				$escaped[$key] = $this->escapeData($val);
			} else {
				$escaped[$key] = htmlspecialchars($val, ENT_QUOTES);
			}
		}

		return $escaped;
	}
}
