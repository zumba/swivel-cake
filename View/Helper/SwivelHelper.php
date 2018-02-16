<?php

App::uses('AppHelper', 'View/Helper');

class SwivelHelper extends AppHelper
{

    /**
     * @var \Zumba\Swivel\Manager
     */
    protected $loader;

    /**
     * Initialize the helper
     *
     * Creates the Swivelmanager object
     *
     * @param View $View
     * @param array $settings
     * @return void
     */
    public function __construct(View $view, $settings = [])
    {
        parent::__construct($view, $settings);
        $this->loader = ClassRegistry::getObject(Configure::read('Swivel.LoaderAlias'));
    }

    /**
     * Create a new Builder instance
     *
     * @param string $slug
     * @return \Zumba\Swivel\Builder
     */
    public function forFeature($slug)
    {
        return $this->loader->getManager()->forFeature($slug);
    }

    /**
     * Syntactic sugar for creating simple feature toggles (ternary style)
     *
     * @param string    $slug
     * @param callable  $a
     * @param callable  $b
     * @return mixed
     */
    public function invoke($slug, $a, $b = null)
    {
        return $this->loader->getManager()->invoke($slug, $a, $b);
    }

    /**
     * Shorthand syntactic sugar for return a simple feature value given a behavior
     *
     * @param string    $slug
     * @param mixed     $a
     * @param mixed     $b
     * @return mixed
     */
    public function returnValue($slug, $a, $b = null) {
        return $this->loader->getManager()->returnValue($slug, $a, $b);
    }
}
