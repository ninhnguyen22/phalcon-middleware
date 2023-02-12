<?php

namespace Nin\Middleware;

use Nin\Middleware\Events\DispatcherEvent;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers the service provider.
     *
     * @param \Phalcon\Di\DiInterface $container
     * @throws \Exception
     */
    public function register(DiInterface $container): void
    {
        $container->setShared('middleware', function () use ($container) {
            $configReader = new ConfigReader();
            return new MiddlewareManager($container, $configReader);
        });

        $this->boot($container);
    }

    protected function boot(DiInterface $container)
    {
        $this->registerRouterEvent($container);
    }

    protected function registerRouterEvent(DiInterface $container)
    {
        $events = $container['eventsManager'];
        $events->attach('application', new DispatcherEvent());
    }

}
