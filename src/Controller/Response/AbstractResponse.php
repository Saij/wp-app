<?php

namespace XLib2\Controller\Response;

abstract class AbstractResponse
{
    abstract public function handle(): void;
}