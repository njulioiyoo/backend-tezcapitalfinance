<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Content;

class UpdateCareersWithDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Update existing careers with department data
        $departmentMapping = [
            'Procurement Supervisor' => [
                'department_id' => 'Finance',
                'department_en' => 'Finance'
            ],
            'Senior Talent Acquisition' => [
                'department_id' => 'People & Operation',
                'department_en' => 'People & Operation'
            ],
            'Transfer Pricing Assistant' => [
                'department_id' => 'Finance',
                'department_en' => 'Finance'
            ],
            'Talent Acquisition Senior Manager' => [
                'department_id' => 'People & Operation',
                'department_en' => 'People & Operation'
            ],
            'Software Engineer' => [
                'department_id' => 'Technology',
                'department_en' => 'Technology'
            ],
            'Marketing Manager' => [
                'department_id' => 'Marketing',
                'department_en' => 'Marketing'
            ],
            'HR Specialist' => [
                'department_id' => 'People & Operation',
                'department_en' => 'People & Operation'
            ],
            'Frontend Developer Intern' => [
                'department_id' => 'Technology',
                'department_en' => 'Technology'
            ]
        ];

        foreach ($departmentMapping as $title => $department) {
            Content::where('type', 'career')
                ->where('title_id', $title)
                ->update([
                    'department_id' => $department['department_id'],
                    'department_en' => $department['department_en']
                ]);
        }

        $this->command->info('Updated existing careers with department data');
    }
}