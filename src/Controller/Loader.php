<?php

namespace XLib2\Controller;

use VAF\WP\Framework\Hook\Attribute\AsHookContainer;
use VAF\WP\Framework\Hook\Attribute\Hook;
use XLib2\Application;

#[AsHookContainer]
final class Loader
{
    public function __construct(
        private readonly Application $app,
        private readonly array $routes,
        private readonly array $customRoutes
    ) {
    }

    #[Hook('theme_templates')]
    public function initCustomRoutes(array $templates): array
    {
        foreach ($this->customRoutes as $route => $description) {
            $templates[$route] = $description;
        }

        return $templates;
    }

    #[Hook('404_template')]
    #[Hook('archive_template')]
    #[Hook('attachment_template')]
    #[Hook('author_template')]
    #[Hook('category_template')]
    #[Hook('date_template')]
    #[Hook('embed_template')]
    #[Hook('frontpage_template')]
    #[Hook('home_template')]
    #[Hook('index_template')]
    #[Hook('page_template')]
    #[Hook('paged_template')]
    #[Hook('privacypolicy_template')]
    #[Hook('search_template')]
    #[Hook('single_template')]
    #[Hook('singular_template')]
    #[Hook('tag_template')]
    #[Hook('taxonomy_template')]
    public function getTemplate(string $template, string $type, array $templates): string
    {
        $layoutService = false;

        foreach ($templates as $templateFile) {
            // Remove .php from template
            if (str_ends_with($templateFile, '.php') && !isset($this->routes[$templateFile])) {
                $templateFile = substr($templateFile, 0, -4);
            }
            if (isset($this->routes[$templateFile])) {
                $layoutService = $this->routes[$templateFile];
                break;
            }
        }

        if (!empty($layoutService)) {
            return 'xlib2:' . $layoutService;
        }

        return $template;
    }

    #[Hook('template_include')]
    public function renderLayout(string $template): string
    {
        if (!str_starts_with($template, 'xlib2:')) {
            // Not an XLib2 layout
            return $template;
        }

        $serviceClass = substr($template, 6);
        if (!$this->app->getContainer()->has($serviceClass)) {
            return $template;
        }

        /** @var AbstractController $controller */
        $controller = $this->app->getContainer()->get($serviceClass);
        $response = $controller->execute();
        $response->handle();
        exit;
    }
}