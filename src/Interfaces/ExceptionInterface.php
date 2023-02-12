<?php

namespace Nin\Middleware\Interfaces;

interface ExceptionInterface
{
    public function report(\Throwable $e);

    public function render(\Throwable $e);
}
