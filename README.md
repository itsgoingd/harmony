Harmony
=======

## Installation

This extension provides out of the box support for Laravel, you can use it with any other framework or vanilla php though.

To install latest version simply add it to your `composer.json`:

```javascript
"itsgoingd/harmony": "dev-master"
```

You also need to add this repository to the repositories section in your `composer.json` as this package is not yet available on Packagist.

```javascript
"repositories": [
	{
		"type": "vcs",
		"url": "https://github.com/itsgoingd/harmony.git"
	}
],
```

### Laravel

Once Harmony is installed, you need to register Laravel service provider, in your `config/app.php`:

```php
'providers' => [
	...
	Harmony\Client\Support\Laravel\ServiceProvider::class
]
```

Setup your Harmony server address and application API key in your `config/services.php`:

```php
'harmony' => [
	'apiKey' => env('HARMONY_API_KEY'),
	'server' => env('HARMONY_SERVER')
],
```

To log all uncaught exceptions update your exception handler report method like this:

```php
public function report(Exception $e)
{
	app('harmony')->report($e);

	parent::report($e);
}
```

While you can set your API key in the services configuration file, there is also a full Harmony configuration file available including various advanced settings. Use the following Artisan command to publish the configuration file into your config directory:

```
php artisan vendor:publish --provider='Harmony\Client\Support\Laravel\ServiceProvider'
```

## Licence

Copyright (c) 2016 Miroslav Rigler

MIT License

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
