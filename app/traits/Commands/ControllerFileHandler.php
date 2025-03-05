<?php

namespace App\Traits\Commands;

trait ControllerFileHandler
{
    /**
     * Update or create a Controller file
     *
     * @param string $filename The name of the Controller (with or without Controller.php suffix)
     * @param string $content The Controller content to write
     * @param string $path Path relative to app/Http/Controllers directory
     * @return bool
     */
    protected function updateControllerFile(string $filename, string $content, string $path = ''): bool
    {
        $fullPath = $this->getControllerPath($filename, $path);

        // Create directory if it doesn't exist
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        return (bool) file_put_contents($fullPath, $content);
    }

    /**
     * Get the full path for a Controller file
     *
     * @param string $filename The name of the Controller
     * @param string $path Path relative to app/Http/Controllers directory
     * @return string
     */
    protected function getControllerPath(string $filename, string $path = ''): string
    {
        // Ensure filename ends with Controller.php
        if (!str_ends_with($filename, 'Controller.php')) {
            $filename = str_ends_with($filename, 'Controller')
                ? $filename . '.php'
                : $filename . 'Controller.php';
        }

        return app_path('Http/Controllers/' . trim($path, '/') . '/' . $filename);
    }

    /**
     * Read a Controller file content
     *
     * @param string $filename The name of the Controller
     * @param string $path Path relative to app/Http/Controllers directory
     * @return string|null
     */
    protected function readControllerFile(string $filename, string $path = ''): ?string
    {
        $fullPath = $this->getControllerPath($filename, $path);

        return file_exists($fullPath) ? file_get_contents($fullPath) : null;
    }

    /**
     * Delete a controllers directory and all its contents
     *
     * @param string $path Path relative to app/Http/Controllers directory
     * @return bool
     */
    protected function deleteControllerPath(string $path): bool
    {
        $fullPath = app_path('Http/Controllers/' . trim($path, '/'));

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
}
