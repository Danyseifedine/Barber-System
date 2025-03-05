<?php

namespace App\Traits\Commands;


/**
 * @code
 * $jsContent = <<<JS
 * // Your JS content here
 *
 * JS;
 *
 * if ($this->updateJsFile('app-config.js', $jsContent, 'global/config')) {
 *     $this->info('App config JS file has been updated successfully!');
 * } else {
 *     $this->error('Failed to update app config JS file!');
 * }
 */
trait JsFileHandler
{
    /**
     * Update or create a JS file
     *
     * @param string $filename The name of the JS file
     * @param string $content The JS content to write
     * @param string $path Path relative to public directory
     * @return bool
     */
    protected function updateJsFile(string $filename, string $content, string $path = 'global'): bool
    {
        $fullPath = $this->getJsPath($filename, $path);

        // Create directory if it doesn't exist
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        return (bool) file_put_contents($fullPath, $content);
    }

    /**
     * Get the full path for a JS file
     *
     * @param string $filename The name of the JS file
     * @param string $path Path relative to public directory
     * @return string
     */
    protected function getJsPath(string $filename, string $path = 'global'): string
    {
        return public_path(trim($path, '/') . '/' . $filename);
    }

    /**
     * Read a JS file content
     *
     * @param string $filename The name of the JS file
     * @param string $path Path relative to public directory
     * @return string|null
     */
    protected function readJsFile(string $filename, string $path = 'global'): ?string
    {
        $fullPath = $this->getJsPath($filename, $path);

        return file_exists($fullPath) ? file_get_contents($fullPath) : null;
    }
}
