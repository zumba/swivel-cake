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

/**
 * Get swivel configuration
 */
$options = Configure::read('Swivel');

/**
 * If no bucket index was configured, swivel-cake will try to handle it with a cookie.
 */
if (empty($options['BucketIndex'])) {

    /**
     * Get the bucket index from the cookie, or a random bucket
     */
    $key = isset($options['Cookie']['name']) ? $options['Cookie']['name'] : 'Swivel_Bucket';
    $index = isset($_COOKIE[$key]) ? $_COOKIE[$key] : mt_rand(1, 10);

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

if (!empty($options['Cookie'])) {
    /**
     * Attach to Dispatcher.beforeDispatch event to set the cookie
     */
    App::uses('CakeEventManager', 'Event');
    CakeEventManager::instance()->attach(function ($event) use ($options) {
        $cookieName = $options['Cookie']['name'];
        if (!isset($_COOKIE[$cookieName]) || $_COOKIE[$cookieName] != $options['BucketIndex']) {
            $event->data['response']->cookie($options['Cookie'] + ['value' => $options['BucketIndex']]);
        }
    }, 'Dispatcher.beforeDispatch');
}
