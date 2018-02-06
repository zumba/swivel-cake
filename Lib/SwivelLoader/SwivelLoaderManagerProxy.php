<?php

App::uses('SwivelLoaderProxy', 'Swivel.Lib/SwivelLoader');
App::uses('SwivelLoaderBuilderProxy', 'Swivel.Lib/SwivelLoader');

final class SwivelLoaderManagerProxy extends SwivelLoaderProxy implements \Zumba\Swivel\ManagerInterface {

    /**
     * Swivel Loader
     *
     * @var SwivelLoader
     */
    protected $loader;

    /**
     * Creates a new ManagerProxy from a Swivel Manager.
     *
     * @param \Zumba\Swivel\ManagerInterface $manager
     * @param SwivelLoader $loader
     * @return \Zumba\Swivel\ManagerInterface
     */
    public static function fromManager(\Zumba\Swivel\ManagerInterface $manager, SwivelLoader $loader) {
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
    private function __construct(\Zumba\Swivel\ManagerInterface $manager, SwivelLoader $loader) {
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
    public function forFeature($slug) {
        return SwivelLoaderBuilderProxy::fromBuilder($this->proxy->forFeature($slug), $this->loader, $slug);
    }

    /**
     * Syntactic sugar for creating simple feature toggles (ternary style).
     *
     * @param string $slug
     * @param mixed  $a
     * @param mixed  $b
     *
     * @return mixed
     */
    public function invoke($slug, $a, $b = null) {
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
     * @see \Zumba\Swivel\ManagerInterface
     */
    public function returnValue($slug, $a, $b = null) {
        $result = $this->proxy->returnValue($slug, $a, $b);
        $this->loader->recordSlug($slug);
        return $result;
    }

    /**
     * Set the Swivel Bucket.
     *
     * @param \Zumba\Swivel\BucketInterface $bucket
     *
     * @return \Zumba\Swivel\ManagerInterface
     */
    public function setBucket(\Zumba\Swivel\BucketInterface $bucket = null) {
        return $this->proxy->setBucket($bucket);
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
}
