<?php

namespace App\Console\Commands\Setup\Common\Datatable;

use App\Traits\Commands\ControllerFileHandler;
use App\Traits\Commands\JsFileHandler;
use App\Traits\Commands\ViewFileHandler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DatatableGenerator extends Command
{

    use ControllerFileHandler, JsFileHandler, ViewFileHandler;

    private $path = "Dashboard/Pages";
    private $validationRules = [];
    private $modelName;
    private $controllerName;
    private $modelFirstLower;
    private $selectedColumns = [];
    private $routePrefix;
    private $jsPath = "js/dashboard/pages";
    private $jsFile;
    private $datatableId;
    private $generatedFiles = [];

    public function __construct()
    {
        parent::__construct();
        $this->modelName = '';
        $this->controllerName = '';
        $this->modelFirstLower = '';
        $this->routePrefix = '';
    }

    protected $signature = 'generate:datatable {--rollback : Remove all generated files for the specified model}';
    protected $description = 'Generate a new Datatable Controller Model';

    private $commonValidationRules = [
        1 => 'required',
        2 => 'string',
        3 => 'integer',
        4 => 'email',
        5 => 'date',
        6 => 'boolean',
        7 => 'unique',
        8 => 'min',
        9 => 'max',
        10 => 'nullable',
        11 => 'numeric',
        12 => 'alpha',
        13 => 'alpha_num',
        14 => 'alpha_dash',
        15 => 'url',
        16 => 'ip',
        17 => 'json',
        18 => 'array',
        19 => 'timezone',
        20 => 'regex',
        21 => 'confirmed',
        22 => 'size',
        23 => 'between',
        24 => 'digits',
        25 => 'digits_between'
    ];

    private function drawLogo()
    {
        $this->line("\n");
        $this->info('╔═══════════════════════════════════════════╗');
        $this->info('║           DATATABLE GENERATOR             ║');
        $this->info('╚═══════════════════════════════════════════╝');
        $this->line("\n");
    }

    private function showSpinner($message)
    {
        $spinner = ['-', '\\', '|', '/'];
        foreach (range(0, 15) as $i) {
            echo "\r" . $message . ' ' . $spinner[$i % 4];
            usleep(100000);
        }
        echo "\r\033[K";
    }

    private function typeWrite($message)
    {
        $chars = str_split($message);
        foreach ($chars as $char) {
            echo $char;
            usleep(50000);
        }
        echo "\n";
    }

    private function handleValidationRule($rule)
    {
        switch ($rule) {
            case 'between':
            case 'digits_between':
                $this->info("\n📏 Setting up {$rule} validation:");
                $options = [
                    1 => 'Enter custom values',
                    2 => 'Choose from common ranges'
                ];

                $this->table(['Option', 'Description'], collect($options)->map(fn($desc, $key) => [$key, $desc])->toArray());

                $choice = $this->choice('How would you like to set the range?', array_values($options), 0);

                if ($choice === 'Enter custom values') {
                    $min = $this->ask('Enter minimum value');
                    $max = $this->ask('Enter maximum value');
                } else {
                    $ranges = [
                        1 => ['min' => 1, 'max' => 10],
                        2 => ['min' => 1, 'max' => 100],
                        3 => ['min' => 0, 'max' => 1000],
                    ];

                    $this->table(
                        ['Option', 'Range'],
                        collect($ranges)->map(fn($range, $key) => [$key, "{$range['min']} - {$range['max']}"])->toArray()
                    );

                    $rangeChoice = $this->choice('Select a range:', array_map(fn($range) => "{$range['min']} - {$range['max']}", $ranges));
                    list($min, $max) = explode(' - ', $rangeChoice);
                }
                return "{$rule}:{$min},{$max}";

            case 'min':
            case 'max':
            case 'size':
            case 'digits':
                $this->info("\n📏 Setting up {$rule} validation:");
                $options = [
                    1 => 'Enter custom value',
                    2 => 'Choose from common values'
                ];

                $this->table(['Option', 'Description'], collect($options)->map(fn($desc, $key) => [$key, $desc])->toArray());

                $choice = $this->choice('How would you like to set the value?', array_values($options), 0);

                if ($choice === 'Enter custom value') {
                    $value = $this->ask("Enter the value for {$rule}");
                } else {
                    $commonValues = [
                        1 => 1,
                        2 => 5,
                        3 => 10,
                        4 => 50,
                        5 => 100,
                        6 => 255,
                        7 => 1000
                    ];

                    $this->table(
                        ['Option', 'Value'],
                        collect($commonValues)->map(fn($val, $key) => [$key, $val])->toArray()
                    );

                    $value = $this->choice('Select a value:', array_values($commonValues));
                }
                return "{$rule}:{$value}";

            case 'unique':
                $tableName = $this->ask("Enter table name for unique validation");
                return "unique:{$tableName}";

            case 'regex':
                $pattern = $this->ask("Enter regex pattern");
                return "regex:/{$pattern}/";

            default:
                return $rule;
        }
    }

    private function handleValidationSetup()
    {
        if ($this->confirm('🔍 Would you like to set up validation rules?', true)) {
            $this->info("\n📝 Let's set up validation rules for your columns!");

            foreach ($this->selectedColumns as $column) {
                if ($this->confirm("\nAdd validation rules for '{$column}'?", true)) {
                    $this->info("\n📋 Available validation rules:");
                    $this->table(
                        ['Number', 'Rule', 'Description'],
                        [
                            ['1', 'required', 'Field cannot be empty'],
                            ['2', 'string', 'Must be a string'],
                            ['3', 'integer', 'Must be an integer'],
                            ['4', 'email', 'Must be a valid email'],
                            ['5', 'date', 'Must be a valid date'],
                            ['6', 'boolean', 'Must be true or false'],
                            ['7', 'unique', 'Must be unique in the database'],
                            ['8', 'min', 'Minimum value/length'],
                            ['9', 'max', 'Maximum value/length'],
                            ['10', 'nullable', 'Field can be null'],
                            ['11', 'numeric', 'Must be numeric'],
                            ['12', 'url', 'Must be a valid URL'],
                            ['13', 'array', 'Must be an array'],
                            ['14', 'in', 'Must be one of specified values'],
                            ['15', 'regex', 'Must match pattern'],
                        ]
                    );

                    $selectedRules = [];
                    while (true) {
                        $ruleNumber = $this->anticipate(
                            'Enter rule number (or "done" to finish this column)',
                            ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'done']
                        );

                        if (strtolower($ruleNumber) === 'done') {
                            break;
                        }

                        switch ($ruleNumber) {
                            case '1': // required
                                $selectedRules[] = 'required';
                                $this->info('✅ Added: required');
                                break;

                            case '2': // string
                                $selectedRules[] = 'string';
                                $this->info('✅ Added: string');
                                break;

                            case '3': // integer
                                $selectedRules[] = 'integer';
                                $this->info('✅ Added: integer');
                                break;

                            case '4': // email
                                $selectedRules[] = 'email';
                                $this->info('✅ Added: email');
                                break;

                            case '5': // date
                                $selectedRules[] = 'date';
                                $this->info('✅ Added: date');
                                break;

                            case '6': // boolean
                                $selectedRules[] = 'boolean';
                                $this->info('✅ Added: boolean');
                                break;

                            case '7': // unique
                                $tableName = $this->ask("Enter table name for unique validation", strtolower($this->modelName) . 's');
                                $selectedRules[] = "unique:{$tableName},{$column}";
                                $this->info("✅ Added: unique:{$tableName},{$column}");
                                break;

                            case '8': // min
                                $min = $this->ask('Enter minimum value');
                                $selectedRules[] = "min:{$min}";
                                $this->info("✅ Added: min:{$min}");
                                break;

                            case '9': // max
                                $max = $this->ask('Enter maximum value');
                                $selectedRules[] = "max:{$max}";
                                $this->info("✅ Added: max:{$max}");
                                break;

                            case '10': // nullable
                                $selectedRules[] = 'nullable';
                                $this->info('✅ Added: nullable');
                                break;

                            case '11': // numeric
                                $selectedRules[] = 'numeric';
                                $this->info('✅ Added: numeric');
                                break;

                            case '12': // url
                                $selectedRules[] = 'url';
                                $this->info('✅ Added: url');
                                break;

                            case '13': // array
                                $selectedRules[] = 'array';
                                $this->info('✅ Added: array');
                                break;

                            case '14': // in
                                $values = $this->ask('Enter allowed values (comma-separated)');
                                $selectedRules[] = "in:" . str_replace(' ', '', $values);
                                $this->info("✅ Added: in:{$values}");
                                break;

                            case '15': // regex
                                $pattern = $this->ask('Enter regex pattern');
                                $selectedRules[] = "regex:/{$pattern}/";
                                $this->info("✅ Added: regex:/{$pattern}/");
                                break;

                            default:
                                $this->error("❌ Invalid rule number!");
                                continue 2;
                        }
                    }

                    if (!empty($selectedRules)) {
                        $this->validationRules[$column] = implode('|', $selectedRules);
                        $this->info("\n✅ Validation rules for '{$column}' set to: " . $this->validationRules[$column]);
                    }
                }
            }

            // Show summary of all validation rules
            if (!empty($this->validationRules)) {
                $this->info("\n📋 Summary of validation rules:");
                $this->table(
                    ['Column', 'Rules'],
                    collect($this->validationRules)->map(function ($rules, $column) {
                        return [$column, $rules];
                    })->toArray()
                );
            }
        }
    }

    private function handleColumnSetup()
    {
        $this->info("\n📊 Let's set up the columns for your datatable!");

        while (true) {
            $column = $this->ask('Enter column name (or type "done" to finish)');

            if (strtolower($column) === 'done') {
                break;
            }

            $this->selectedColumns[] = $column;
            $this->info("✅ Added column: $column");
        }

        if (empty($this->selectedColumns)) {
            $this->selectedColumns = ['id', 'name', 'email', 'created_at'];
            $this->info("Using default columns: " . implode(', ', $this->selectedColumns));
        }
    }

    private function handleSearchableColumns()
    {
        $this->info("\n🔍 Which columns should be searchable?");
        $searchableColumns = [];

        foreach ($this->selectedColumns as $column) {
            if ($this->confirm("Make '$column' searchable?", true)) {
                $searchableColumns[] = $column;
            }
        }

        return $searchableColumns;
    }

    private function addRoutesToDashboard()
    {
        try {
            $dashboardFile = base_path('routes/dashboard.php');
            $content = File::get($dashboardFile);

            // Check if the controller import already exists
            $importStatement = "use App\Http\Controllers\Dashboard\Pages\\{$this->controllerName};";
            if (!str_contains($content, $importStatement)) {
                // Find the correct position for import
                $position = strpos($content, '// Datatable Controllers');
                if ($position === false) {
                    // If marker not found, add it after the last use statement
                    $lastUsePos = strrpos($content, 'use ');
                    $endOfLine = strpos($content, ';', $lastUsePos) + 1;
                    $content = substr_replace($content, "\n// Datatable Controllers\n" . $importStatement . "\n", $endOfLine, 0);
                } else {
                    $content = substr_replace($content, $importStatement . "\n", $position + strlen('// Datatable Controllers') + 1, 0);
                }
            }

            // Add routes if they don't exist
            if (!str_contains($content, "Route::resource('{$this->routePrefix}'")) {
                $routeContent = "\n    // ======================================================================= //\n";
                $routeContent .= "    // ====================== START " . strtoupper($this->modelName) . " DATATABLE =========================== //\n";
                $routeContent .= "    // ======================================================================= //\n\n";
                $routeContent .= "    Route::controller({$this->controllerName}::class)\n";
                $routeContent .= "        ->prefix('{$this->routePrefix}')\n";
                $routeContent .= "        ->name('{$this->routePrefix}.')\n";
                $routeContent .= "        ->group(function () {\n";
                $routeContent .= "            Route::post('/update', 'update')->name('update');\n";
                $routeContent .= "            Route::get('/{id}/show', 'show')->name('show');\n";
                $routeContent .= "            Route::get('/datatable', 'datatable')->name('datatable');\n";
                $routeContent .= "    });\n\n";
                $routeContent .= "    Route::resource('{$this->routePrefix}', {$this->controllerName}::class)\n";
                $routeContent .= "        ->except(['show', 'update']);\n\n";
                $routeContent .= "    // ======================================================================= //\n";
                $routeContent .= "    // ====================== END " . strtoupper($this->modelName) . " DATATABLE =========================== //\n";
                $routeContent .= "    // ======================================================================= //\n";

                // Find position to add routes
                $position = strrpos($content, '// =======================================================================');
                if ($position === false) {
                    // If no marker found, add at the end
                    $content .= $routeContent;
                } else {
                    $content = substr_replace($content, $routeContent, $position, 0);
                }
            }

            File::put($dashboardFile, $content);
            return true;
        } catch (\Exception $e) {
            $this->error('Error updating dashboard routes: ' . $e->getMessage());
            return false;
        }
    }

    private function rollback()
    {
        $this->error("\n❌ Rolling back changes...");

        // Remove files
        foreach ($this->generatedFiles as $file) {
            if (File::exists($file)) {
                if (is_dir($file)) {
                    File::deleteDirectory($file);
                    $this->line("Removed directory: " . $file);
                } else {
                    File::delete($file);
                    $this->line("Removed file: " . $file);
                }
            }
        }

        // Remove route entries
        try {
            $dashboardFile = base_path('routes/dashboard.php');
            if (File::exists($dashboardFile)) {
                $content = File::get($dashboardFile);

                // Remove controller import
                $importStatement = "use App\Http\Controllers\Dashboard\Pages\\{$this->controllerName};";
                $content = str_replace($importStatement . "\n", '', $content);

                // Remove route block
                $routeStart = "// ====================== START " . strtoupper($this->modelName) . " DATATABLE ===========================";
                $routeEnd = "// ====================== END " . strtoupper($this->modelName) . " DATATABLE ===========================";

                $startPos = strpos($content, $routeStart);
                if ($startPos !== false) {
                    $endPos = strpos($content, $routeEnd, $startPos);
                    if ($endPos !== false) {
                        $endPos = strpos($content, "\n", $endPos) + 1;
                        $content = substr_replace($content, '', $startPos, $endPos - $startPos);
                    }
                }

                File::put($dashboardFile, $content);
                $this->line("Removed routes from dashboard.php");
            }
        } catch (\Exception $e) {
            $this->warn("Warning: Could not remove routes - " . $e->getMessage());
        }

        $this->info("✅ Rollback completed successfully");
    }

    public function handleAll()
    {
        try {
            $this->drawLogo();

            $this->typeWrite("👋 Welcome to the Datatable Generator!");
            $this->line("\n");

            // Model Input
            $this->info('📦 First, let\'s talk about your Model:');
            $this->modelName = $this->anticipate('What\'s the name of your Model?', [
                'User',
                'Product',
                'Order',
                'Customer'
            ]);
            $this->modelFirstLower = lcfirst($this->modelName);

            // Set default controller name
            $this->controllerName = $this->modelName . 'Controller';

            $this->showSpinner('Processing model information');
            $this->line("✅ Model set to: <fg=green>{$this->modelName}</>");
            $this->line("\n");

            // Route Prefix Input
            $this->info('🛣️ Let\'s set up the route prefix:');
            $this->routePrefix = $this->ask('What should be the route prefix?', strtolower($this->modelName) . 's');

            // Controller Name Input
            $this->info('🎮 Now, about the Controller:');
            $this->controllerName = $this->ask('What should we name the Controller?', $this->controllerName);

            $this->showSpinner('Setting up controller configuration');
            $this->line("✅ Controller set to: <fg=green>{$this->controllerName}</>");
            $this->line("\n");

            // JavaScript Setup
            $this->info('📜 Let\'s configure the JavaScript:');

            // Datatable ID
            $this->datatableId = $this->ask(
                'What should be the datatable ID?',
                strtolower($this->modelName) . 'Table'
            );

            // JavaScript file name
            $this->jsFile = $this->ask(
                'What should be the JavaScript file name?',
                strtolower($this->modelName)
            );
            // Ensure .js extension
            if (!str_ends_with($this->jsFile, '.js')) {
                $this->jsFile .= '.js';
            }

            // Custom JavaScript path
            $customJsPath = $this->ask(
                'Enter the JavaScript path (default: dashboard/pages/' . strtolower($this->modelName) . ')',
                'dashboard/pages/' . strtolower($this->modelName)
            );
            $this->jsPath = $customJsPath;

            $this->showSpinner('Setting up JavaScript configuration');
            $this->line("✅ JavaScript configuration complete");
            $this->line("\n");

            // Column Setup Choice
            $this->info('📊 Let\'s set up the columns for your datatable:');
            $columnChoice = $this->choice(
                'How would you like to specify the columns?',
                [
                    'auto' => 'Automatically get from model fillable',
                    'manual' => 'Manually specify columns'
                ],
                'auto'
            );

            if ($columnChoice === 'auto') {
                $this->getColumnsFromModel();

                // Show the columns and allow adding/removing
                if (!empty($this->selectedColumns)) {
                    $this->info("\n📋 Retrieved columns from model:");
                    $this->line(implode(', ', $this->selectedColumns));

                    if ($this->confirm('Would you like to modify these columns?', false)) {
                        $this->handleColumnModification();
                    }
                } else {
                    $this->warn("\n⚠️ No fillable columns found in the model. Switching to manual entry.");
                    $this->handleColumnSetup();
                }
            } else {
                $this->handleColumnSetup();
            }

            $searchableColumns = $this->handleSearchableColumns();

            // Handle Validation Setup
            $this->handleValidationSetup();

            // Summary
            $this->info('📋 Here\'s a summary of what we\'ll generate:');
            $this->line("\n");
            $this->table(
                ['Component', 'Name/Details'],
                array_merge(
                    [
                        ['Model', $this->modelName],
                        ['Controller', $this->controllerName],
                        ['Route Prefix', $this->routePrefix],
                        ['Columns', implode(', ', $this->selectedColumns)],
                        ['Searchable Columns', implode(', ', $searchableColumns)],
                        ['Datatable ID', $this->datatableId],
                        ['JavaScript File', $this->jsFile],
                        ['JavaScript Path', $this->jsPath],
                    ],
                    !empty($this->validationRules) ? [['Validation Rules', json_encode($this->validationRules, JSON_PRETTY_PRINT)]] : []
                )
            );

            // Confirmation
            if ($this->confirm('🚀 Ready to generate these files?', true)) {
                $this->showSpinner('Generating files');

                // Generate Controller
                $controllerPath = app_path("Http/Controllers/{$this->path}/{$this->controllerName}.php");
                if ($this->updateControllerFile(
                    $this->controllerName,
                    $this->getControllerContent($this->modelName, $this->controllerName),
                    $this->path
                )) {
                    $this->generatedFiles[] = $controllerPath;
                    $this->info('Controller updated successfully');
                } else {
                    throw new \Exception('Controller generation failed');
                }

                // Generate JavaScript
                $jsPath = public_path("js/dashboard/{$this->jsFile}");
                if ($this->handleJs()) {
                    $this->generatedFiles[] = $jsPath;
                    $this->info('JavaScript file generated successfully');
                } else {
                    throw new \Exception('JavaScript generation failed');
                }

                // Generate Views
                $viewPaths = [
                    resource_path("views/dashboard/pages/{$this->modelFirstLower}/index.blade.php"),
                    resource_path("views/dashboard/pages/{$this->modelFirstLower}/modal/show.blade.php"),
                    resource_path("views/dashboard/pages/{$this->modelFirstLower}/modal/create.blade.php"),
                    resource_path("views/dashboard/pages/{$this->modelFirstLower}/modal/edit.blade.php"),
                ];

                try {
                    $this->handleView();
                    $this->generatedFiles = array_merge($this->generatedFiles, $viewPaths);
                    $this->info('Views generated successfully');
                } catch (\Exception $e) {
                    throw new \Exception('Views generation failed: ' . $e->getMessage());
                }

                // Add routes to dashboard.php
                if ($this->addRoutesToDashboard()) {
                    $this->info('Routes added successfully');
                } else {
                    throw new \Exception('Routes update failed');
                }

                // Show next steps
                $this->line("\n📝 Next steps:");
                $this->line("1. Check the generated files in:");
                $this->line("   - Controller: app/Http/Controllers/Dashboard/Pages/{$this->controllerName}.php");
                $this->line("   - JavaScript: public/js/dashboard/{$this->jsFile}");
                $this->line("   - Views: resources/views/dashboard/pages/{$this->modelFirstLower}/");
                $this->line("2. Add your form fields in the create and edit views");
                $this->line("3. Customize the show view to display your data");
                $this->line("4. Add any custom filters in the index view if needed");

                $this->info("\n✨ Generation completed successfully!");

                $this->line("\n");
                $this->info('╔═══════════════════════════════════════════════════╗');
                $this->info('║             🎉 DATATABLE IS READY! 🎉            ║');
                $this->info('╚═══════════════════════════════════════════════════╝');
            }
        } catch (\Exception $e) {
            $this->rollback();
            $this->error("\n🚫 Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function handleColumnModification()
    {
        $this->info("\n🔄 Current columns: " . implode(', ', $this->selectedColumns));

        // Remove columns
        if ($this->confirm('Would you like to remove any columns?', false)) {
            while (true) {
                $removeColumn = $this->choice(
                    'Select column to remove (or "done" to finish)',
                    array_merge(['done'], $this->selectedColumns),
                    'done'
                );

                if ($removeColumn === 'done') break;

                $this->selectedColumns = array_diff($this->selectedColumns, [$removeColumn]);
                $this->info("Updated columns: " . implode(', ', $this->selectedColumns));
            }
        }

        // Add columns
        if ($this->confirm('Would you like to add any columns?', false)) {
            while (true) {
                $newColumn = $this->ask('Enter new column name (or "done" to finish)');

                if (strtolower($newColumn) === 'done') break;

                if (!in_array($newColumn, $this->selectedColumns)) {
                    $this->selectedColumns[] = $newColumn;
                    $this->info("Added column: $newColumn");
                    $this->info("Current columns: " . implode(', ', $this->selectedColumns));
                } else {
                    $this->warn("Column '$newColumn' already exists!");
                }
            }
        }
    }

    private function getColumnsFromModel()
    {
        try {
            $modelClass = "App\\Models\\{$this->modelName}";
            if (class_exists($modelClass)) {
                $model = new $modelClass();
                $fillableColumns = $model->getFillable();

                if (!empty($fillableColumns)) {
                    $this->selectedColumns = $fillableColumns;
                    $this->info("✅ Successfully retrieved columns from model");
                } else {
                    $this->warn("⚠️ No fillable columns found in the model");
                    return false;
                }
            } else {
                $this->error("🚫 Model class not found: {$modelClass}");
                return false;
            }
        } catch (\Exception $e) {
            $this->error("🚫 Error getting columns from model: " . $e->getMessage());
            return false;
        }

        return true;
    }

    public function getControllerContent($model, $controllerName)
    {
        // Generate validation rules string
        $validationRulesStr = empty($this->validationRules) ?
            "'name' => 'required|string',\n                    'email' => 'required|string'," :
            implode(",\n                    ", array_map(
                fn($key, $value) => "'$key' => '$value'",
                array_keys($this->validationRules),
                $this->validationRules
            ));

        // Generate columns string
        $columnsStr = implode(",\n                ", array_map(
            fn($column) => "'$column'",
            $this->selectedColumns
        ));

        // Generate search conditions
        $searchConditionsStr = implode("', 'like', '%' . \$value . '%')\n                                ->orWhere('", $this->selectedColumns);

        return <<<CONTROLLER
        <?php

        namespace App\Http\Controllers\Dashboard\Pages;

        use App\Http\Controllers\BaseController;
        use App\Models\\$model;
        use Illuminate\Http\Request;
        use Yajra\DataTables\Facades\DataTables;

        class {$controllerName} extends BaseController
        {
            /**
             * Display a listing of the resource.
             */
            public function index()
            {
                \$user = auth()->user();
                return view('dashboard.pages.{$this->modelFirstLower}.index', compact('user'));
            }

            /**
             * Show the form for creating a new resource.
             */
            public function create()
            {
                return \$this->componentResponse(view('dashboard.pages.{$this->modelFirstLower}.modal.create'));
            }

            /**
             * Store a newly created resource in storage.
             */
            public function store(Request \$request)
            {
                \$request->validate([
                    {$validationRulesStr}
                ]);

                $model::create(\$request->all());
                return \$this->modalToastResponse('{$model} created successfully');
            }

            /**
             * Display the specified resource.
             */
            public function show(string \$id)
            {
                \${$this->modelFirstLower} = $model::find(\$id);
                return \$this->componentResponse(view('dashboard.pages.{$this->modelFirstLower}.modal.show', compact('{$this->modelFirstLower}')));
            }

            /**
             * Show the form for editing the specified resource.
             */
            public function edit(string \$id)
            {
                \${$this->modelFirstLower} = $model::find(\$id);
                return \$this->componentResponse(view('dashboard.pages.{$this->modelFirstLower}.modal.edit', compact('{$this->modelFirstLower}')));
            }

            /**
             * Update the specified resource in storage.
             */
            public function update(Request \$request)
            {
                \$request->validate([
                    {$validationRulesStr}
                ]);

                \${$this->modelFirstLower} = $model::find(\$request->id);
                \${$this->modelFirstLower}->update(\$request->all());
                return \$this->modalToastResponse('{$model} updated successfully');
            }

            /**
             * Remove the specified resource from storage.
             */
            public function destroy(string \$id)
            {
                \${$this->modelFirstLower} = $model::find(\$id);
                \${$this->modelFirstLower}->delete();
                return response()->json(['message' => '{$model} deleted successfully']);
            }

            public function datatable(Request \$request)
            {
                \$search = request()->get('search');
                \$value = isset(\$search['value']) ? \$search['value'] : null;

                \${$this->modelFirstLower}s = $model::select(
                'id',
                {$columnsStr},
                'created_at',
                )
                    ->when(\$value, function (\$query) use (\$value) {
                        return \$query->where(function (\$query) use (\$value) {
                            \$query->where('{$searchConditionsStr}', 'like', '%' . \$value . '%');
                        });
                    });

                return DataTables::of(\${$this->modelFirstLower}s->latest())
                    ->editColumn('created_at', function (\${$this->modelFirstLower}) {
                        return \${$this->modelFirstLower}->created_at->diffForHumans();
                    })
                    ->make(true);
            }
        }
        CONTROLLER;
    }


    // ==================================================================================================== //

    //  ================================================ JS =============================================== //

    public function handleJs()
    {
        try {
            $jsContent = $this->getDatatableJsContent();
            $success = $this->updateJsFile($this->jsFile, $jsContent, 'js/dashboard');

            if (!$success) {
                $this->error('Failed to write JavaScript file');
                return false;
            }

            return true;
        } catch (\Exception $e) {
            $this->error('Error generating JavaScript: ' . $e->getMessage());
            return false;
        }
    }

    private function getDatatableJsContent()
    {
        // Convert selectedColumns array to proper datatable column format
        $columnsArray = array_map(function ($column) {
            if ($column === 'id') {
                return ['data' => 'id'];
            } elseif ($column === 'created_at') {
                return ['data' => 'created_at', 'title' => 'Created At'];
            } else {
                // Convert snake_case to Title Case for title
                $title = str_replace('_', ' ', ucwords($column, '_'));
                return ['data' => $column, 'title' => $title];
            }
        }, $this->selectedColumns);

        // Add id column at the top
        array_unshift($columnsArray, ['data' => 'id']);

        // Add null column at the end for actions
        $columnsArray[] = ['data' => null];

        // Convert to JSON with proper formatting
        $columnsStr = json_encode($columnsArray, JSON_PRETTY_PRINT);

        return <<<JS
/*=============================================================================
 * {$this->modelName} Management Module
 *
 * This module handles all {$this->modelFirstLower}-related operations in the dashboard including:
 * - CRUD operations through DataTable
 * - Modal interactions
 * - Event handling
 * - API communications
 *============================================================================*/

import { HttpRequest } from '../../core/global/services/httpRequest.js';
import { DASHBOARD_URL } from '../../core/global/config/app-config.js';
import { SweetAlert } from '../../core/global/notifications/sweetAlert.js';
import { \$DatatableController } from '../../core/global/advanced/advanced.js';
import { ModalLoader } from '../../core/global/advanced/advanced.js';

/*---------------------------------------------------------------------------
 * Utility Functions
 * @function defaultErrorHandler - Global error handler for consistency
 * @function reloadDataTable - Refreshes the DataTable after operations
 * @function buildApiUrl - Constructs API endpoints for {$this->modelFirstLower} operations
 *--------------------------------------------------------------------------*/
const defaultErrorHandler = (err) => console.error('Error:', err);
const reloadDataTable = () => {$this->datatableId}.reload();
const buildApiUrl = (path) => `\${DASHBOARD_URL}/{$this->routePrefix}/\${path}`;

/*---------------------------------------------------------------------------
 * Modal Configuration Factory
 * Creates consistent modal configurations with error handling
 * @param {Object} config - Modal configuration options
 * @returns {ModalLoader} Configured modal instance
 *--------------------------------------------------------------------------*/
const createModalLoader = (config) => new ModalLoader({
    modalBodySelector: config.modalBodySelector || '.modal-body',
    endpoint: config.endpoint,
    triggerSelector: config.triggerSelector,
    onSuccess: config.onSuccess,
    onError: config.onError || defaultErrorHandler
});

/*=============================================================================
 * API Operation Handlers
 * Manages all HTTP requests with consistent error handling and response processing
 * Each method follows a similar pattern:
 * 1. Executes the request
 * 2. Handles success callback
 * 3. Manages errors through defaultErrorHandler
 *============================================================================*/
const apiOperations = {
    _DELETE_: async (endpoint, onSuccess) => {
        try {
            const confirmDelete = await SweetAlert.deleteAction();
            if (confirmDelete) {
                const response = await HttpRequest.del(endpoint);
                onSuccess(response);
            }
        } catch (error) {
            defaultErrorHandler(error);
        }
    },

    _SHOW_: async (id, endpoint) => {
        createModalLoader({
            modalBodySelector: '#show-modal .modal-body',
            endpoint,
            onError: defaultErrorHandler
        });
    },

    _EDIT_: async (id, endpoint, onSuccess) => {
        createModalLoader({
            modalBodySelector: '#edit-modal .modal-body',
            endpoint,
            onSuccess,
            onError: defaultErrorHandler
        });
    },

    _POST_: async (endpoint, onSuccess) => {
        try {
            const response = await HttpRequest.post(endpoint);
            onSuccess(response);
        } catch (error) {
            defaultErrorHandler(error);
        }
    },

    _PATCH_: async (endpoint, onSuccess) => {
        try {
            const response = await HttpRequest.patch(endpoint);
            onSuccess(response);
        } catch (error) {
            defaultErrorHandler(error);
        }
    },

    _GET_: async (endpoint, onSuccess) => {
        try {
            const response = await HttpRequest.get(endpoint);
            onSuccess(response);
        } catch (error) {
            defaultErrorHandler(error);
        }
    },

    _PUT_: async (endpoint, onSuccess) => {
        try {
            const response = await HttpRequest.put(endpoint);
            onSuccess(response);
        } catch (error) {
            defaultErrorHandler(error);
        }
    },
};

/*=============================================================================
 * User Interface Event Handlers
 * Manages user interactions and connects them to appropriate API operations
 * Each handler:
 * 1. Receives user input
 * 2. Calls appropriate API operation
 * 3. Handles the response (success/error)
 *============================================================================*/
const userActionHandlers = {
    delete: function (id) {
        this.callCustomFunction('_DELETE_', buildApiUrl(id), (response) => {
            response.risk ? SweetAlert.error() : (SweetAlert.deleteSuccess(), reloadDataTable());
        });
    },

    show: function (id) {
        this.callCustomFunction('_SHOW_', id, buildApiUrl(`\${id}/show`));
    },

    edit: function (id) {
        this.callCustomFunction('_EDIT_', id, buildApiUrl(`\${id}/edit`), (response) => {
            // Handler for successful edit operation
        });
    },

    status: function (id) {
        this.callCustomFunction('_PATCH_', buildApiUrl(`\${id}/status`), (response) => {
            console.log(response);
        });
    }
};

/*---------------------------------------------------------------------------
 * Event Listener Configurations
 * Maps DOM events to their respective handlers
 * Structure:
 * - event: The DOM event to listen for
 * - selector: The DOM element selector to attach the listener to
 * - handler: The function to execute when the event occurs
 *--------------------------------------------------------------------------*/
const uiEventListeners = [
    { event: 'click', selector: '.delete-btn', handler: userActionHandlers.delete },
    { event: 'click', selector: '.btn-show', handler: userActionHandlers.show },
    { event: 'click', selector: '.btn-edit', handler: userActionHandlers.edit },
];

/*---------------------------------------------------------------------------
 * DataTable Configuration
 * Defines the structure and behavior of the {$this->modelName} management table
 *--------------------------------------------------------------------------*/
const tableColumns = $columnsStr;

const tableColumnDefinitions = [
    { targets: [0], orderable: false, htmlType: 'selectCheckbox' },
    {
        targets: [-1],
        htmlType: 'actions',
        className: 'text-end',
        actionButtons: {
            edit: true,
            delete: { type: 'null' },
            view: true
        }
    },
];

/*---------------------------------------------------------------------------
 * Bulk Action Handler
 * Processes operations on multiple selected {$this->modelFirstLower}s
 * @param {Array} selectedIds - Array of selected {$this->modelFirstLower} IDs
 *--------------------------------------------------------------------------*/
const handleBulkActions = (selectedIds) => {
    // Implementation for bulk actions
    // Example: Delete multiple {$this->modelFirstLower}s, change status, etc.
};

/*=============================================================================
 * DataTable Initialization
 * Creates and configures the main {$this->modelFirstLower} management interface
 *============================================================================*/
export const {$this->datatableId} = new \$DatatableController('{$this->datatableId}', {
    lengthMenu: [[15, 50, 100, 200, -1], [15, 50, 100, 200, 'All']],
    selectedAction: handleBulkActions,
    ajax: {
        url: buildApiUrl('datatable'),
        data: (d) => ({
            ...d,
            // Add your custom filters here
        })
    },
    columns: tableColumns,
    columnDefs: \$DatatableController.generateColumnDefs(tableColumnDefinitions),
    customFunctions: apiOperations,
    eventListeners: uiEventListeners
});

// Initialize create {$this->modelFirstLower} modal
createModalLoader({
    triggerSelector: '.create',
    endpoint: buildApiUrl('create')
});

// Global access for table reload
window.RDT = reloadDataTable;

JS;
    }

    public function handle()
    {
        if ($this->option('rollback')) {
            // Ask for model name
            $this->modelName = $this->ask('Enter the model name to rollback');
            $this->modelFirstLower = lcfirst($this->modelName);
            $this->controllerName = $this->modelName . 'Controller';
            $this->jsFile = strtolower($this->modelName) . '.js';

            // Build paths for generated files
            $this->generatedFiles = [
                app_path("Http/Controllers/Dashboard/Pages/{$this->controllerName}.php"),
                public_path("js/dashboard/{$this->jsFile}"),
                resource_path("views/dashboard/pages/{$this->modelFirstLower}"), // Directory containing all view files
            ];

            // Confirm rollback
            if ($this->confirm("Are you sure you want to remove all files for {$this->modelName}?", false)) {
                $this->rollback();
            }
            return 0;
        }

        return $this->handleAll();
    }


    //  ================================================ JS =============================================== //

    //  ================================================ END JS =============================================== //


    //  ================================================ view =============================================== //

    //  ================================================ END view =============================================== //

    public function handleView()
    {
        $this->updateViewFile('index.blade.php', $this->getIndexViewContent(), 'dashboard/pages/' . $this->modelFirstLower);
        $this->updateViewFile('show.blade.php', $this->getShowViewContent(), 'dashboard/pages/' . $this->modelFirstLower . '/modal');
        $this->updateViewFile('create.blade.php', $this->getCreateViewContent(), 'dashboard/pages/' . $this->modelFirstLower . '/modal');
        $this->updateViewFile('edit.blade.php', $this->getEditViewContent(), 'dashboard/pages/' . $this->modelFirstLower . '/modal');
    }

    private function getViewColumnsStr()
    {
        // Convert columns to simple array string format
        return "['" . implode("', '", $this->selectedColumns) . "', 'actions']";
    }

    public function getIndexViewContent()
    {
        // Use the simple array format for view
        $viewColumnsStr = $this->getViewColumnsStr();

        return <<<VIEW
<!---------------------------
    Layout
---------------------------->
@extends('dashboard.layout.index')

<!---------------------------
    Title
---------------------------->
@section('title', '{$this->modelName}')

<!---------------------------
    Toolbar
---------------------------->
@section('toolbar')
    @include('dashboard.components.toolbar', [
        'title' => '{$this->modelName}',
        'currentPage' => '{$this->modelName} Management',
    ])
@endsection

<!---------------------------
    Columns
---------------------------->

@php
\$columns = {$viewColumnsStr};
@endphp

<!---------------------------
    Main Content
---------------------------->
@section('content')
    <x-lebify-table
    id="{$this->datatableId}"
    :columns="\$columns"

    {{-- create="true"                         // BY DEFAULT TRUE
    selected="true"                            // BY DEFAULT TRUE
    filter="true"                              // BY DEFAULT TRUE
    showCheckbox="true"                        // BY DEFAULT TRUE
    showSearch="true"                          // BY DEFAULT TRUE
    showColumnVisibility="true"                // BY DEFAULT TRUE
    columnVisibilityPlacement="bottom-end"     // BY DEFAULT BOTTOM-END
    columnSettingsTitle="Column Settingss"     // BY DEFAULT COLUMN SETTINGS
    columnToggles=""                           // BY DEFAULT EMPTY
    tableClass="table-class"                   // BY DEFAULT EMPTY
    searchPlaceholder="Search..."              // BY DEFAULT SEARCH...
    selectedText="Selected"                    // BY DEFAULT SELECTED
    selectedActionButtonClass="btn-success"    // BY DEFAULT btn-danger
    selectedActionButtonText="Delete Selected" // BY DEFAULT DELETE SELECTED
    selectedAction=""                          // BY DEFAULT EMPTY
    --}}
    >


{{-- start Filter Options --}}

@section('filter-options')


     {{--  Filters... --}}

     {{-- example form field --}}
   {{-- <label class="form-check form-check-sm form-check-custom form-check-solid">
        <select class="form-select form-select-solid" data-control="select2" data-placeholder="Select Status" name="status">
            <option></option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </label> --}}
@endsection
{{-- End Filter Options --}}

    </x-lebify-table>
@endsection


<!---------------------------
    Filter Options
---------------------------->


<!---------------------------
    Modals
---------------------------->
<x-lebify-modal modal-id="create-modal" size="lg" submit-form-id="createForm" title="Create"></x-lebify-modal>
<x-lebify-modal modal-id="edit-modal" size="lg" submit-form-id="editForm" title="Edit"></x-lebify-modal>
<x-lebify-modal modal-id="show-modal" size="lg" :show-submit-button="false" title="Show"></x-lebify-modal>

<!---------------------------
    Scripts
---------------------------->
@push('scripts')
    <script src="{{ asset('js/dashboard/{$this->modelFirstLower}.js') }}" type="module" defer></script>
@endpush

VIEW;
    }

    public function getCreateViewContent()
    {
        return <<<VIEW
        <form id="create-{$this->modelFirstLower}-form" form-id="createForm" http-request route="{{ route('dashboard.{$this->routePrefix}.store') }}"
    identifier="single-form-post-handler" feedback close-modal success-toast on-success="RDT">

     {{-- form fields ... --}}

     {{-- example form field --}}
    {{-- <div class="mb-3">
        <label for="name" class="form-label">name</label>
        <input type="text" feedback-id="name-feedback" class="form-control form-control-solid" name="name" id="name">
        <div id="name-feedback" class="invalid-feedback"></div>
    </div> --}}

</form>

VIEW;
    }

    public function getEditViewContent()
    {
        return <<<VIEW
<form id="edit-{$this->modelFirstLower}-form" form-id="editForm" http-request route="{{ route('dashboard.{$this->routePrefix}.update') }}"
    identifier="single-form-post-handler" feedback close-modal success-toast on-success="RDT">
    <input type="hidden" name="id" id="id" value="{{ \${$this->modelFirstLower}->id }}">

     {{-- form fields ... --}}

     {{-- example form field --}}
    {{-- <div class="mb-3">
        <label for="name" class="form-label">name</label>
        <input type="text" value="{{ \${$this->modelFirstLower}->name }}" feedback-id="name-feedback" class="form-control form-control-solid"
            name="name" id="name">
        <div id="name-feedback" class="invalid-feedback"></div>
    </div> --}}
</form>

VIEW;
    }

    public function getShowViewContent()
    {
        return <<<VIEW
<div class="d-flex flex-column">

     {{-- form fields ... --}}

     {{-- example form field --}}
    <div class="mb-3">
        <label class="form-label fw-bold">Created At</label>
        <p class="text-gray-800">{{ \${$this->modelFirstLower}->created_at->diffForHumans() }}</p>
    </div>

</div>
VIEW;
    }
}
