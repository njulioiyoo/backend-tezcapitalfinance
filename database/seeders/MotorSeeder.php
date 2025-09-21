<?php

namespace Database\Seeders;

use App\Models\Motor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MotorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Read motor data from JSON file
        $jsonPath = database_path('../motor_complete_data.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error('Motor data file not found: ' . $jsonPath);
            return;
        }

        $motorData = json_decode(File::get($jsonPath), true);
        
        if (!$motorData) {
            $this->command->error('Failed to parse motor data JSON');
            return;
        }

        $this->command->info('Seeding motors from JSON file...');
        
        foreach ($motorData as $motor) {
            Motor::create([
                'name' => $motor['name'],
                'price' => $motor['price'],
                'area' => $motor['area'],
                'period' => $motor['period'],
                'installment_plans' => $motor['installment_plans'],
                'is_active' => true,
            ]);
        }

        $this->command->info('Motors seeded successfully! Total: ' . count($motorData) . ' motors');
    }
}