<?php

namespace XLib2\Controller\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class TemplateRoute
{
    public function __construct(
        public readonly string $template,
        public readonly bool $showInCustom = false,
        public readonly string $description = ''
    ) {
    }
}