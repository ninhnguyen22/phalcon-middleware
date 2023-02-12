<?php

namespace Nin\Middleware;

use Phalcon\Config\Config;

trait MiddlewarePreProcessor
{
    protected function getSpecifyMiddleware(array $aliases)
    {
        return $this->getMiddlewareFromGroups($aliases);
    }

    protected function getMiddlewareForRoute(array $aliases)
    {
        $middlewareGroups = $this->getMiddlewareFromGroups($aliases);
        $middlewareGlobal = $this->getMiddlewareGlobal();
        return [...$middlewareGroups, ...$middlewareGlobal];
    }

    public function getMiddlewareFromGroups(array $aliases)
    {
        $middleware = [];
        foreach ($aliases as $alias) {
            $pro = explode(':', $alias);
            $middlewareAlias = $pro[0];
            $param = $pro[1] ?? null;
            if ($this->middlewareGroups->has($middlewareAlias)) {
                $middleware[$alias] = $this->getPropFactory(
                    $alias,
                    $this->makeMiddlewareInstanceList($this->middlewareGroups->get($middlewareAlias)),
                    $param
                );
            }
        }
        return $middleware;
    }

    public function getMiddlewareGlobal()
    {
        $middleware = [];
        foreach ($this->middleware as $key => $alias) {
            $middleware[$key] = $this->getPropFactory(
                $key,
                $this->makeMiddlewareInstanceList([$alias])
            );
        }
        return $middleware;
    }

    protected function getPropFactory($alias, $instance, $param = null): MiddlewareProp
    {
        $mProp = new MiddlewareProp();
        $mProp->alias = $alias;
        $mProp->instance = $instance;
        $mProp->param = $param;
        return $mProp;
    }

    protected function makeMiddlewareInstanceList($middleware)
    {
        $instances = [];
        $list = $middleware;
        if ($middleware instanceof Config) {
            $list = $middleware->toArray();
        }
        foreach ($list as $m) {
            $instances[] = $this->makeMiddleware($m);
        }
        return $instances;
    }

    private function makeMiddleware($instance)
    {
        return new $instance();
    }
}
