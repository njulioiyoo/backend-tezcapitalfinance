<?php

namespace App\Console\Commands;

use App\Models\Motor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportMotorData extends Command
{
    protected $signature = 'motor:import {file_path}';

    protected $description = 'Import motor data from Excel file';

    public function handle()
    {
        $filePath = $this->argument('file_path');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info('Starting motor data import...');

        try {
            $pythonScript = base_path('../parse_excel.py');
            $this->createPythonScript($pythonScript);

            $output = shell_exec("cd " . base_path('..') . " && source excel_env/bin/activate && python3 parse_excel.py '{$filePath}'");
            
            if (!$output) {
                $this->error('Failed to parse Excel file');
                return 1;
            }

            $motorData = json_decode($output, true);
            
            if (!$motorData) {
                $this->error('Invalid JSON output from parser');
                return 1;
            }

            DB::transaction(function () use ($motorData) {
                foreach ($motorData as $motor) {
                    Motor::updateOrCreate(
                        ['name' => $motor['name'], 'area' => $motor['area']],
                        $motor
                    );
                }
            });

            $this->info('Motor data imported successfully!');
            $this->info('Total motors imported: ' . count($motorData));

            unlink($pythonScript);

            return 0;
        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function createPythonScript($path)
    {
        $script = <<<'PYTHON'
import pandas as pd
import json
import sys

def parse_motor_data(file_path):
    try:
        df = pd.read_excel(file_path, sheet_name=0)
        motors = []
        current_motor = None
        current_period = None
        
        for i, row in df.iterrows():
            # Check for period info
            if pd.notna(row.iloc[0]) and 'Periode' in str(row.iloc[0]):
                current_period = str(row.iloc[1]) if pd.notna(row.iloc[1]) else None
                continue
            
            # Check if this is a motor name row (has price)
            if (pd.notna(row.iloc[2]) and 
                isinstance(row.iloc[2], (int, float)) and 
                row.iloc[2] > 1000000 and
                pd.notna(row.iloc[1]) and 
                isinstance(row.iloc[1], str) and
                str(row.iloc[1]) not in ['ANGSURAN', 'DP', 'DP %']):
                
                # Save previous motor if exists
                if current_motor:
                    motors.append(current_motor)
                
                # Start new motor
                current_motor = {
                    'name': str(row.iloc[1]).strip(),
                    'price': float(row.iloc[2]),
                    'area': 'Jakarta',
                    'period': current_period,
                    'installment_plans': [],
                    'is_active': True
                }
                continue
            
            # Check if this is installment data (numeric DP % row)
            if (current_motor and 
                pd.notna(row.iloc[0]) and 
                isinstance(row.iloc[0], (int, float)) and
                pd.notna(row.iloc[1]) and 
                isinstance(row.iloc[1], (int, float))):
                
                # Parse installment plan
                dp_percent = float(row.iloc[0])
                dp_amount = float(row.iloc[1])
                
                installments = {}
                # Parse monthly installments (columns 2-6)
                tenors = [11, 17, 23, 29, 35]
                for j, tenor in enumerate(tenors):
                    col_idx = j + 2
                    if col_idx < len(row) and pd.notna(row.iloc[col_idx]):
                        installments[f"{tenor}_months"] = float(row.iloc[col_idx])
                
                if installments:
                    current_motor['installment_plans'].append({
                        'dp_percent': dp_percent,
                        'dp_amount': dp_amount,
                        'installments': installments
                    })
        
        # Add last motor
        if current_motor:
            motors.append(current_motor)
        
        return motors
    
    except Exception as e:
        print(f"Error: {e}", file=sys.stderr)
        return []

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: python3 parse_excel.py <file_path>", file=sys.stderr)
        sys.exit(1)
    
    file_path = sys.argv[1]
    motors = parse_motor_data(file_path)
    print(json.dumps(motors, indent=2))
PYTHON;

        file_put_contents($path, $script);
    }
}
