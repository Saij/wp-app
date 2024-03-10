<?php

namespace XLib2\Controller;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class LoaderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(Loader::class)) {
            return;
        }

        $loaderDefinition = $container->findDefinition(Loader::class);

        $controllerServices = $container->findTaggedServiceIds('controller.route');

        $customRoutes = [];
        $routes = [];

        foreach ($controllerServices as $id => $tags) {
            $definition = $container->findDefinition($id);
            $definition->setPublic(true);

            foreach ($tags as $tag) {
                $routes[$tag['template']] = $id;
                if ($tag['isCustom'] ?? false) {
                    $customRoutes[$tag['template']] = $tag['description'];
                }
            }
        }

        $loaderDefinition->setArgument('$routes', $routes);
        $loaderDefinition->setArgument('$customRoutes', $customRoutes);
    }
}