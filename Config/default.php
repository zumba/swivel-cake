<?php

$config = [
	'Swivel' => [
		'Cookie' => [
			'name' => 'Swivel.Bucket',
			'expire' => 0,
			'path' => '/',
			'domain' => env('HTTP_HOST'),
			'secure' => false,
			'httpOnly' => false
		],
		'LoaderAlias' => 'SwivelManager',
		'Logger' => null,
		'Metrics' => null,
		'ModelAlias' => 'Swivel.SwivelFeature',
	]
];