<?php

namespace Nin\Middleware;

class Router extends \Phalcon\Mvc\Router
{
    /**
     * @var []
     */
    protected $middleware = [];

    /**
     * @param array|string $middleware
     */
    public function middleware($middleware)
    {
        $this->middleware = $middleware;
    }
/*
    public function setMiddleware() {

    }*/


}
