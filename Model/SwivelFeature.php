<?php

App::uses('SwivelAppModel', 'Swivel.Model');
App::uses('SwivelModelInterface', 'Swivel.Lib');

class SwivelFeature extends SwivelAppModel implements SwivelModelInterface {

	const DELIMITER = ',';

	/**
	 * Format data from cake to the data swivel expects
	 *
	 * @param array $data
	 * @return array
	 */
	protected function formatRow(array $data) {
		if (!empty($data)) {
			$row = $data[$this->alias];
			return [ $row['slug'] => explode(static::DELIMITER, $row['buckets']) ];
		}
		return [];
	}

	/**
	 * Return an array of map data in the format that Swivel expects
	 *
	 * @return array
	 */
	public function getMapData() {
		$data = $this->find('all');
		if (empty($data)) {
			return [];
		}
		return call_user_func_array('array_merge', array_map([$this, 'formatRow'], $data));
	}
}
