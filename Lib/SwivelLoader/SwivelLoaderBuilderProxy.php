<?php

App::uses('SwivelLoaderProxy', 'Swivel.Lib/SwivelLoader');

final class SwivelLoaderBuilderProxy extends SwivelLoaderProxy implements \Zumba\Swivel\BuilderInterface {

    /**
     * Parent slug prefix
     *
     * @var string
     */
    private $prefix = '';

    /**
     * An array of feature slugs that have been proxied.
     *
     * @var array
     */
    private $slugs = [];

    /**
     * Swivel Loader
     *
     * @var SwivelLoader
     */
    protected $loader;

    /**
     * Creates a new BuilderProxy from a Swivel Builder.
     *
     * @param \Zumba\Swivel\BuilderInterface $builder
     * @param SwivelLoader $loader
     * @param string $slug
     * @return \Zumba\Swivel\BuilderInterface
     */
    public static function fromBuilder(\Zumba\Swivel\BuilderInterface $builder, SwivelLoader $loader, $slug) {
        $proxy = new static($builder, $loader);
        $proxy->setPrefix($slug);
        return $proxy;
    }

    /**
     * Swivel Builder Proxy
     *
     * Keeps track of what has been called on the swivel builder
     *
     * @param \Zumba\Swivel\BuilderInterface $builder
     * @param SwivelLoader $loader
     */
    private function __construct(\Zumba\Swivel\BuilderInterface $builder, SwivelLoader $loader) {
        $this->proxy = $builder;
        $this->loader = $loader;
    }

    /**
     * Add a behavior to be executed later.
     *
     * Behavior will only be added if it is enabled for the user's bucket.
     *
     * @param string $slug
     * @param mixed  $strategy
     * @param array  $args
     *
     * @return \Zumba\Swivel\BuilderInterface
     */
    public function addBehavior($slug, $strategy, array $args = []) {
        $this->proxy->addBehavior($slug, $strategy, $args);

        $this->addSlug($slug);

        return $this;
    }

    /**
     * Add a value to be returned when the builder is executed.
     *
     * Value will only be returned if it is enabled for the user's bucket.
     *
     * @param string $slug
     * @param mixed  $value
     *
     * @return \Zumba\Swivel\BuilderInterface
     */
    public function addValue($slug, $value) {
        $this->proxy->addValue($slug, $value);

        $this->addSlug($slug);

        return $this;
    }

    /**
     * A fallback strategy if no added behaviors are active for the bucket.
     *
     * @param mixed $strategy
     * @param array $args
     *
     * @return mixed
     */
    public function defaultBehavior($strategy, array $args = []) {
        $this->proxy->defaultBehavior($strategy, $args);
        return $this;
    }

    /**
     * Creates a new Behavior object with an attached strategy.
     *
     * @param string $slug
     * @param mixed  $strategy
     *
     * @return \Zumba\Swivel\Behavior
     */
    public function getBehavior($slug, $strategy = null) {
        $this->proxy->getBehavior($slug, $strategy);
        return $this;
    }

    /**
     * Indicates that the feature has no default behavior.
     *
     * @return \Zumba\Swivel\BuilderInterface
     */
    public function noDefault() {
        $this->proxy->noDefault();
        return $this;
    }

    /**
     * Execute the feature and return the result of the behavior.
     *
     * @return mixed
     */
    public function execute() {
        $result = $this->proxy->execute();
        $slugs = array_unique($this->slugs);
        array_walk($slugs, [$this->loader, 'recordSlug']);
        return $result;
    }

    /**
     * Set a metrics object.
     *
     * @param \Zumba\Swivel\MetricsInterface $metrics
     * @return \Zumba\Swivel\BuilderInterface
     */
    public function setMetrics(\Zumba\Swivel\MetricsInterface $metrics) {
        $this->proxy->setMetrics($metrics);
        return $this;
    }

    /**
     * Sets a logger.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @return void
     */
    public function setLogger(\Psr\Log\LoggerInterface $logger) {
        $this->proxy->setLogger($logger);
    }

    /**
     * Set the prefix property
     *
     * @param string $slug
     * @return void
     */
    private function setPrefix($slug) {
        $this->prefix = $slug . '.';
    }

    /**
     * Add a prefixed slug to the slugs array
     *
     * @param string $slug
     * @return void
     */
    private function addSlug($slug) {
        $this->slugs[] = $this->prefix . $slug;
    }
}
