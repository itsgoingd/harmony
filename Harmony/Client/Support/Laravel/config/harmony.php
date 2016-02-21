<?php

return [

	/**
	 * API key for this application. Create a new application on the Harmony server to get your API key.
	 * This setting can be overriden in your services configuration file.
	 */

	'apiKey' => '',

	/**
	 * How many lines of code should be collected before and after the executed line of code for each call stack frame.
	 * Set to false to disable collecting of file previews.
	 */

	'previewLines' => 3,

	/**
	 * Whether the application should be considered in debug mode. While this is enabled, some basic exception
	 * information including link to more details will be displayed on crash.
	 * Set to null to use app.debug value.
	 */

	'debug' => null,

	/**
	 * Title of the crash screen in non-debug mode.
	 */

	'crashTitle' => null,

	/**
	 * Message on the crash screen in non-debug mode.
	 */

	'crashMessage' => null,

	/**
	 * Hostname of your own Harmony server.
	 */

	'server' => ''

];
