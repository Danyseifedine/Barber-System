<?php

namespace App\Traits\Commands;

trait CssFileHandler
{
    /**
     * Update or create a CSS file
     *
     * @param string $filename The name of the CSS file (with or without .css suffix)
     * @param string $content The CSS content to write
     * @param string $path Path relative to public/css directory
     * @return bool
     */
    protected function updateCssFile(string $filename, string $content, string $path = 'css'): bool
    {
        $fullPath = $this->getCssPath($filename, $path);

        // Create directory if it doesn't exist
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        return (bool) file_put_contents($fullPath, $content);
    }

    /**
     * Get the full path for a CSS file
     *
     * @param string $filename The name of the CSS file
     * @param string $path Path relative to public directory
     * @return string
     */
    protected function getCssPath(string $filename, string $path = 'css'): string
    {
        // Ensure filename ends with .css
        if (!str_ends_with($filename, '.css')) {
            $filename .= '.css';
        }

        return public_path(trim($path, '/') . '/' . $filename);
    }

    /**
     * Read a CSS file content
     *
     * @param string $filename The name of the CSS file
     * @param string $path Path relative to public directory
     * @return string|null
     */
    protected function readCssFile(string $filename, string $path = 'css'): ?string
    {
        $fullPath = $this->getCssPath($filename, $path);

        return file_exists($fullPath) ? file_get_contents($fullPath) : null;
    }

    /**
     * Delete a CSS directory and all its contents
     *
     * @param string $path Path relative to public/css directory
     * @return bool
     */
    protected function deleteCssPath(string $path): bool
    {
        $fullPath = public_path('css/' . trim($path, '/'));

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
     * Minify CSS content
     *
     * @param string $content The CSS content to minify
     * @return string
     */
    protected function minifyCss(string $content): string
    {
        // Remove comments
        $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);

        // Remove space after colons
        $content = str_replace(': ', ':', $content);

        // Remove whitespace
        $content = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $content);

        return trim($content);
    }

    /**
     * Add CSS vendor prefixes
     *
     * @param string $property CSS property
     * @param string $value CSS value
     * @return string
     */
    protected function addVendorPrefixes(string $property, string $value): string
    {
        $prefixes = ['-webkit-', '-moz-', '-ms-', '-o-', ''];
        $result = '';

        foreach ($prefixes as $prefix) {
            $result .= $prefix . $property . ': ' . $value . ";\n";
        }

        return $result;
    }
}
