<?php

namespace App\Console\Commands\Setup\Common\Migration\R;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Helper\ProgressBar;

class ExportDB extends Command
{
    protected $signature = 'data:export';
    protected $description = 'Export database tables to various formats';

    private $supportedFormats = [
        1 => ['name' => 'csv', 'description' => 'CSV format'],
        2 => ['name' => 'json', 'description' => 'JSON format'],
        3 => ['name' => 'sql', 'description' => 'SQL INSERT statements'],
    ];

    public function handle()
    {
        $this->drawLogo();

        // Get all tables
        $tables = $this->getTables();

        // Create a display array with "Export All Tables" option
        $displayTables = ['Export All Tables'];
        foreach ($tables as $table) {
            $displayTables[] = $table;
        }

        // Display table selection
        $this->table(
            ['Number', 'Table', 'Row Count'],
            collect($displayTables)->map(function ($table, $key) {
                if ($table === 'Export All Tables') {
                    $rowCount = '-';
                } else {
                    $rowCount = number_format(DB::table($table)->count());
                }
                return [$key, $table, $rowCount];
            })->toArray()
        );

        // Select table
        $selectedTable = $this->choice(
            'Select table to export',
            $displayTables,
            0
        );

        // Display format selection
        $this->table(
            ['Number', 'Format', 'Description'],
            collect($this->supportedFormats)->map(function ($format, $key) {
                return [$key, $format['name'], $format['description']];
            })->toArray()
        );

        // Select format
        $formatChoices = collect($this->supportedFormats)->pluck('name')->toArray();
        $selectedFormat = $this->choice(
            'Select export format',
            $formatChoices,
            0
        );

        // Create export directory if it doesn't exist
        $exportPath = storage_path('app/exports');
        if (!file_exists($exportPath)) {
            mkdir($exportPath, 0755, true);
        }

        if ($selectedTable === 'Export All Tables') {
            $this->exportAllTables($tables, $selectedFormat, $exportPath);
        } else {
            $this->exportTable($selectedTable, $selectedFormat, $exportPath);
        }

        return Command::SUCCESS;
    }

    private function drawLogo()
    {
        $this->line("\n");
        $this->info('╔═══════════════════════════════════════════╗');
        $this->info('║           DATABASE EXPORTER               ║');
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
                // Exclude Laravel system tables if you want
                return in_array($name, ['migrations', 'failed_jobs', 'password_reset_tokens']);
            })
            ->values()
            ->toArray();
    }

    private function exportAllTables($tables, $format, $exportPath)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');

        if (!class_exists('ZipArchive')) {
            $this->warn("\nZipArchive not found. Creating individual files instead...");

            $progressBar = $this->output->createProgressBar(count($tables));
            $progressBar->start();

            foreach ($tables as $table) {
                if ($table !== 'Export All Tables') {
                    // Create table-specific directory
                    $tablePath = "{$exportPath}/{$table}";
                    if (!file_exists($tablePath)) {
                        mkdir($tablePath, 0755, true);
                    }

                    $content = $this->getTableContent($table, $format);
                    $filename = "{$tablePath}/{$timestamp}.{$format}";
                    file_put_contents($filename, $content);
                    $progressBar->advance();
                }
            }

            $progressBar->finish();
            $this->info("\n✅ All tables exported to their respective directories in: {$exportPath}");
            return;
        }

        // If ZipArchive is available, organize files in zip by table
        $zipPath = "{$exportPath}/full_export_{$timestamp}.zip";
        $zip = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            $progressBar = $this->output->createProgressBar(count($tables) - 1);
            $progressBar->start();

            foreach ($tables as $table) {
                if ($table !== 'Export All Tables') {
                    $content = $this->getTableContent($table, $format);
                    // Create table-specific directory in zip
                    $filename = "{$table}/{$timestamp}.{$format}";
                    $zip->addFromString($filename, $content);
                    $progressBar->advance();
                }
            }

            $zip->close();
            $progressBar->finish();

            $this->info("\n✅ Full database export completed: {$zipPath}");
        } else {
            $this->error("\n❌ Failed to create zip archive");
        }
    }

    private function exportTable($table, $format, $exportPath)
    {
        // Create table-specific directory
        $tablePath = "{$exportPath}/{$table}";
        if (!file_exists($tablePath)) {
            mkdir($tablePath, 0755, true);
        }

        $content = $this->getTableContent($table, $format);
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "{$timestamp}.{$format}";
        $fullPath = "{$tablePath}/{$filename}";

        file_put_contents($fullPath, $content);

        $this->info("\n✅ Export completed: {$fullPath}");
        $this->info("📁 All {$table} exports are in: {$tablePath}");
    }

    private function getTableContent($table, $format)
    {
        $data = DB::table($table)->get();

        switch ($format) {
            case 'json':
                return $data->toJson(JSON_PRETTY_PRINT);

            case 'csv':
                $output = fopen('php://temp', 'r+');

                // Add headers
                if (count($data) > 0) {
                    fputcsv($output, array_keys((array) $data[0]));
                }

                // Add data
                foreach ($data as $row) {
                    fputcsv($output, (array) $row);
                }

                rewind($output);
                $content = stream_get_contents($output);
                fclose($output);

                return $content;

            case 'sql':
                $columns = Schema::getColumnListing($table);
                $inserts = [];

                foreach ($data as $row) {
                    $values = array_map(function ($value) {
                        if (is_null($value)) return 'NULL';
                        if (is_numeric($value)) return $value;
                        return "'" . addslashes($value) . "'";
                    }, (array) $row);

                    $inserts[] = "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");";
                }

                return implode("\n", $inserts);
        }

        return '';
    }
}
