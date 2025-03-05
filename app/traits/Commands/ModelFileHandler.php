<?php

namespace App\Traits\Commands;

trait ModelFileHandler
{
    /**
     * Update or create a Model file
     *
     * @param string $filename The name of the Model (with or without .php suffix)
     * @param string $content The Model content to write
     * @param string $path Path relative to app/Models directory
     * @return bool
     */
    protected function updateModelFile(string $filename, string $content, string $path = ''): bool
    {
        $fullPath = $this->getModelPath($filename, $path);

        // Create directory if it doesn't exist
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        return (bool) file_put_contents($fullPath, $content);
    }

    /**
     * Get the full path for a Model file
     *
     * @param string $filename The name of the Model
     * @param string $path Path relative to app/Models directory
     * @return string
     */
    protected function getModelPath(string $filename, string $path = ''): string
    {
        // Ensure filename ends with .php and starts with uppercase
        if (!str_ends_with($filename, '.php')) {
            $filename .= '.php';
        }

        // Ensure first character is uppercase
        $filename = ucfirst($filename);

        return app_path('Models/' . trim($path, '/') . '/' . $filename);
    }

    /**
     * Read a Model file content
     *
     * @param string $filename The name of the Model
     * @param string $path Path relative to app/Models directory
     * @return string|null
     */
    protected function readModelFile(string $filename, string $path = ''): ?string
    {
        $fullPath = $this->getModelPath($filename, $path);

        return file_exists($fullPath) ? file_get_contents($fullPath) : null;
    }

    /**
     * Delete a models directory and all its contents
     *
     * @param string $path Path relative to app/Models directory
     * @return bool
     */
    protected function deleteModelPath(string $path): bool
    {
        $fullPath = app_path('Models/' . trim($path, '/'));

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
