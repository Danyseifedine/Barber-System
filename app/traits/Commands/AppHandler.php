<?php

namespace App\Traits\Commands;

trait AppHandler
{
    /**
     * Update or create a file in the app directory
     *
     * @param string $filename The name of the file
     * @param string $content The file content to write
     * @param string $path Path relative to app directory
     * @return bool
     */
    protected function updateAppFile(string $filename, string $content, string $path = ''): bool
    {
        $fullPath = $this->getAppPath($filename, $path);

        // Create directory if it doesn't exist
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        return (bool) file_put_contents($fullPath, $content);
    }

    /**
     * Get the full path for a file in the app directory
     *
     * @param string $filename The name of the file
     * @param string $path Path relative to app directory
     * @return string
     */
    protected function getAppPath(string $filename, string $path = ''): string
    {
        return app_path(trim($path, '/') . '/' . $filename);
    }

    /**
     * Read a file content from the app directory
     *
     * @param string $filename The name of the file
     * @param string $path Path relative to app directory
     * @return string|null
     */
    protected function readAppFile(string $filename, string $path = ''): ?string
    {
        $fullPath = $this->getAppPath($filename, $path);

        return file_exists($fullPath) ? file_get_contents($fullPath) : null;
    }

    /**
     * Delete a directory and all its contents from the app directory
     *
     * @param string $path Path relative to app directory
     * @return bool
     */
    protected function deleteAppPath(string $path): bool
    {
        $fullPath = app_path(trim($path, '/'));

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
