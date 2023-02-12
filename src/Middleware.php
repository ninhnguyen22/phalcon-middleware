<?php

namespace Nin\Middleware;

use Nin\Middleware\Interfaces\MiddlewareInterface;
use Phalcon\Di\Di;
use Phalcon\Mvc\Url;

class Middleware extends Pipeline implements MiddlewareInterface
{
    protected $param;

    /**
     * Handle pipe task
     *
     * @param $request
     * @return mixed
     */
    public function handle($request)
    {
        if (isset($this->nextPipe)) {
            return $this->nextPipe->handle($request);
        }

        return true;
    }

    /**
     * Redirect to route
     *
     * @param string $routeName
     * @throws \Exception
     */
    public function redirectToRoute(string $routeName)
    {
        $container = Di::getDefault();
        /** @var \Phalcon\Mvc\Router $route */
        $router = $container->get('router');

        /** @var \Phalcon\Mvc\Router\Route $route */
        $route = $router->getRouteByName($routeName);

        if ($route) {
            $this->redirect((new Url())->path($route->getCompiledPattern()));
        }
        $this->redirect('/');
    }

    /**
     * Redirect to url
     *
     * @param string $url
     */
    public function redirectToUrl(string $url)
    {
        $this->redirect($url);
    }

    public function setParam($param)
    {
        $this->param = $param;
    }

    public function getParam()
    {
        return $this->param;
    }

    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit();
    }
}
