<?php

namespace Nin\Middleware\Exceptions;

use Nin\Middleware\Interfaces\ExceptionInterface;
use Phalcon\Di\DiInterface;
use Throwable;

class MiddlewareHandleException implements ExceptionInterface
{
    protected DiInterface $container;

    public function __construct(DiInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Report or log an exception.
     *
     * @param Throwable $e
     * @throws Throwable
     */
    public function report(Throwable $e)
    {
        try {
            if ($this->container->has('logger')) {
                $logger = $this->container->get('logger');
                $this->logException($logger, $e);
            }
        } catch (\Exception $ex) {
            throw $e;
        }
    }

    /**
     * Render or log an exception.
     *
     * @param Throwable $e
     * @throws Throwable
     */
    public function render(Throwable $e)
    {
        throw $e;
    }

    protected function logException($logger, Throwable $e)
    {
        $logger->error(
            'Middleware Error: ' . $e->getMessage(),
            ['exception' => $e]
        );
    }

}
