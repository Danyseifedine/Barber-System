<?php

namespace App\Traits\Commands;

trait ViewFileHandler
{
    /**
     * Update or create a View file
     *
     * @param string $filename The name of the View file (with or without .blade.php extension)
     * @param string $content The View content to write
     * @param string $path Path relative to resources/views directory
     * @return bool
     */
    protected function updateViewFile(string $filename, string $content, string $path = ''): bool
    {
        $fullPath = $this->getViewPath($filename, $path);

        // Create directory if it doesn't exist
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        return (bool) file_put_contents($fullPath, $content);
    }

    /**
     * Get the full path for a View file
     *
     * @param string $filename The name of the View file
     * @param string $path Path relative to resources/views directory
     * @return string
     */
    protected function getViewPath(string $filename, string $path = ''): string
    {
        // Ensure filename ends with .blade.php
        if (!str_ends_with($filename, '.blade.php')) {
            $filename = $filename . '.blade.php';
        }

        return resource_path('views/' . trim($path, '/') . '/' . $filename);
    }

    /**
     * Read a View file content
     *
     * @param string $filename The name of the View file
     * @param string $path Path relative to resources/views directory
     * @return string|null
     */
    protected function readViewFile(string $filename, string $path = ''): ?string
    {
        $fullPath = $this->getViewPath($filename, $path);

        return file_exists($fullPath) ? file_get_contents($fullPath) : null;
    }

    /**
     * Delete a view directory and all its contents
     *
     * @param string $path Path relative to resources/views directory
     * @return bool
     */
    protected function deleteViewPath(string $path): bool
    {
        $fullPath = resource_path('views/' . trim($path, '/'));

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
