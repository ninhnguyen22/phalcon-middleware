<?php

namespace Nin\Middleware\Interfaces;

interface MiddlewareInterface
{
    public function handle($request);

    public function getParam();

    public function setParam($param);
}
