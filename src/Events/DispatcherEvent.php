<?php

namespace Nin\Middleware\Events;

use Phalcon\Di\Injectable;
use Phalcon\Http\Request;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Router\GroupInterface;

class DispatcherEvent extends Injectable
{
    public function beforeHandleRequest($event, Application $application)
    {
        $container = $application->getDI();

        /** @var Router $router */
        $router = $container->getShared('router');

        /** @var Request $request */
        $request = $container->getShared('request');

        $current = $router->getMatchedRoute();
        $container->get('middleware')->handleForRoute($request, $current);
    }

}
