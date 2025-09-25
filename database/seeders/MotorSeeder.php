<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Motor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MotorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting motor data import...');
        
        // Read JSON file
        $jsonPath = base_path('motor_complete_data.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->error('motor_complete_data.json file not found!');
            return;
        }
        
        $jsonContent = file_get_contents($jsonPath);
        $motorData = json_decode($jsonContent, true);
        
        if (!$motorData) {
            $this->command->error('Failed to decode JSON data!');
            return;
        }
        
        $this->command->info('Found ' . count($motorData) . ' motor records to import...');
        
        // Clear existing data
        $this->command->info('Clearing existing motor data...');
        DB::table('motors')->truncate();
        
        // Import data in chunks for better performance
        $chunkSize = 100;
        $chunks = array_chunk($motorData, $chunkSize);
        $totalProcessed = 0;
        
        foreach ($chunks as $index => $chunk) {
            $dataToInsert = [];
            
            foreach ($chunk as $motor) {
                // Validate required fields
                if (!isset($motor['name']) || !isset($motor['price']) || !isset($motor['area']) || !isset($motor['period'])) {
                    $this->command->warn('Skipping motor record due to missing required fields: ' . json_encode($motor));
                    continue;
                }
                
                $dataToInsert[] = [
                    'name' => $motor['name'],
                    'price' => $motor['price'],
                    'area' => $motor['area'],
                    'period' => $motor['period'],
                    'installment_plans' => json_encode($motor['installment_plans'] ?? []),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            if (!empty($dataToInsert)) {
                try {
                    DB::table('motors')->insert($dataToInsert);
                    $totalProcessed += count($dataToInsert);
                    $this->command->info('Processed chunk ' . ($index + 1) . '/' . count($chunks) . ' (' . count($dataToInsert) . ' records)');
                } catch (\Exception $e) {
                    $this->command->error('Error inserting chunk ' . ($index + 1) . ': ' . $e->getMessage());
                    Log::error('Motor seeder error', [
                        'chunk' => $index + 1,
                        'error' => $e->getMessage(),
                        'data_sample' => array_slice($dataToInsert, 0, 2) // Log first 2 records for debugging
                    ]);
                }
            }
        }
        
        $this->command->info('Motor import completed!');
        $this->command->info('Total records processed: ' . $totalProcessed);
        
        // Verify import
        $totalInDb = DB::table('motors')->count();
        $this->command->info('Total records in database: ' . $totalInDb);
        
        if ($totalProcessed !== $totalInDb) {
            $this->command->warn('Warning: Processed count does not match database count!');
        } else {
            $this->command->info('✅ Import verification successful!');
        }
    }
}