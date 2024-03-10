<?php

namespace XLib2\Templates\Functions;

use VAF\WP\Framework\TemplateRenderer\Attribute\AsFunctionContainer;
use VAF\WP\Framework\TemplateRenderer\Attribute\IsFunction;
use XLib2\Application;

#[AsFunctionContainer]
class Assets
{
    #[IsFunction('asset')]
    public function getAssetURL(string $asset): string
    {
        return Application::getInstance()->getAssetUrl($asset);
    }
}