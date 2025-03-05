<?php

namespace App\Console\Commands\Setup\Common\Migration\R;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ImportDB extends Command
{
    protected $signature = 'data:import';
    protected $description = 'Import data from exported files';
    private $selectedTable;

    public function handle()
    {
        $this->drawLogo();

        // Get available export directories
        $exportPath = storage_path('app/exports');
        if (!file_exists($exportPath)) {
            $this->error('No exports found! Please run data:export first.');
            return Command::FAILURE;
        }

        $availableTables = collect(File::directories($exportPath))
            ->map(fn($path) => basename($path))
            ->toArray();

        if (empty($availableTables)) {
            $this->error('No exported tables found!');
            return Command::FAILURE;
        }

        // Select table and store it in the class property
        $this->selectedTable = $this->choice(
            'Select table to import',
            $availableTables,
            0
        );

        // Get available exports for the selected table
        $tablePath = "{$exportPath}/{$this->selectedTable}";
        $exports = collect(File::files($tablePath))
            ->map(function ($file) {
                return [
                    'filename' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'size' => $this->formatFileSize($file->getSize()),
                    'date' => date('Y-m-d H:i:s', $file->getMTime()),
                    'type' => strtoupper($file->getExtension())
                ];
            })
            ->filter(function ($file) {
                return in_array(strtolower($file['type']), ['json', 'sql']);
            })
            ->values();

        if ($exports->isEmpty()) {
            $this->error('No valid export files found for this table!');
            return Command::FAILURE;
        }

        // Display available exports
        $this->table(
            ['#', 'Filename', 'Size', 'Date', 'Type'],
            $exports->map(function ($export, $key) {
                return [
                    $key + 1,
                    $export['filename'],
                    $export['size'],
                    $export['date'],
                    $export['type']
                ];
            })->toArray()
        );

        // Select export file
        $selectedIndex = $this->ask('Enter the number of the export you want to import') - 1;
        $selectedExport = $exports[$selectedIndex] ?? null;

        if (!$selectedExport) {
            $this->error('Invalid selection!');
            return Command::FAILURE;
        }

        // Preview the data with similarity info
        $analysisResult = $this->readExportFile($selectedExport['path'], $selectedExport['type']);

        if ($analysisResult) {
            $this->info("\n📊 Data Analysis Results:");

            // Show overall statistics
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Records', $analysisResult['statistics']['total_records']],
                    ['New Records', $analysisResult['statistics']['new_records']],
                    ['Duplicate Records', $analysisResult['statistics']['existing_records']],
                    ['Overall Similarity', $analysisResult['statistics']['overall_similarity'] . '%'],
                ]
            );

            // Show preview of records with their status
            $this->info("\nRecord Preview:");
            $previewData = array_slice($analysisResult['records'], 0, 5);
            $tableRows = [];

            foreach ($previewData as $record) {
                $tableRows[] = [
                    'Status' => $record['status'],
                    'Similarity' => $record['similarity'] . '%',
                    'Data' => json_encode($record['data'])
                ];
            }

            $this->table(['Status', 'Similarity', 'Data'], $tableRows);

            if ($analysisResult['statistics']['existing_records'] > 0) {
                $this->warn("\n⚠️ Some records already exist in the database!");

                if (!$this->confirm('Do you want to skip existing records and import only new ones?', true)) {
                    if (!$this->confirm('Do you want to update existing records?', false)) {
                        $this->info('Import cancelled.');
                        return Command::SUCCESS;
                    }
                }
            }

            // Confirm import
            if (!$this->confirm('Do you want to proceed with the import?', true)) {
                $this->info('Import cancelled.');
                return Command::SUCCESS;
            }

            // Perform import
            try {
                DB::beginTransaction();

                if (strtolower($selectedExport['type']) === 'json') {
                    foreach ($analysisResult['records'] as $record) {
                        // Remove status fields before insert
                        unset($record['data']['_status'], $record['data']['_similarity'], $record['data']['_matched_by']);

                        if (isset($record['data']['id'])) {
                            DB::table($this->selectedTable)
                                ->updateOrInsert(
                                    ['id' => $record['data']['id']],
                                    $record['data']
                                );
                        } else {
                            DB::table($this->selectedTable)->insert($record['data']);
                        }
                    }
                } else { // SQL
                    DB::unprepared(File::get($selectedExport['path']));
                }

                DB::commit();
                $this->info('✅ Import completed successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error('❌ Import failed: ' . $e->getMessage());
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }

    private function drawLogo()
    {
        $this->line("\n");
        $this->info('╔═══════════════════════════════════════════╗');
        $this->info('║           DATABASE IMPORTER               ║');
        $this->info('╚═══════════════════════════════════════════╝');
        $this->line("\n");
    }

    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    private function readExportFile($path, $type)
    {
        $type = strtolower($type);
        if ($type === 'json') {
            $data = json_decode(File::get($path), true);
            return $this->analyzeSimilarity($data, $this->selectedTable);
        } elseif ($type === 'sql') {
            // For SQL files, extract INSERT statements and convert to structured data
            $content = File::get($path);
            $data = $this->parseSqlInserts($content);
            return $this->analyzeSimilarity($data, $this->selectedTable);
        }
        return null;
    }

    private function parseSqlInserts($content)
    {
        $data = [];

        // First, try to extract column names from INSERT statement
        preg_match("/INSERT INTO `?\w+`? \((.*?)\) VALUES/i", $content, $columnMatch);
        $columns = [];

        if (!empty($columnMatch[1])) {
            // Clean up column names
            $columns = array_map(function ($col) {
                return trim(str_replace(['`', "'"], '', $col));
            }, explode(',', $columnMatch[1]));
        }

        // Then extract values
        preg_match_all("/VALUES\s*(\((.*?)\))/i", $content, $matches);

        if (!empty($matches[2])) {
            foreach ($matches[2] as $valueSet) {
                // Split values and clean them
                $values = str_getcsv($valueSet, ',', "'");
                $values = array_map(function ($val) {
                    return trim($val, "' \t\n\r\0\x0B");
                }, $values);

                // Create row with proper column names
                $row = [];
                foreach ($values as $index => $value) {
                    $columnName = isset($columns[$index]) ? $columns[$index] : "column_{$index}";
                    $row[$columnName] = $value;
                }
                $data[] = $row;
            }
        }

        return $data;
    }

    private function analyzeSimilarity($data, $tableName)
    {
        if (empty($data)) {
            return $data;
        }

        // Get table columns
        $tableColumns = Schema::getColumnListing($tableName);
        $existingData = DB::table($tableName)->get();

        $analyzedData = [
            'records' => [],
            'statistics' => [
                'total_records' => count($data),
                'existing_records' => 0,
                'new_records' => 0,
                'overall_similarity' => 0,
                'duplicate_records' => [],
            ]
        ];

        foreach ($data as $newRecord) {
            $recordAnalysis = [
                'data' => $newRecord,
                'status' => '✅ New',
                'similarity' => 0,
                'matching_fields' => [],
            ];

            $highestSimilarity = 0;
            $bestMatch = null;
            $matchingFields = [];

            // Compare with existing records
            foreach ($existingData as $existingRecord) {
                $currentSimilarity = 0;
                $currentMatching = [];
                $totalFields = 0;

                foreach ($tableColumns as $column) {
                    if (isset($newRecord[$column])) {
                        $totalFields++;
                        $newValue = $newRecord[$column];
                        $existingValue = $existingRecord->$column;

                        if ($this->compareValues($newValue, $existingValue)) {
                            $currentSimilarity++;
                            $currentMatching[] = $column;
                        }
                    }
                }

                $similarityPercentage = $totalFields > 0
                    ? round(($currentSimilarity / $totalFields) * 100)
                    : 0;

                if ($similarityPercentage > $highestSimilarity) {
                    $highestSimilarity = $similarityPercentage;
                    $bestMatch = $existingRecord;
                    $matchingFields = $currentMatching;
                }
            }

            // Determine record status based on similarity
            if ($highestSimilarity > 90) {
                $recordAnalysis['status'] = '⚠️ Duplicate';
                $analyzedData['statistics']['existing_records']++;
            } elseif ($highestSimilarity > 50) {
                $recordAnalysis['status'] = '⚡ Similar';
                $analyzedData['statistics']['new_records']++;
            } else {
                $analyzedData['statistics']['new_records']++;
            }

            $recordAnalysis['similarity'] = $highestSimilarity;
            $recordAnalysis['matching_fields'] = $matchingFields;

            $analyzedData['records'][] = $recordAnalysis;
        }

        // Calculate overall similarity
        $analyzedData['statistics']['overall_similarity'] = round(
            ($analyzedData['statistics']['existing_records'] / $analyzedData['statistics']['total_records']) * 100
        );

        return $analyzedData;
    }

    private function compareValues($value1, $value2)
    {
        // Handle NULL values
        if (is_null($value1) && is_null($value2)) {
            return true;
        }

        // Clean and normalize values for comparison
        $value1 = is_string($value1) ? trim(strtolower($value1)) : $value1;
        $value2 = is_string($value2) ? trim(strtolower($value2)) : $value2;

        // Remove any extra quotes
        $value1 = trim($value1, "'\"");
        $value2 = trim($value2, "'\"");

        // Convert to strings for final comparison
        return (string)$value1 === (string)$value2;
    }
}
