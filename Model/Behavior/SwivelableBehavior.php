<?php

App::uses('ModelBehavior', 'Model');

class SwivelableBehavior extends ModelBehavior {

	/**
	 * @var \Zumba\Swivel\Manager
	 */
	protected $loader;

	/**
	 * Initialize the behavior
	 *
	 * Creates the Swivelmanager object
	 *
	 * @param Model $model
	 * @param array $settings
	 * @return void
	 */
	public function setup(Model $Model, $settings = []) {
		$this->loader = ClassRegistry::getObject(Configure::read('Swivel.LoaderAlias'));
	}

	/**
	 * Create a new Builder instance
	 *
	 * @param Model $model
	 * @param string $slug
	 * @return \Zumba\Swivel\Builder
	 */
	public function forFeature(Model $model, $slug) {
		return $this->loader->getManager()->forFeature($slug);
	}

	/**
	 * Syntactic sugar for creating simple feature toggles (ternary style)
	 *
	 * @param Model $model
	 * @param string $slug
	 * @param mixed $a
	 * @param mixed $b
	 * @return mixed
	 */
	public function invoke(Model $model, $slug, $a, $b = null) {
		return $this->loader->getManager()->invoke($slug, $a, $b);
	}
}
