<?php

namespace XLib2\Controller;

use XLib2\Controller\Response\AbstractResponse;

abstract class AbstractController
{
    abstract public function execute(): AbstractResponse;
}