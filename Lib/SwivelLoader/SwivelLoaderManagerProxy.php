<?php

App::uses('SwivelLoaderProxy', 'Swivel.Lib/SwivelLoader');
App::uses('SwivelLoaderBuilderProxy', 'Swivel.Lib/SwivelLoader');

use Psr\Log\LoggerInterface;
use Zumba\Swivel\BucketInterface;
use Zumba\Swivel\BuilderInterface;
use Zumba\Swivel\ManagerInterface;

final class SwivelLoaderManagerProxy extends SwivelLoaderProxy implements ManagerInterface {

    /**
     * Swivel Loader
     *
     * @var SwivelLoader
     */
    protected SwivelLoader $loader;

    /**
     * Creates a new ManagerProxy from a Swivel Manager.
     *
     * @param \Zumba\Swivel\ManagerInterface $manager
     * @param SwivelLoader $loader
     * @return \Zumba\Swivel\ManagerInterface
     */
    public static function fromManager(ManagerInterface $manager, SwivelLoader $loader): ManagerInterface {
        return new static($manager, $loader);
    }

    /**
     * Swivel Manager Proxy
     *
     * Keeps track of what has been called on the swivel manager
     * and wraps swivel builders with Builder Proxy objects
     *
     * @param \Zumba\Swivel\ManagerInterface $manager
     * @param SwivelLoader $loader
     */
    private function __construct(ManagerInterface $manager, SwivelLoader $loader) {
        $this->proxy = $manager;
        $this->loader = $loader;
    }

    /**
     * Create a new Builder Proxy instance.
     *
     * @param string $slug
     *
     * @return \Zumba\Swivel\BuilderInterface
     */
    public function forFeature(string $slug): BuilderInterface {
        return SwivelLoaderBuilderProxy::fromBuilder($this->proxy->forFeature($slug), $this->loader, $slug);
    }

    /**
     * Syntactic sugar for creating simple feature toggles (ternary style).
     *
     * @param string    $slug
     * @param callable  $a
     * @param callable  $b
     *
     * @return mixed
     */
    public function invoke(string $slug, mixed $a, mixed $b = null): mixed {
        $result = $this->proxy->invoke($slug, $a, $b);
        $this->loader->recordSlug($slug);
        return $result;
    }

    /**
     * Syntactic sugar for creating simple feature toggles (ternary style).
     *
     * Uses Builder::addValue
     *
     * @param string $slug
     * @param mixed  $a
     * @param mixed  $b
     *
     * @return mixed
     *
     * @see \Zumba\Swivel\Manager
     */
    public function returnValue(string $slug, mixed $a, mixed $b = null): mixed {
        $result = $this->proxy->returnValue($slug, $a, $b);
        $this->loader->recordSlug($slug);
        return $result;
    }

    /**
     * Set the Swivel Bucket.
     *
     * Returns the proxy (not the wrapped manager) so that chained calls keep
     * recording slugs.
     *
     * @param \Zumba\Swivel\BucketInterface|null $bucket
     *
     * @return \Zumba\Swivel\ManagerInterface
     */
    public function setBucket(?BucketInterface $bucket = null): ManagerInterface {
        $this->proxy->setBucket($bucket);
        return $this;
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
}
