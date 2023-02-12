<?php

namespace Nin\Middleware\Interfaces;

interface PipelineInterface
{
    public function addPipe(PipelineInterface $handler): PipelineInterface;

    public function handle($ob);
}
