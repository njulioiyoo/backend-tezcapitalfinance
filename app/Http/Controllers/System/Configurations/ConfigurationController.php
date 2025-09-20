<?php

namespace App\Http\Controllers\System\Configurations;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\Configurations\ConfigurationStoreRequest;
use App\Http\Requests\System\Configurations\ConfigurationUpdateRequest;
use App\Models\Configuration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ConfigurationController extends Controller
{
    public function index(): Response
    {
        $configurations = Configuration::getAllGrouped();

        return Inertia::render('system/configurations/Configurations', [
            'configurations' => $configurations,
        ]);
    }

    public function api(): JsonResponse
    {
        // Only return configuration groups that are relevant for the configuration menu
        // Exclude homepage and about as they have their own endpoints
        $relevantGroups = [
            Configuration::GROUP_GENERAL,
            Configuration::GROUP_BRANDING,
            Configuration::GROUP_HOMEPAGE,
            Configuration::GROUP_CONTACT,
            Configuration::GROUP_LANGUAGE,
            Configuration::GROUP_MAINTENANCE,
            Configuration::GROUP_CREDIT,
            Configuration::GROUP_ABOUT,
            Configuration::GROUP_BANNERS,
        ];

        $configurations = Configuration::whereIn('group', $relevantGroups)
            ->get()
            ->groupBy('group')
            ->map(function ($configs) {
                return $configs->mapWithKeys(function ($config) {
                    return [$config->key => [
                        'value' => $config->getValue(),
                        'type' => $config->type,
                        'description' => $config->description,
                        'is_public' => $config->is_public,
                    ]];
                })->all();
            })
            ->toArray();

        return response()->json([
            'data' => $configurations,
            'message' => 'Configuration data retrieved successfully',
            'response_time_ms' => round((microtime(true) - LARAVEL_START) * 1000, 2),
            'success' => true,
        ]);
    }

    public function getByGroup(string $group): JsonResponse
    {
        $configurations = Configuration::getByGroup($group);

        return response()->json([
            'data' => $configurations,
        ]);
    }

    public function getPublic(): JsonResponse
    {
        $configurations = Configuration::getPublic();

        return response()->json([
            'data' => $configurations,
        ]);
    }

    public function store(ConfigurationStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('file') && $data['type'] === 'file') {
            $file = $request->file('file');
            $path = $file->store('configurations', 'public');
            $data['value'] = $path;
        }

        // Use updateOrCreate to handle both create and update cases
        $configuration = Configuration::updateOrCreate(
            ['key' => $data['key']],
            $data
        );

        return response()->json([
            'message' => 'Configuration saved successfully',
            'data' => [
                'key' => $configuration->key,
                'value' => $configuration->getValue(),
                'type' => $configuration->type,
                'group' => $configuration->group,
                'description' => $configuration->description,
                'is_public' => $configuration->is_public,
            ],
        ], 201);
    }

    public function show(Configuration $configuration): JsonResponse
    {
        return response()->json([
            'data' => [
                'key' => $configuration->key,
                'value' => $configuration->getValue(),
                'type' => $configuration->type,
                'group' => $configuration->group,
                'description' => $configuration->description,
                'is_public' => $configuration->is_public,
            ],
        ]);
    }

    public function update(ConfigurationUpdateRequest $request, Configuration $configuration): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('file') && $data['type'] === 'file') {
            if ($configuration->value && $configuration->type === 'file') {
                Storage::disk('public')->delete($configuration->value);
            }

            $file = $request->file('file');
            $path = $file->store('configurations', 'public');
            $data['value'] = $path;
        }

        $configuration->update($data);

        return response()->json([
            'message' => 'Configuration updated successfully',
            'data' => [
                'key' => $configuration->key,
                'value' => $configuration->getValue(),
                'type' => $configuration->type,
                'group' => $configuration->group,
                'description' => $configuration->description,
                'is_public' => $configuration->is_public,
            ],
        ]);
    }

    public function destroy(Configuration $configuration): JsonResponse
    {
        if ($configuration->type === 'file' && $configuration->value) {
            Storage::disk('public')->delete($configuration->value);
        }

        $configuration->delete();

        return response()->json([
            'message' => 'Configuration deleted successfully',
        ]);
    }

    public function updateMultiple(Request $request): JsonResponse
    {
        \Log::info('updateMultiple called', ['payload' => $request->all()]);

        // Handle both JSON and FormData requests
        $configurations = [];
        $fileFields = [];
        
        if ($request->has('configurations')) {
            if (is_string($request->input('configurations'))) {
                // FormData with JSON string
                $configurations = json_decode($request->input('configurations'), true);
                \Log::info('📋 Decoded configurations from FormData', ['count' => count($configurations)]);
            } else {
                // Regular JSON request
                $configurations = $request->input('configurations');
            }
        }
        
        if ($request->has('file_fields')) {
            $fileFields = json_decode($request->input('file_fields'), true);
            \Log::info('📁 Decoded file fields from FormData', ['count' => count($fileFields)]);
        }
        
        if (empty($configurations) && empty($fileFields)) {
            return response()->json([
                'success' => false,
                'message' => 'No configurations provided',
            ], 400);
        }

        // Validate configurations
        foreach ($configurations as $index => $config) {
            if (!isset($config['key']) || !isset($config['type']) || !isset($config['group'])) {
                return response()->json([
                    'success' => false,
                    'message' => "Invalid configuration at index {$index}",
                ], 400);
            }
        }
        
        // Validate file fields
        foreach ($fileFields as $index => $fileField) {
            if (!isset($fileField['fieldName']) || !isset($fileField['key']) || !isset($fileField['type'])) {
                return response()->json([
                    'success' => false,
                    'message' => "Invalid file field at index {$index}",
                ], 400);
            }
        }
        $results = [];
        
        // First, handle file uploads to get their paths
        $iconPaths = [];
        $regularFilePaths = [];
        
        \Log::info('🔧 Processing file uploads', ['fileFields' => count($fileFields)]);
        
        foreach ($fileFields as $fileField) {
            $fieldName = $fileField['fieldName'];
            
            if ($request->hasFile($fieldName)) {
                $file = $request->file($fieldName);
                $path = $file->store('configurations', 'public');
                
                if (isset($fileField['metadata'])) {
                    // This is an icon for array items
                    $metadata = $fileField['metadata'];
                    $iconPaths[$metadata['arrayKey']][$metadata['arrayIndex']][$metadata['arrayField']] = $path;
                    
                    \Log::info('🖼️ Icon uploaded for array', [
                        'arrayKey' => $metadata['arrayKey'],
                        'index' => $metadata['arrayIndex'],
                        'field' => $metadata['arrayField'],
                        'path' => $path,
                        'fileSize' => $file->getSize(),
                        'fileName' => $file->getClientOriginalName()
                    ]);
                } else {
                    // Regular file upload
                    $regularFilePaths[$fileField['key']] = $path;
                    
                    \Log::info('📁 Regular file uploaded', [
                        'key' => $fileField['key'],
                        'path' => $path,
                        'fileSize' => $file->getSize(),
                        'fileName' => $file->getClientOriginalName()
                    ]);
                }
                
                // Store the file configuration
                $results[] = Configuration::updateOrCreate(
                    ['key' => $fileField['key']],
                    [
                        'value' => $path,
                        'type' => $fileField['type'],
                        'group' => $fileField['group'],
                        'description' => $fileField['description'] ?? null,
                        'is_public' => $fileField['is_public'] ?? false,
                    ]
                );
            } else {
                \Log::warning('🚨 File not found in request', ['fieldName' => $fieldName]);
            }
        }
        
        \Log::info('🖼️ Total icon paths collected', ['iconPaths' => $iconPaths]);
        \Log::info('📁 Total regular file paths collected', ['regularFilePaths' => $regularFilePaths]);

        foreach ($configurations as $configData) {
            \Log::info('Processing config', ['configData' => $configData]);

            // Handle value processing based on type
            $value = $configData['value'];

            // Skip file configurations as they're handled separately above
            if ($configData['type'] === 'file') {
                continue;
            }

            // For JSON type, ensure we store it properly
            if ($configData['type'] === 'json' && is_string($value)) {
                // If it's already a JSON string, decode it first so Laravel can encode it again
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $value = $decoded;
                }
            }
            
            // For JSON arrays, update with uploaded icon paths
            if ($configData['type'] === 'json' && is_array($value) && isset($iconPaths[$configData['key']])) {
                foreach ($iconPaths[$configData['key']] as $index => $fields) {
                    if (isset($value[$index])) {
                        foreach ($fields as $field => $path) {
                            $value[$index][$field] = $path;
                        }
                    }
                }
                \Log::info('Updated JSON array with icon paths', [
                    'key' => $configData['key'],
                    'iconPaths' => $iconPaths[$configData['key']]
                ]);
            }

            // For boolean type, ensure proper boolean value
            if ($configData['type'] === 'boolean') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }

            // For integer type, ensure proper integer value
            if ($configData['type'] === 'integer') {
                $value = (int) $value;
            }

            $config = Configuration::updateOrCreate(
                ['key' => $configData['key']],
                [
                    'value' => $value,
                    'type' => $configData['type'],
                    'group' => $configData['group'],
                    'description' => $configData['description'] ?? null,
                    'is_public' => $configData['is_public'] ?? false,
                ]
            );

            \Log::info('Config saved', [
                'id' => $config->id,
                'key' => $config->key,
                'value' => $config->value,
                'processed_value' => $value,
                'fresh_value' => $config->fresh()->value,
            ]);

            $results[] = [
                'key' => $config->key,
                'value' => $config->getValue(),
                'type' => $config->type,
                'group' => $config->group,
                'description' => $config->description,
                'is_public' => $config->is_public,
            ];
        }

        \Log::info('updateMultiple completed', ['results' => $results]);

        return response()->json([
            'message' => 'Configurations updated successfully',
            'data' => $results,
        ]);
    }


}
