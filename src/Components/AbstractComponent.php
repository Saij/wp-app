<?php

namespace WPApp\Components;

use VAF\WP\Framework\TemplateRenderer\TemplateRenderer;
use WPApp\Application;

abstract class AbstractComponent
{
    final public function __construct(
        private readonly TemplateRenderer $renderer,
        private readonly Application $application
    ) {
    }

    protected function getRenderer(): TemplateRenderer
    {
        return $this->renderer;
    }

    protected function getConfig(string $path, mixed $default = null): mixed
    {
        return $this->config->get($path, $default);
    }

    protected function getService(string $service): ?object
    {
        return $this->application->getContainer()->get($service);
    }

    abstract public function execute(array $parameters = []): string;
}