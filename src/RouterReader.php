<?php

namespace Nin\Middleware;

use Phalcon\Mvc\Router\GroupInterface;
use Phalcon\Mvc\Router\Route;
use Phalcon\Mvc\Router\RouteInterface;

/**
 * Read middleware from route
 *
 * @package Nin\Middleware
 */
class RouterReader
{
    protected Route $route;

    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    public function getMiddleware(): array
    {
        $middleware = $this->getFromRouteGroup($this->route);
        foreach ($this->getFromRoute($this->route) as $m) {
            if (!in_array($m, $middleware)) {
                $middleware[] = $m;
            }
        }
        return $middleware;
    }

    protected function getFromRouteGroup(RouteInterface $route): array
    {
        /** @var GroupInterface $group */
        $group = $route->getGroup();
        if ($group) {
            return $this->getRouteMiddleware($group);
        }
        return [];
    }

    protected function getFromRoute(RouteInterface $route): array
    {
        return $this->getRouteMiddleware($route);
    }

    protected function getRouteMiddleware(GroupInterface|RouteInterface $route): array
    {
        $paths = $route->getPaths();
        if (is_array($paths) && isset($paths['middleware'])) {
            return is_array($paths['middleware']) ? $paths['middleware'] : [$paths['middleware']];
        }
        return [];
    }

}
