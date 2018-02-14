<?php

App::uses('ModelBehavior', 'Model');

class SwivelableBehavior extends ModelBehavior
{

    /**
     * @var string
     */
    protected $loaderAlias;

    /**
     * Initialize the behavior
     *
     * @param Model $model
     * @param array $settings
     * @return void
     */
    public function setup(Model $Model, $settings = [])
    {
        $this->loaderAlias = Configure::read('Swivel.LoaderAlias');
    }

    /**
     * Create a new Builder instance
     *
     * @param Model $model
     * @param string $slug
     * @return \Zumba\Swivel\Builder
     */
    public function forFeature(Model $model, $slug)
    {
        return ClassRegistry::getObject($this->loaderAlias)->getManager()->forFeature($slug);
    }

    /**
     * Syntactic sugar for creating simple feature toggles (ternary style)
     *
     * @param Model $model
     * @param string    $slug
     * @param callable  $a
     * @param callable  $b
     * @return mixed
     */
    public function invoke(Model $model, $slug, $a, $b = null)
    {
        return ClassRegistry::getObject($this->loaderAlias)->getManager()->invoke($slug, $a, $b);
    }
}
