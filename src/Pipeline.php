<?php

namespace Nin\Middleware;

use Nin\Middleware\Interfaces\PipelineInterface;

class Pipeline implements PipelineInterface
{
    /**
     * @var
     */
    private $passable;

    /**
     * @var PipelineInterface
     */
    protected PipelineInterface $nextPipe;

    /**
     * Add next pipe task
     * @param PipelineInterface $handler
     * @return PipelineInterface
     */
    public function addPipe(PipelineInterface $handler): PipelineInterface
    {
        $this->nextPipe = $handler;

        return $handler;
    }

    /**
     * Add next pipe task
     * @param $handlers
     * @return PipelineInterface
     */
    public function addPipes($handlers): PipelineInterface
    {
        $first = true;
        $next = null;
        foreach ($handlers as $handler) {
            foreach ($handler->instance as $instance) {
                $ob = $instance;
                $ob->setParam($handler->param);
                if ($first) {
                    $next = $this->addPipe($ob);
                    $first = false;
                    continue;
                }
                $next = $next->addPipe($ob);
            }
        }

        return $this;
    }

    /**
     * Handle pipe task
     * @param $ob
     * @return mixed
     */
    public function handle($ob)
    {
        if (isset($this->nextPipe)) {
            return $this->nextPipe->handle($ob);
        }

        return $ob;
    }

    /**
     * Send passable
     *
     * @param $passable
     * @return $this
     */
    public function send($passable)
    {
        $this->passable = $passable;

        return $this;
    }

    /**
     * Run pipeline and return passple
     *
     * @return mixed
     */
    public function thenReturn()
    {
        return $this->handle($this->passable);
    }

    /**
     * Run pipeline with a final pipe task
     *
     * @param \Closure $callback
     * @return mixed
     */
    public function then(\Closure $callback)
    {
        $passable = $this->handle($this->passable);
        return $callback($passable);
    }
}
