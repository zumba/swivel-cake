<?php

App::uses('ClassRegistry', 'Utility');

class SwivelLoader {

	/**
	 * Configuration options
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * Swivel manager
	 *
	 * @var \Zumba\Swivel\Manager
	 */
	protected $manager;

	/**
	 * SwivelLoader only creates the swivel manager whenever you try to use it.
	 *
	 * @param array $options
	 */
	public function __construct($options) {
		$this->options = $options;
	}

	/**
	 * Get the swivel manager instance
	 *
	 * @return \Zumba\Swivel\Manager
	 */
	public function getManager() {
		return $this->manager ?: $this->load();
	}

	/**
	 * Get the configured swivel model.
	 *
	 * Falls back to the SwivelFeature model provided by the plugin if the app does not define one.
	 *
	 * @return SwivelModelInterface
	 */
	protected function getModel() {
		return ClassRegistry::init($this->options['ModelAlias']);
	}

	/**
	 * Create a Swivel Manager object and return it.
	 *
	 * @return \Zumba\Swivel\Manager
	 */
	protected function load() {
		$options = $this->options;
		$config = new \Zumba\Swivel\Config(
			$this->getModel()->getMapData(),
			$options['BucketIndex'],
			$options['Logger']
		);
		if (!empty($options['Metrics'])) {
			$config->setMetrics($options['Metrics']);
		}
		$this->manager = new \Zumba\Swivel\Manager($config);

		return $this->manager;
	}
}
