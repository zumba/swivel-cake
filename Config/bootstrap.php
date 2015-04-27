<?php

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

App::uses('CakeEventManager', 'Event');

/**
 * Attach to Dispatcher.beforeDispatch event
 */
CakeEventManager::instance()->attach(function($event) {

	$options = Configure::read('Swivel');

	/**
	 * If no bucket index was configured, swivel-cake will try to handle it with a cookie.
	 */
	if (empty($options['BucketIndex'])) {

		/**
		 * Get the bucket index from the cookie. Will set the cookie if it is not set.
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
	}

	/**
	 * Register the SwivelLoader for lazy loading.
	 */
	App::uses('ClassRegistry', 'Utility');
	App::uses('SwivelLoader', 'Swivel.Lib');
	ClassRegistry::addObject($options['LoaderAlias'], new SwivelLoader($options));

}, 'Dispatcher.beforeDispatch');
