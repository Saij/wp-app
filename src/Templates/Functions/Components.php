<?php

namespace XLib2\Templates\Functions;

use LogicException;
use VAF\WP\Framework\TemplateRenderer\Attribute\AsFunctionContainer;
use VAF\WP\Framework\TemplateRenderer\Attribute\IsFunction;
use XLib2\Application;
use XLib2\Components\AbstractComponent;

#[AsFunctionContainer]
final class Components
{
    public function __construct(private readonly Application $app)
    {
    }

    #[IsFunction('component', safeHTML: true)]
    public function component(string $componentService, array $parameters = []): string
    {
        /** @var AbstractComponent $service */
        $service = $this->app->getContainer()->get($componentService);
        if (!$service instanceof AbstractComponent) {
            throw new LogicException(
                sprintf(
                    'Component %2 has to extend %s!',
                    $componentService,
                    AbstractComponent::class
                )
            );
        }

        return $service->execute($parameters);
    }
}