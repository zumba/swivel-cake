<?php

App::uses('ClassRegistry', 'Utility');
App::uses('SwivelLoaderManagerProxy', 'Swivel.Lib/SwivelLoader');

class SwivelLoader
{

    const ALL_ON = '1,2,3,4,5,6,7,8,9,10';
    const ALL_OFF = '0';

    /**
     * Swivel config
     *
     * @var \Zumba\Swivel\Config
     */
    protected $config;

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
     * Array of features for the Swivel Config
     *
     * @var array
     */
    protected $features;

    /**
     * An array of feature slugs.
     *
     * This will only be populated by a slug if that slug is partially enabled, and it was accessed
     * by the manager or the builder.
     *
     * For example, given the following slugs that were executed:
     *
     * 'Test.a' => '1,2,3,4,5,6,7,8,9,10'
     * 'Test.b' => '',
     * 'Test.c' => '1,2,3'
     *
     * This array will look like this:  [ 'Test.c' ]
     *
     * @var array
     */
     protected $deviated = [];

    /**
     * SwivelLoader only creates the swivel manager whenever you try to use it.
     *
     * @param array $options
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * Get the swivel config instance
     *
     * @return \Zumba\Swivel\Config
     */
    public function getConfig()
    {
        if (empty($this->config)) {
            $options = $this->options;

            if ($hasCallable = isset($options['MissingSlug']['Model']) && isset($options['MissingSlug']['Callback'])) {
                App::uses($options['MissingSlug']['Model'], 'Model');
            }

            $this->features = $this->getModel()->getMapData();
            $this->config = new \Zumba\Swivel\Config(
                $this->features,
                $options['BucketIndex'],
                $options['Logger'],
                $hasCallable ? array(ClassRegistry::init($options['MissingSlug']['Model']) , $options['MissingSlug']['Callback']) : null
            );
            if (!empty($options['Metrics'])) {
                $this->config->setMetrics($options['Metrics']);
            }
        }
        return $this->config;
    }

    /**
     * Get the swivel manager instance
     *
     * @return \Zumba\Swivel\Manager
     */
    public function getManager()
    {
        return $this->manager ?: $this->load();
    }

    /**
     * Get the configured swivel model.
     *
     * Falls back to the SwivelFeature model provided by the plugin if the app does not define one.
     *
     * @return SwivelModelInterface
     */
    protected function getModel()
    {
        return ClassRegistry::init($this->options['ModelAlias']);
    }

    /**
     * Create a Swivel Manager object and return it.
     *
     * @return \Zumba\Swivel\Manager
     */
    protected function load()
    {
        $manager = new \Zumba\Swivel\Manager($this->getConfig());
        $this->manager = SwivelLoaderManagerProxy::fromManager($manager, $this);
        return $this->manager;
    }

    /**
     * Used to set the bucket index before loading swivel.
     *
     * @param integer $index Number between 1 and 10
     * @return void
     * @throws InvalidArgumentException if $index is not valid
     */
    public function setBucketIndex($index)
    {
        if (!is_numeric($index) || $index < 1 || $index > 10) {
            throw new InvalidArgumentException("$index is not a valid bucket index.");
        }
        if (empty($this->manager)) {
            $this->options['BucketIndex'] = $index;
        } else {
            $config = $this->getConfig();
            $config->setBucketIndex($index);
            $this->manager->setBucket($config->getBucket());
        }
    }

    /**
     * Records a slug only if it has deviated.
     *
     * @param string $slug
     * @return void
     */
    public function recordSlug($slug) {
        if (isset($this->features[$slug]) && !in_array($slug, $this->deviated)) {
            sort($this->features[$slug]);
            $test = implode(',', $this->features[$slug]);
            if ($test !== static::ALL_ON && $test !== static::ALL_OFF) {
                $this->deviated[] = $slug;
            }
        }
    }

    /**
     * Check to see if Swivel has possibly diverged code execution.
     *
     * @return boolean
     */
    public function hasDiverged() {
        return !empty($this->features) && !empty($this->deviated);
    }

    /**
     * Get a list of diverged features.
     *
     * @return boolean
     */
    public function getDiverged() {
        return $this->deviated;
    }
}
