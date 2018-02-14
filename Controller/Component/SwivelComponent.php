<?php
App::uses('Component', 'Controller');

class SwivelComponent extends Component
{

    /**
     * @var \Zumba\Swivel\Manager
     */
    protected $loader;

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
     * Initialize the component
     *
     * Creates the Swivelmanager object
     *
     * @param Controller $controller
     * @return void
     */
    public function initialize(Controller $controller)
    {
        $this->loader = ClassRegistry::getObject(Configure::read('Swivel.LoaderAlias'));
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
}
