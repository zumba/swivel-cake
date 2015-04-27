<?php

$config = [
	'Swivel' => [
		'Cookie' => [
			'name' => 'Swivel_Bucket',
			'expire' => 0,
			'path' => '/',
			'domain' => env('HTTP_HOST'),
			'secure' => false,
			'httpOnly' => false
		],
		'BucketIndex' => null,
		'LoaderAlias' => 'SwivelManager',
		'Logger' => null,
		'Metrics' => null,
		'ModelAlias' => 'Swivel.SwivelFeature',
	]
];