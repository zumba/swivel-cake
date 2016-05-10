<?php

class SwivelFeatureFixture extends CakeTestFixture {
	public $table = 'swivel_features';

	public $fields = [
		'id' => ['type' => 'integer', 'key' => 'primary'],
		'slug' => ['type' => 'string'],
		'buckets' => ['type' => 'string'],
	];

	public $records = [
		['id' => 1, 'slug' => 'FeatureAll', 'buckets' => '1,2,3,4,5,6,7,8,9,10'],
		['id' => 2, 'slug' => 'FeatureFirst2', 'buckets' => '1,2'],
		['id' => 3, 'slug' => 'FeatureNone', 'buckets' => ''],
		['id' => 4, 'slug' => 'FeatureLastOne', 'buckets' => '10'],
	];
}
