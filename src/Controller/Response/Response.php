<?php

namespace XLib2\Controller\Response;

class Response extends AbstractResponse
{
    public function __construct(private readonly string $content)
    {
    }

    public function handle(): void
    {
        echo $this->content;
    }
}