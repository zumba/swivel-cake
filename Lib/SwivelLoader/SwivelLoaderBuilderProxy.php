<?php

App::uses('SwivelLoaderProxy', 'Swivel.Lib/SwivelLoader');

use Psr\Log\LoggerInterface;
use Zumba\Swivel\BehaviorInterface;
use Zumba\Swivel\Builder;
use Zumba\Swivel\BuilderInterface;
use Zumba\Swivel\MetricsInterface;

final class SwivelLoaderBuilderProxy extends SwivelLoaderProxy implements BuilderInterface {

    /**
     * Parent slug prefix
     *
     * @var string
     */
    private string $prefix = '';

    /**
     * An array of feature slugs that have been proxied.
     *
     * @var array
     */
    private array $slugs = [];

    /**
     * Swivel Loader
     *
     * @var SwivelLoader
     */
    protected SwivelLoader $loader;

    /**
     * Creates a new BuilderProxy from a Swivel Builder.
     *
     * @param \Zumba\Swivel\BuilderInterface $builder
     * @param SwivelLoader $loader
     * @param string $slug
     * @return \Zumba\Swivel\BuilderInterface
     */
    public static function fromBuilder(BuilderInterface $builder, SwivelLoader $loader, string $slug): BuilderInterface {
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
    private function __construct(BuilderInterface $builder, SwivelLoader $loader) {
        $this->proxy = $builder;
        $this->loader = $loader;
    }

    /**
     * Add a behavior to be executed later.
     *
     * Behavior will only be added if it is enabled for the user's bucket.
     *
     * @param string    $slug
     * @param callable  $strategy
     * @param array     $args
     *
     * @return \Zumba\Swivel\BuilderInterface
     */
    public function addBehavior(string $slug, mixed $strategy, array $args = []): BuilderInterface {
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
    public function addValue(string $slug, mixed $value): BuilderInterface {
        $this->proxy->addValue($slug, $value);

        $this->addSlug($slug);

        return $this;
    }

    /**
     * A fallback strategy if no added behaviors are active for the bucket.
     *
     * @param callable  $strategy
     * @param array     $args
     *
     * @return \Zumba\Swivel\BuilderInterface
     */
    public function defaultBehavior(mixed $strategy, array $args = []): BuilderInterface {
        $this->proxy->defaultBehavior($strategy, $args);
        return $this;
    }

    /**
     * A fallback value if no added behaviors are active for the bucket.
     *
     * @param mixed $value
     *
     * @return \Zumba\Swivel\BuilderInterface
     */
    public function defaultValue(mixed $value): BuilderInterface {
        $this->proxy->defaultValue($value);
        return $this;
    }

    /**
     * Creates a new Behavior object with an attached strategy.
     *
     * The default for $strategy mirrors \Zumba\Swivel\Builder so that calling
     * getBehavior($callable) with a single argument keeps working through the proxy.
     *
     * @param string|callable $slug
     * @param mixed           $strategy
     *
     * @return \Zumba\Swivel\BehaviorInterface
     */
    public function getBehavior(string|callable $slug, mixed $strategy = Builder::DEFAULT_STRATEGY): BehaviorInterface {
        return $this->proxy->getBehavior($slug, $strategy);
    }

    /**
     * Indicates that the feature has no default behavior.
     *
     * @return \Zumba\Swivel\BuilderInterface
     */
    public function noDefault(): BuilderInterface {
        $this->proxy->noDefault();
        return $this;
    }

    /**
     * Execute the feature and return the result of the behavior.
     *
     * @return mixed
     */
    public function execute(): mixed {
        $result = $this->proxy->execute();
        $slugs = array_unique($this->slugs);
        array_walk($slugs, [$this->loader, 'recordSlug']);
        return $result;
    }

    /**
     * Set a metrics object.
     *
     * @param \Zumba\Swivel\MetricsInterface $metrics
     * @return void
     */
    public function setMetrics(MetricsInterface $metrics): void {
        $this->proxy->setMetrics($metrics);
    }

    /**
     * Sets a logger.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger): void {
        $this->proxy->setLogger($logger);
    }

    /**
     * Set the prefix property
     *
     * @param string $slug
     * @return void
     */
    private function setPrefix(string $slug): void {
        $this->prefix = $slug . '.';
    }

    /**
     * Add a prefixed slug to the slugs array
     *
     * @param string $slug
     * @return void
     */
    private function addSlug(string $slug): void {
        $this->slugs[] = $this->prefix . $slug;
    }
}
