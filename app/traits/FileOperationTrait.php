<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Trait FileOperationTrait
 *
 * This trait provides methods for creating various types of files in a Laravel application.
 */
trait FileOperationTrait
{
    /**
     * Create a file with the given configuration.
     *
     * @param array $config {
     *     @var string $name The name of the file (required)
     *     @var string $path The path where the file should be created
     *     @var callable|string $content The content of the file (required)
     *     @var bool $overwrite Whether to overwrite existing files (default: false)
     *     @var string|null $namespace The namespace for the file
     *     @var array $traits An array of traits to be used in the file
     *     @var string $fullPath The full path where the file should be created
     * }
     * @return bool True if the file was created successfully, false otherwise
     * @throws InvalidArgumentException If the configuration is invalid
     */
    protected function createOrUpdateFile(array $config): bool
    {
        $config = $this->normalizeConfig($config);
        $this->validateConfig($config);

        $fullPath = $config['fullPath'];

        if (!$this->canCreateOrUpdateFile($config)) {
            return false;
        }

        $content = $this->generateFileContent($config);

        File::put($fullPath, $content);
        return true;
    }

    /**
     * Validate the configuration array.
     *
     * @param array $config The configuration array
     * @throws InvalidArgumentException If the configuration is invalid
     */
    protected function validateConfig(array $config): void
    {
        if (empty($config['name'])) {
            throw new InvalidArgumentException('File name is required.');
        }

        if (!is_callable($config['content']) && !is_string($config['content'])) {
            throw new InvalidArgumentException('Content must be a callable or a string.');
        }
    }

    /**
     * Check if the file can be created or updated.
     *
     * @param array $config The configuration array
     * @return bool True if the file can be created or updated, false otherwise
     */
    protected function canCreateOrUpdateFile(array $config): bool
    {
        if (File::exists($config['fullPath']) && !$config['overwrite']) {
            return false;
        }
        File::ensureDirectoryExists(dirname($config['fullPath']));
        return true;
    }

    /**
     * Generate the content for the file.
     *
     * @param array $config The configuration array
     * @return string The generated content
     */
    protected function generateFileContent(array $config): string
    {
        $namespace = $this->resolveNamespace($config);
        $name = $this->getClassName($config['fullPath']);

        $content = is_callable($config['content'])
            ? $config['content']($config['fullPath'], $namespace, $name)
            : $config['content'];

        $content = $this->addNamespaceToContent($content, $namespace);
        return $this->addTraitsToContent($content, $config['traits']);
    }

    /**
     * Resolve the namespace for the file.
     *
     * @param array $config The configuration array
     * @return string|false The resolved namespace or false if not applicable
     */
    protected function resolveNamespace(array $config)
    {
        return $config['namespace'] ?? $this->getNamespaceFromPath($config['fullPath']);
    }

    /**
     * Get the class name from the file path.
     *
     * @param string $path The file path
     * @return string The class name
     */
    protected function getClassName(string $path): string
    {
        return basename($path, '.' . pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * Create or update a view file.
     *
     * @param array $config The configuration array
     * @return bool True if the view was created or updated successfully, false otherwise
     */
    protected function createOrUpdateView(array $config): bool
    {
        $config = $this->normalizeConfig($config);
        $config['fullPath'] = resource_path("views/{$config['path']}/{$config['name']}.blade.php");
        $config['namespace'] = false;
        return $this->createOrUpdateFile($config);
    }

    /**
     * Create or update a controller file.
     *
     * @param array $config The configuration array
     * @return bool True if the controller was created or updated successfully, false otherwise
     */
    protected function createOrUpdateController(array $config): bool
    {
        $config['name'] = Str::studly($config['name']) . 'Controller';
        return $this->createOrUpdateFileWithConfig($config, 'Http/Controllers');
    }

    /**
     * Create or update a JavaScript file.
     *
     * @param array $config The configuration array
     * @return bool True if the JavaScript file was created or updated successfully, false otherwise
     */
    protected function createOrUpdateJavaScript(array $config): bool
    {
        return $this->createOrUpdateFileWithConfig($config, 'js', '.js', 'public_path');
    }

    /**
     * Create or update a CSS file.
     *
     * @param array $config The configuration array
     * @return bool True if the CSS file was created or updated successfully, false otherwise
     */
    protected function createOrUpdateCss(array $config): bool
    {
        return $this->createOrUpdateFileWithConfig($config, 'css', '.css', 'public_path');
    }

    /**
     * Create or update a migration file.
     *
     * @param array $config The configuration array
     * @return bool True if the migration was created or updated successfully, false otherwise
     */
    protected function createOrUpdateMigration(array $config): bool
    {
        $config['name'] = date('Y_m_d_His') . "_{$config['name']}";
        $config['namespace'] = false;
        return $this->createOrUpdateFileWithConfig($config, 'migrations', '.php', 'database_path');
    }

    /**
     * Create or update a model file.
     *
     * @param array $config The configuration array
     * @return bool True if the model was created or updated successfully, false otherwise
     */
    protected function createOrUpdateModel(array $config): bool
    {
        $config['name'] = Str::studly(Str::singular($config['name']));
        return $this->createOrUpdateFileWithConfig($config, 'Models');
    }

    /**
     * Create or update a middleware file.
     *
     * @param array $config The configuration array
     * @return bool True if the middleware was created or updated successfully, false otherwise
     */
    protected function createOrUpdateMiddleware(array $config): bool
    {
        return $this->createOrUpdateFileWithConfig($config, 'Http/Middleware');
    }

    /**
     * Create or update a job file.
     *
     * @param array $config The configuration array
     * @return bool True if the job was created or updated successfully, false otherwise
     */
    protected function createOrUpdateJob(array $config): bool
    {
        return $this->createOrUpdateFileWithConfig($config, 'Jobs');
    }

    /**
     * Create or update a schedule file.
     *
     * @param array $config The configuration array
     * @return bool True if the schedule was created or updated successfully, false otherwise
     */
    protected function createOrUpdateSchedule(array $config): bool
    {
        return $this->createOrUpdateFileWithConfig($config, 'Console');
    }

    /**
     * Create or update a service file.
     *
     * @param array $config The configuration array
     * @return bool True if the service was created or updated successfully, false otherwise
     */
    protected function createOrUpdateService(array $config): bool
    {
        return $this->createOrUpdateFileWithConfig($config, 'Services');
    }

    /**
     * Create or update a request file.
     *
     * @param array $config The configuration array
     * @return bool True if the request was created or updated successfully, false otherwise
     */
    protected function createOrUpdateRequest(array $config): bool
    {
        return $this->createOrUpdateFileWithConfig($config, 'Http/Requests');
    }

    /**
     * Create or update a file with the given configuration.
     *
     * @param array $config The configuration array
     * @param string $baseDir The base directory for the file
     * @param string $extension The file extension
     * @param string $pathFunction The function to use for generating the path
     * @return bool True if the file was created or updated successfully, false otherwise
     */
    protected function createOrUpdateFileWithConfig(array $config, string $baseDir, string $extension = '.php', string $pathFunction = 'app_path'): bool
    {
        $config = $this->normalizeConfig($config);
        $config['fullPath'] = $pathFunction("{$baseDir}/{$config['path']}/{$config['name']}{$extension}");
        return $this->createOrUpdateFile($config);
    }

    /**
     * Get the namespace from the file path.
     *
     * @param string $path The file path
     * @return string|false The namespace or false if not applicable
     */
    protected function getNamespaceFromPath($path)
    {
        if (Str::contains($path, 'database/migrations')) {
            return false;
        }

        $appPath = str_replace('\\', '/', app_path());
        $relativePath = str_replace($appPath, '', str_replace('\\', '/', $path));
        $parts = explode('/', trim($relativePath, '/'));
        array_pop($parts);

        return empty($parts) ? 'App' : $this->buildNamespace($parts);
    }

    /**
     * Build a namespace from an array of parts.
     *
     * @param array $parts The parts of the namespace
     * @return string The built namespace
     */
    protected function buildNamespace(array $parts): string
    {
        return rtrim('App\\' . implode('\\', array_map('ucfirst', $parts)), '\\');
    }

    /**
     * Normalize the configuration array.
     *
     * @param array $config The configuration array
     * @return array The normalized configuration array
     */
    private function normalizeConfig(array $config): array
    {
        return array_merge([
            'name' => '',
            'path' => '',
            'content' => fn() => '',
            'overwrite' => false,
            'namespace' => null,
            'traits' => [],
        ], $config);
    }

    /**
     * Add namespace to the content if necessary.
     *
     * @param string $content The content
     * @param string|false $namespace The namespace
     * @return string The content with namespace added if necessary
     */
    protected function addNamespaceToContent($content, $namespace): string
    {
        if ($namespace !== false && !Str::contains($content, 'namespace')) {
            return "<?php\n\nnamespace {$namespace};\n\n" . ltrim($content);
        } elseif ($namespace === false && !Str::contains($content, '<?php')) {
            return "<?php\n\n" . ltrim($content);
        }
        return $content;
    }

    /**
     * Add traits to the content if necessary.
     *
     * @param string $content The content
     * @param array $traits The traits to add
     * @return string The content with traits added if necessary
     */
    protected function addTraitsToContent($content, array $traits): string
    {
        if (empty($traits)) {
            return $content;
        }

        $useStatements = 'use ' . implode(', ', $traits) . ';';
        $classPosition = strpos($content, 'class ');

        return $classPosition !== false
            ? substr_replace($content, "{$useStatements}\n\n", $classPosition, 0)
            : $content;
    }
}
