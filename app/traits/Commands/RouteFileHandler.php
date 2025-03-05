<?php

namespace App\Traits\Commands;

trait RouteFileHandler
{
    /**
     * Update or create a Route file
     *
     * @param string $filename The name of the Route file (with or without .php suffix)
     * @param string $content The Route content to write
     * @param string $path Path relative to routes directory
     * @return bool
     */
    protected function updateRouteFile(string $filename, string $content, string $path = ''): bool
    {
        $fullPath = $this->getRoutePath($filename, $path);

        // Create directory if it doesn't exist
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        return (bool) file_put_contents($fullPath, $content);
    }

    /**
     * Get the full path for a Route file
     *
     * @param string $filename The name of the Route file
     * @param string $path Path relative to routes directory
     * @return string
     */
    protected function getRoutePath(string $filename, string $path = ''): string
    {
        // Ensure filename ends with .php
        if (!str_ends_with($filename, '.php')) {
            $filename .= '.php';
        }

        return base_path('routes/' . trim($path, '/') . '/' . $filename);
    }

    /**
     * Read a Route file content
     *
     * @param string $filename The name of the Route file
     * @param string $path Path relative to routes directory
     * @return string|null
     */
    protected function readRouteFile(string $filename, string $path = ''): ?string
    {
        $fullPath = $this->getRoutePath($filename, $path);

        return file_exists($fullPath) ? file_get_contents($fullPath) : null;
    }

    /**
     * Delete a routes directory and all its contents
     *
     * @param string $path Path relative to routes directory
     * @return bool
     */
    protected function deleteRoutePath(string $path): bool
    {
        $fullPath = base_path('routes/' . trim($path, '/'));

        if (!file_exists($fullPath)) {
            return true; // Return true if directory doesn't exist
        }

        // Recursively delete directory contents
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($fullPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                rmdir($item->getRealPath());
            } else {
                unlink($item->getRealPath());
            }
        }

        return rmdir($fullPath);
    }

    /**
     * Get standard route file header with namespace and use statements
     *
     * @return string
     */
    protected function getRouteFileHeader(): string
    {
        return <<<'PHP'
<?php

use Illuminate\Support\Facades\Route;

PHP;
    }

    /**
     * Add route group with middleware and prefix
     *
     * @param string $prefix Route prefix
     * @param array $middleware Array of middleware names
     * @param string $routes The routes to be wrapped
     * @return string
     */
    protected function wrapInRouteGroup(string $prefix, array $middleware, string $routes): string
    {
        $middlewareStr = implode("','", $middleware);
        return <<<PHP
Route::middleware(['$middlewareStr'])
    ->prefix('$prefix')
    ->group(function () {
    $routes
});
PHP;
    }
}
