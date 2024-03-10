<?php

namespace XLib2\Controller\Response;

use VAF\WP\Framework\TemplateRenderer\TemplateRenderer;
use XLib2\Application;

class TemplateResponse extends AbstractResponse
{
    public function __construct(
        private readonly string $template,
        private readonly array $data = []
    ) {
    }

    public function handle(): void
    {
        /** @var TemplateRenderer $renderer */
        $renderer = Application::getInstance()->getContainer()->get('template.renderer');

        $renderer->output($this->template, $this->data);
    }
}