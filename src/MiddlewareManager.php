<?php

namespace Nin\Middleware;

use Nin\Middleware\Exceptions\MiddlewareHandleException;
use Nin\Middleware\Interfaces\MiddlewareManagerInterface;
use Phalcon\Config\Config;
use Phalcon\Di\DiInterface;
use Throwable;

class MiddlewareManager implements MiddlewareManagerInterface
{
    use MiddlewarePreProcessor;

    /**
     * @var DiInterface $container
     */
    protected $container;

    /**
     * Default middleware.
     *
     * @var Config $middleware
     */
    protected $middleware;

    /**
     * Group middleware
     * Only call when configured into the router
     *
     * @var Config $middlewareGroups
     */
    protected $middlewareGroups;

    public function __construct(DiInterface $container, ConfigReader $configReader)
    {
        $this->container = $container;
        $this->loadMiddlewareFromConfig($configReader);
    }

    /**
     * Load middleware from config
     *
     * @param ConfigReader $configReader
     */
    protected function loadMiddlewareFromConfig(ConfigReader $configReader)
    {
        $this->middleware = $configReader->getGlobalMiddleware();
        $this->middlewareGroups = $configReader->getGroupsMiddleware();
    }

    /**
     * Middle handler for router
     *
     * @param $request
     * @param $middleware
     * @return mixed
     */
    public function handle($request, $middleware)
    {
        try {
            return $this->sendThoughMiddleware($request, $middleware)
                ->then($this->dispatchToRouter());
        } catch (Throwable $e) {
            $handle = $this->getHandleException();
            $this->reportException($handle, $e);
            $this->renderException($handle, $e);
        }
    }

    /**
     * Middle handler for router
     *
     * @param $request
     * @param $route
     * @return mixed
     */
    public function handleForRoute($request, $route)
    {
        $middleware = $this->getMiddlewareForRoute($this->getRouteReader($route)->getMiddleware());
        return $this->handle($request, $middleware);
    }

    public function attach($middleware)
    {
        try {
            if (is_string($middleware)) {
                $middleware = [$middleware];
            }
            $middleware = $this->getSpecifyMiddleware($middleware);
            $this->sendThoughMiddleware($this->container['request'], $middleware)
                ->then(function () {
                });
        } catch (Throwable $e) {
            $handle = $this->getHandleException();
            $this->reportException($handle, $e);
            $this->renderException($handle, $e);
        }
    }

    /**
     * Send though middleware
     *
     * @param $request
     * @param array $middlewareList
     * @return Middleware
     */
    private function sendThoughMiddleware($request, $middlewareList)
    {
        $pipeline = $this->getMiddleware();
        $pipeline->send($request);
        $pipeline->addPipes($middlewareList);

        return $pipeline;
    }

    public function getMiddleware()
    {
        return new Middleware();
    }

    public function getHandleException()
    {
        return new MiddlewareHandleException($this->container);
    }

    public function getRouteReader($route)
    {
        return new RouterReader($route);
    }

    private function dispatchToRouter()
    {
        $container = $this->container;
        return function () use ($container) {
            if (!$container->has('dispatcher')) {
                $dispatcher = new \Phalcon\Mvc\Dispatcher();
            } else {
                $dispatcher = $container->get('dispatcher');
            }
            $dispatcher->dispatch();
        };
    }

    private function reportException($handle, Throwable $e)
    {
        $handle->report($e);
    }

    private function renderException($handle, Throwable $e)
    {
        $handle->render($e);
    }

}
