<?php

namespace Nin\Middleware\Interfaces;

interface MiddlewareManagerInterface
{
    public function attach($middleware);
}
