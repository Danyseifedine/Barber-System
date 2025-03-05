<?php

namespace App\Console\Commands\Setup\Common\Migration\R;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TableManager extends Command
{
    protected $signature = 'table:manage {--table= : The table to modify}';
    protected $description = 'Advanced table management tool for adding, removing, or modifying columns';

    private $columnTypes = [
        1 => ['name' => 'string', 'description' => 'String (VARCHAR)'],
        2 => ['name' => 'integer', 'description' => 'Integer'],
        3 => ['name' => 'bigInteger', 'description' => 'Big Integer'],
        4 => ['name' => 'text', 'description' => 'Text'],
        5 => ['name' => 'boolean', 'description' => 'Boolean'],
        6 => ['name' => 'date', 'description' => 'Date'],
        7 => ['name' => 'dateTime', 'description' => 'DateTime'],
        8 => ['name' => 'decimal', 'description' => 'Decimal'],
        9 => ['name' => 'float', 'description' => 'Float'],
        10 => ['name' => 'json', 'description' => 'JSON'],
        11 => ['name' => 'timestamp', 'description' => 'Timestamp'],
        12 => ['name' => 'enum', 'description' => 'Enumeration'],
    ];

    private $modifiers = [
        1 => ['name' => 'nullable', 'description' => 'Allow NULL values'],
        2 => ['name' => 'default', 'description' => 'Set default value'],
        3 => ['name' => 'unique', 'description' => 'Must be unique'],
        4 => ['name' => 'index', 'description' => 'Create an index'],
        5 => ['name' => 'unsigned', 'description' => 'Unsigned (for numbers)'],
        6 => ['name' => 'after', 'description' => 'Position after column'],
    ];

    public function handle()
    {
        $this->drawLogo();

        // Get all available tables
        $tables = $this->getTables();

        // Select table
        $table = $this->option('table') ?? $this->choice(
            'Select the table to modify:',
            $tables,
            0
        );

        // Show current columns
        $this->showCurrentColumns($table);

        // Select operation
        $operation = $this->choice(
            'What operation would you like to perform?',
            [
                'add' => 'Add new column(s)',
                'remove' => 'Remove existing column(s)',
                'modify' => 'Modify existing column(s)'
            ],
            'add'
        );

        switch ($operation) {
            case 'add':
                $columns = $this->collectColumns();
                if (!empty($columns)) {
                    $this->generateAddMigration($table, $columns);
                }
                break;

            case 'remove':
                $existingColumns = Schema::getColumnListing($table);
                $columnsToRemove = $this->choice(
                    'Select columns to remove',
                    $existingColumns,
                    null,
                    null,
                    true // Allow multiple selections
                );
                if (!empty($columnsToRemove)) {
                    $this->generateRemoveMigration($table, $columnsToRemove);
                }
                break;

            case 'modify':
                $existingColumns = Schema::getColumnListing($table);
                $columnToModify = $this->choice(
                    'Select column to modify',
                    $existingColumns
                );
                $modifications = $this->handleModifyColumn($table, $columnToModify);
                if (!empty($modifications)) {
                    $this->generateModifyMigration($table, $columnToModify, $modifications);
                }
                break;
        }

        return Command::SUCCESS;
    }

    private function drawLogo()
    {
        $this->line("\n");
        $this->info('╔═══════════════════════════════════════════╗');
        $this->info('║           TABLE MANAGER                   ║');
        $this->info('╚═══════════════════════════════════════════╝');
        $this->line("\n");
    }

    private function getTables()
    {
        return collect(DB::select('SHOW TABLES'))
            ->map(function ($table) {
                return array_values((array) $table)[0];
            })
            ->reject(function ($name) {
                return in_array($name, ['migrations', 'failed_jobs', 'password_reset_tokens']);
            })
            ->values()
            ->toArray();
    }

    private function showCurrentColumns($table)
    {
        $columns = Schema::getColumnListing($table);
        $this->info("\nCurrent columns in '$table' table:");
        $this->table(
            ['Column Name', 'Type'],
            collect($columns)->map(function ($column) use ($table) {
                $type = Schema::getColumnType($table, $column);
                return [$column, $type];
            })->toArray()
        );
    }

    private function generateAddMigration($table, $columns)
    {
        $migrationName = 'add_' . implode('_', array_column($columns, 'name')) . '_to_' . $table . '_table';
        $className = Str::studly($migrationName);
        $timestamp = Carbon::now()->format('Y_m_d_His');
        $filename = $timestamp . '_' . $migrationName . '.php';

        $migrationContent = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('$table', function (Blueprint \$table) {
            {$this->generateColumnsUp($columns)}
        });
    }

    public function down(): void
    {
        Schema::table('$table', function (Blueprint \$table) {
            {$this->generateColumnsDown($columns)}
        });
    }
};
PHP;

        $path = database_path('migrations/' . $filename);
        File::put($path, $migrationContent);

        $this->info("\n✅ Migration created successfully: {$filename}");

        if ($this->confirm('Would you like to run this migration now?', true)) {
            $this->call('migrate', [
                '--path' => 'database/migrations/' . $filename,
            ]);
        }
    }

    private function generateRemoveMigration($table, $columns)
    {
        $migrationName = 'remove_' . implode('_', $columns) . '_from_' . $table . '_table';
        $className = Str::studly($migrationName);
        $timestamp = Carbon::now()->format('Y_m_d_His');
        $filename = $timestamp . '_' . $migrationName . '.php';

        $columnsString = "'" . implode("', '", $columns) . "'";

        $migrationContent = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('$table', function (Blueprint \$table) {
            \$table->dropColumn([$columnsString]);
        });
    }

    public function down(): void
    {
        Schema::table('$table', function (Blueprint \$table) {
            // Add columns back if needed
        });
    }
};
PHP;

        $path = database_path('migrations/' . $filename);
        File::put($path, $migrationContent);

        $this->info("\n✅ Migration created successfully: {$filename}");

        if ($this->confirm('Would you like to run this migration now?', true)) {
            $this->call('migrate', [
                '--path' => 'database/migrations/' . $filename,
            ]);
        }
    }

    private function handleModifyColumn($table, $column)
    {
        $modifications = [];

        $this->info("\n📝 Modifying column: $column");

        // Show current column details
        $currentType = Schema::getColumnType($table, $column);
        $this->info("Current type: $currentType");

        // Modification options
        $modificationTypes = [
            'type' => 'Change column type',
            'nullable' => 'Toggle nullable',
            'default' => 'Change default value',
            'index' => 'Add/Remove index',
            'unique' => 'Add/Remove unique constraint',
            'rename' => 'Rename column',
            'comment' => 'Add/Change comment',
            'after' => 'Change column position',
        ];

        while (true) {
            $modification = $this->choice(
                'What would you like to modify?',
                array_merge($modificationTypes, ['done' => 'Finish modifications']),
                'done'
            );

            if ($modification === 'done') {
                break;
            }

            switch ($modification) {
                case 'type':
                    $this->table(
                        ['Number', 'Type', 'Description'],
                        collect($this->columnTypes)->map(function ($type, $key) {
                            return [$key, $type['name'], $type['description']];
                        })->toArray()
                    );

                    $newType = $this->choice(
                        'Select new column type',
                        collect($this->columnTypes)->pluck('name')->toArray()
                    );

                    $modifications['type'] = $newType;

                    // Handle specific type requirements
                    if ($newType === 'decimal') {
                        $modifications['precision'] = $this->ask('Enter precision (total digits)', 8);
                        $modifications['scale'] = $this->ask('Enter scale (decimal places)', 2);
                    } elseif ($newType === 'enum') {
                        $values = $this->ask('Enter enum values (comma-separated)');
                        $modifications['values'] = array_map('trim', explode(',', $values));
                    }
                    break;

                case 'nullable':
                    $nullableChoice = $this->choice(
                        'Select nullable status',
                        [
                            'nullable' => 'Make nullable (allow NULL values)',
                            'not_nullable' => 'Make NOT nullable (disallow NULL values)',
                        ]
                    );
                    $modifications['nullable'] = ($nullableChoice === 'nullable');
                    break;

                case 'default':
                    $defaultChoice = $this->choice(
                        'Select default value type',
                        [
                            'null' => 'NULL',
                            'string' => 'String value',
                            'number' => 'Numeric value',
                            'boolean' => 'Boolean value',
                            'expression' => 'SQL Expression (NOW(), UUID(), etc.)',
                            'remove' => 'Remove default value',
                        ]
                    );

                    switch ($defaultChoice) {
                        case 'null':
                            $modifications['default'] = null;
                            break;
                        case 'string':
                            $modifications['default'] = $this->ask('Enter default string value');
                            break;
                        case 'number':
                            $modifications['default'] = $this->ask('Enter default numeric value');
                            break;
                        case 'boolean':
                            $modifications['default'] = $this->confirm('Should the default value be true?', true) ? 1 : 0;
                            break;
                        case 'expression':
                            $modifications['default'] = DB::raw($this->ask('Enter SQL expression'));
                            break;
                        case 'remove':
                            $modifications['dropDefault'] = true;
                            break;
                    }
                    break;

                case 'index':
                    $hasIndex = Schema::hasIndex($table, [$column]);
                    if ($hasIndex) {
                        if ($this->confirm('Column already has an index. Would you like to remove it?')) {
                            $modifications['dropIndex'] = true;
                        }
                    } else {
                        $indexType = $this->choice(
                            'Select index type',
                            [
                                'index' => 'Regular index',
                                'unique' => 'Unique index',
                                'fulltext' => 'Fulltext index (for text/string columns)',
                            ]
                        );
                        $modifications['addIndex'] = $indexType;
                    }
                    break;

                case 'unique':
                    $hasUnique = Schema::hasIndex($table, [$column], true);
                    if ($hasUnique) {
                        if ($this->confirm('Column already has a unique constraint. Would you like to remove it?')) {
                            $modifications['dropUnique'] = true;
                        }
                    } else {
                        $modifications['addUnique'] = true;
                    }
                    break;

                case 'rename':
                    $newName = $this->ask('Enter new column name');
                    $modifications['rename'] = $newName;
                    break;

                case 'comment':
                    $comment = $this->ask('Enter column comment');
                    $modifications['comment'] = $comment;
                    break;

                case 'after':
                    $existingColumns = Schema::getColumnListing($table);
                    $afterColumn = $this->choice(
                        'Position this column after which column?',
                        array_merge(['FIRST'], array_diff($existingColumns, [$column]))
                    );
                    $modifications['after'] = $afterColumn === 'FIRST' ? null : $afterColumn;
                    break;
            }

            $this->info("\n✅ Modification added: $modification");
            $this->info("You can add more modifications or choose 'done' to finish.");
        }

        return $modifications;
    }

    private function generateModifyMigration($table, $column, $modifications)
    {
        $migrationName = 'modify_' . $column . '_in_' . $table . '_table';
        $className = Str::studly($migrationName);
        $timestamp = Carbon::now()->format('Y_m_d_His');
        $filename = $timestamp . '_' . $migrationName . '.php';

        $upChanges = $this->generateModificationUp($table, $column, $modifications);
        $downChanges = $this->generateModificationDown($table, $column, $modifications);

        $migrationContent = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('$table', function (Blueprint \$table) {
            $upChanges
        });
    }

    public function down(): void
    {
        Schema::table('$table', function (Blueprint \$table) {
            $downChanges
        });
    }
};
PHP;

        $path = database_path('migrations/' . $filename);
        File::put($path, $migrationContent);

        $this->info("\n✅ Migration created successfully: {$filename}");

        if ($this->confirm('Would you like to run this migration now?', true)) {
            $this->call('migrate', [
                '--path' => 'database/migrations/' . $filename,
            ]);
        }
    }

    private function generateModificationUp($table, $column, $modifications)
    {
        $changes = [];

        if (isset($modifications['rename'])) {
            $changes[] = "\$table->renameColumn('$column', '{$modifications['rename']}');";
            $column = $modifications['rename'];
        }

        // Start building the column modification
        if (isset($modifications['type']) || isset($modifications['nullable'])) {
            // Convert varchar to string type
            $type = $modifications['type'] ?? Schema::getColumnType($table, $column);
            $type = strtolower($type) === 'varchar' ? 'string' : $type;

            // Build the column definition
            $columnDefinition = [];
            $columnDefinition[] = "'$column'";

            if (isset($modifications['precision'])) {
                $columnDefinition[] = $modifications['precision'];
                $columnDefinition[] = $modifications['scale'];
            }
            if (isset($modifications['values'])) {
                $values = array_map(fn($v) => "'$v'", $modifications['values']);
                $columnDefinition[] = '[' . implode(', ', $values) . ']';
            }

            $change = "\$table->$type(" . implode(', ', $columnDefinition) . ")";

            // Add modifiers
            if (isset($modifications['nullable'])) {
                $change .= $modifications['nullable'] ? '->nullable()' : '->nullable(false)';
            }

            if (isset($modifications['default'])) {
                $change .= "->default('{$modifications['default']}')";
            }

            if (isset($modifications['comment'])) {
                $change .= "->comment('{$modifications['comment']}')";
            }

            // Add the change() method at the end
            $change .= '->change();';
            $changes[] = $change;
        }

        // Handle indexes separately as they don't need ->change()
        if (isset($modifications['addIndex'])) {
            $changes[] = "\$table->index(['$column']);";
        } elseif (isset($modifications['dropIndex'])) {
            $changes[] = "\$table->dropIndex(['$column']);";
        }

        if (isset($modifications['addUnique'])) {
            $changes[] = "\$table->unique(['$column']);";
        } elseif (isset($modifications['dropUnique'])) {
            $changes[] = "\$table->dropUnique(['$column']);";
        }

        return implode("\n            ", $changes);
    }

    private function generateModificationDown($table, $column, $modifications)
    {
        // Generate reverse operations for down method
        $changes = [];

        // Reverse rename
        if (isset($modifications['rename'])) {
            $changes[] = "\$table->renameColumn('{$modifications['rename']}', '$column');";
        }

        // Add other reverse operations as needed

        return implode("\n            ", $changes);
    }

    private function collectColumns()
    {
        $columns = [];

        while (true) {
            if (!$this->confirm("\nWould you like to add a new column?", true)) {
                break;
            }

            $column = [];

            // Column name
            $column['name'] = $this->ask('Enter column name');

            // Column type
            $this->table(
                ['Number', 'Type', 'Description'],
                collect($this->columnTypes)->map(function ($type, $key) {
                    return [$key, $type['name'], $type['description']];
                })->toArray()
            );

            $typeChoice = $this->choice(
                'Select column type',
                collect($this->columnTypes)->pluck('name')->toArray()
            );
            $column['type'] = $typeChoice;

            // Handle specific type requirements
            switch ($typeChoice) {
                case 'decimal':
                    $column['precision'] = $this->ask('Enter precision (total digits)', 8);
                    $column['scale'] = $this->ask('Enter scale (decimal places)', 2);
                    break;
                case 'enum':
                    $values = $this->ask('Enter enum values (comma-separated)');
                    $column['values'] = array_map('trim', explode(',', $values));
                    break;
            }

            // Modifiers
            $this->table(
                ['Number', 'Modifier', 'Description'],
                collect($this->modifiers)->map(function ($modifier, $key) {
                    return [$key, $modifier['name'], $modifier['description']];
                })->toArray()
            );

            while ($this->confirm('Would you like to add a modifier?', true)) {
                $modifier = $this->choice(
                    'Select modifier',
                    collect($this->modifiers)->pluck('name')->toArray()
                );

                switch ($modifier) {
                    case 'default':
                        $column['default'] = $this->ask('Enter default value');
                        break;
                    case 'after':
                        $column['after'] = $this->ask('Enter column name to position after');
                        break;
                    default:
                        $column[$modifier] = true;
                }
            }

            $columns[] = $column;
        }

        return $columns;
    }

    private function generateColumnsUp($columns)
    {
        return collect($columns)->map(function ($column) {
            $method = "\$table->{$column['type']}('{$column['name']}'";

            // Add type-specific parameters
            if (isset($column['precision'])) {
                $method .= ", {$column['precision']}, {$column['scale']}";
            }
            if (isset($column['values'])) {
                $values = array_map(function ($value) {
                    return "'{$value}'";
                }, $column['values']);
                $method .= ", [" . implode(', ', $values) . "]";
            }
            $method .= ')';

            // Add modifiers
            if (!empty($column['unsigned'])) $method .= '->unsigned()';
            if (!empty($column['nullable'])) $method .= '->nullable()';
            if (!empty($column['unique'])) $method .= '->unique()';
            if (!empty($column['index'])) $method .= '->index()';
            if (isset($column['default'])) $method .= "->default('{$column['default']}')";
            if (isset($column['after'])) $method .= "->after('{$column['after']}')";

            return str_repeat(' ', 12) . $method . ';';
        })->implode("\n");
    }

    private function generateColumnsDown($columns)
    {
        $columnNames = array_column($columns, 'name');
        return str_repeat(' ', 12) . "\$table->dropColumn(['" . implode("', '", $columnNames) . "']);";
    }
}
