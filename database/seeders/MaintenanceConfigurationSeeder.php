<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class MaintenanceConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $maintenanceConfigs = [
            [
                'key' => 'maintenance_mode_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => Configuration::GROUP_MAINTENANCE,
                'description' => 'Enable or disable maintenance mode for the entire application',
                'is_public' => true,
            ],
            [
                'key' => 'maintenance_title',
                'value' => 'Site Under Maintenance',
                'type' => 'string',
                'group' => Configuration::GROUP_MAINTENANCE,
                'description' => 'Title displayed during maintenance mode',
                'is_public' => true,
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'We are currently performing scheduled maintenance. Please check back in a few minutes.',
                'type' => 'text',
                'group' => Configuration::GROUP_MAINTENANCE,
                'description' => 'Message displayed during maintenance mode',
                'is_public' => true,
            ],
            [
                'key' => 'maintenance_estimated_time',
                'value' => '',
                'type' => 'string',
                'group' => Configuration::GROUP_MAINTENANCE,
                'description' => 'Estimated time when maintenance will be completed (ISO format)',
                'is_public' => true,
            ],
            [
                'key' => 'maintenance_contact_email',
                'value' => 'support@tez-capital.com',
                'type' => 'email',
                'group' => Configuration::GROUP_MAINTENANCE,
                'description' => 'Contact email for maintenance inquiries',
                'is_public' => true,
            ],
            [
                'key' => 'maintenance_bypass_ips',
                'value' => '[]',
                'type' => 'json',
                'group' => Configuration::GROUP_MAINTENANCE,
                'description' => 'IP addresses that can bypass maintenance mode',
                'is_public' => false,
            ],
        ];

        foreach ($maintenanceConfigs as $config) {
            Configuration::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
}