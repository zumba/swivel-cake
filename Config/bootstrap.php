<?php

!defined('SWIVEL_DIR') && define('SWIVEL_DIR', dirname(__DIR__));
!defined('SWIVEL_VENDOR') && define('SWIVEL_VENDOR', SWIVEL_DIR . '/Vendor/');

/**
 * Get the composer autoloader
 */
require_once SWIVEL_VENDOR . 'autoload.php';

/**
 * Load Swivel plugin dsefaults
 */
Configure::load('Swivel.default');

/**
 * Load application config for swivel.  Will overwrite the defaults.
 */
if (file_exists(APP . 'Config/swivel.php')) {
	Configure::load('swivel');
}

//ClassRegistry::addObject(Configure::read('Swivel.LoaderAlias'), new SwivelLoader($options));

App::uses('CakeEventManager', 'Event');

/**
 * Attach to Dispatcher.beforeDispatch event
 */
CakeEventManager::instance()->attach(function($event) {

	$options = Configure::read('Swivel');

	/**
	 * Get the bucket index from the cookie.
	 * Will set the cookie if it is not set.
	 */
	$key = $options['Cookie']['name'];
	if (isset($_COOKIE[$key])) {
		$index = $_COOKIE[$key];
	} else {
		$index = mt_rand(1, 10);
		$event->data['response']->cookie($options['Cookie'] + ['value' => $index]);
	}

	/**
	 * Add the index to the SwivelLoader options
	 */
	$options['BucketIndex'] = $index;

	/**
	 * Register the SwivelLoader for lazy loading.
	 */
	App::uses('ClassRegistry', 'Utility');
	App::uses('SwivelLoader', 'Swivel.Lib');
	ClassRegistry::addObject($options['LoaderAlias'], new SwivelLoader($options));

}, 'Dispatcher.beforeDispatch');
