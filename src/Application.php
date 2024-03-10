<?php

namespace WPApp;

use Exception;
use LogicException;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VAF\WP\Framework\TemplateRenderer\TemplateRenderer;
use VAF\WP\Framework\Theme;
use VAF\WP\Framework\Utils\ThemeSearchMode;
use WPApp\Controller\Attributes\TemplateRoute;
use WPApp\Controller\LoaderCompilerPass as ControllerLoaderCompilerPass;

abstract class Application extends Theme
{
    private static ?Application $app = null;

    final public static function boot(): void
    {
        if (!is_null(self::$app)) {
            throw new LogicException('Application already booted!');
        }

        self::$app = new static(defined('WP_DEBUG') && WP_DEBUG);
        self::$app->kernel->boot();
        self::$app->registerTemplateNamespaces();
        self::$app->setupDebug();
        self::$app->configureApplication();
    }

    abstract protected function configureApplication(): void;

    private function setupDebug(): void
    {
        if ($this->getStage() !== 'development') {
            return;
        }

        /** @var TemplateRenderer $templateRenderer */
        $templateRenderer = $this->get(TemplateRenderer::class);
        $templateRenderer->enableDebug();
    }

    protected function getTemplateNamespace(): string
    {
        $theme = wp_get_theme();
        $name = $theme->get_stylesheet();

        if (false !== $theme->parent()) {
            $name = $theme->parent()->get_stylesheet();
        }

        return $name;
    }

    private function registerTemplateNamespaces(): void
    {
        $name = $this->getTemplateNamespace();

        // Register admin templates
        $parentThemeDirectory = $this->getPathForFile('admin_templates/', ThemeSearchMode::PARENT_ONLY);
        $currentThemeDirectory = $this->getPathForFile('admin_templates/', ThemeSearchMode::CURRENT_ONLY);

        $adminPaths = [];
        if (false !== $parentThemeDirectory) {
            $adminPaths[] = $parentThemeDirectory;
        }
        if (false !== $currentThemeDirectory) {
            $adminPaths[] = $currentThemeDirectory;
        }

        // Register admin templates
        $parentThemeDirectory = $this->getPathForFile('templates/', ThemeSearchMode::PARENT_ONLY);
        $currentThemeDirectory = $this->getPathForFile('templates/', ThemeSearchMode::CURRENT_ONLY);

        $paths = [];
        if (false !== $parentThemeDirectory) {
            $paths[] = $parentThemeDirectory;
        }
        if (false !== $currentThemeDirectory) {
            $paths[] = $currentThemeDirectory;
        }

        /** @var TemplateRenderer $renderer */
        $renderer = $this->getContainer()->get('template.renderer');
        $renderer->registerNamespace($name . '-admin', $adminPaths, true);
        $renderer->registerNamespace($name, $paths, true);

        /**
         * Register XLib2 templates
         */
        $renderer->registerNamespace('wpapp', [realpath(__DIR__ . '/../templates')]);
    }

    final public static function getInstance(): static
    {
        if (is_null(self::$app)) {
            throw new LogicException('Application is not booted yet!');
        }

        return self::$app;
    }

    public function configureContainer(ContainerBuilder $builder, ContainerConfigurator $configurator): void
    {
        parent::configureContainer($builder, $configurator);

        $configurator->import(trailingslashit(__DIR__) . '../config/services.yaml');

        $this->registerControllerLoader($builder);
    }

    private function registerControllerLoader(ContainerBuilder $builder): void
    {
        $builder->addCompilerPass(new ControllerLoaderCompilerPass());

        $builder->registerAttributeForAutoconfiguration(
            TemplateRoute::class,
            static function (
                ChildDefinition $definition,
                TemplateRoute $attribute
            ) use ($builder): void {
                $definition->addTag(
                    'controller.route',
                    [
                        'template' => $attribute->template,
                        'description' => $attribute->description ?: $attribute->template,
                        'isCustom' => $attribute->showInCustom
                    ]
                );
            }
        );
    }

    public function getStage(): string|false
    {
        return defined('APPLICATION_ENV') ? APPLICATION_ENV : false;
    }

    public function get(string $service): ?object
    {
        return $this->getContainer()->get($service);
    }
}
