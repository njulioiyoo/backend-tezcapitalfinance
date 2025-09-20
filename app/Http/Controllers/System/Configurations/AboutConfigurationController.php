<?php

namespace App\Http\Controllers\System\Configurations;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class AboutConfigurationController extends Controller
{
    /**
     * Display the about configuration form
     */
    public function index(): Response
    {
        $configurations = $this->getAboutConfigurations();
        
        return Inertia::render('System/Configurations/AboutConfiguration', [
            'configurations' => $configurations
        ]);
    }

    /**
     * Update about configurations
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            // Our Story
            'about_our_story_title_id' => 'required|string|max:255',
            'about_our_story_title_en' => 'required|string|max:255',
            'about_our_story_content_id' => 'required|string',
            'about_our_story_content_en' => 'required|string',
            'about_our_story_image' => 'nullable|string',

            // Vision
            'about_vision_title_id' => 'required|string|max:255',
            'about_vision_title_en' => 'required|string|max:255',
            'about_vision_content_id' => 'required|string',
            'about_vision_content_en' => 'required|string',

            // Mission
            'about_mission_title_id' => 'required|string|max:255',
            'about_mission_title_en' => 'required|string|max:255',
            'about_mission_items' => 'required|array',
            'about_mission_items.*.id' => 'required|integer',
            'about_mission_items.*.text_id' => 'required|string',
            'about_mission_items.*.text_en' => 'required|string',
            'about_mission_items.*.order' => 'required|integer',

            // Logo Philosophy
            'about_logo_philosophy_title_id' => 'required|string|max:255',
            'about_logo_philosophy_title_en' => 'required|string|max:255',
            'about_logo_philosophy_image' => 'nullable|string',
            'about_logo_philosophy_points' => 'required|array',
            'about_logo_philosophy_points.*.id' => 'required|integer',
            'about_logo_philosophy_points.*.text_id' => 'required|string',
            'about_logo_philosophy_points.*.text_en' => 'required|string',
            'about_logo_philosophy_points.*.order' => 'required|integer',

            // F.A.S.T Values
            'about_fast_values_title_id' => 'required|string|max:255',
            'about_fast_values_title_en' => 'required|string|max:255',
            'about_fast_values_subtitle_id' => 'nullable|string',
            'about_fast_values_subtitle_en' => 'nullable|string',
            'about_fast_values_items' => 'required|array',
            'about_fast_values_items.*.id' => 'required|integer',
            'about_fast_values_items.*.title_id' => 'required|string',
            'about_fast_values_items.*.title_en' => 'required|string',
            'about_fast_values_items.*.description_id' => 'required|string',
            'about_fast_values_items.*.description_en' => 'required|string',
            'about_fast_values_items.*.icon' => 'nullable|string',
            'about_fast_values_items.*.order' => 'required|integer',

            // I.D.C Values
            'about_idc_values_title_id' => 'required|string|max:255',
            'about_idc_values_title_en' => 'required|string|max:255',
            'about_idc_values_subtitle_id' => 'nullable|string',
            'about_idc_values_subtitle_en' => 'nullable|string',
            'about_idc_values_items' => 'required|array',
            'about_idc_values_items.*.id' => 'required|integer',
            'about_idc_values_items.*.title_id' => 'required|string',
            'about_idc_values_items.*.title_en' => 'required|string',
            'about_idc_values_items.*.description_id' => 'required|string',
            'about_idc_values_items.*.description_en' => 'required|string',
            'about_idc_values_items.*.icon' => 'nullable|string',
            'about_idc_values_items.*.order' => 'required|integer',

            // Closing Statement
            'about_closing_statement_id' => 'required|string',
            'about_closing_statement_en' => 'required|string',
        ]);

        try {
            foreach ($validated as $key => $value) {
                // Handle JSON fields
                if (in_array($key, ['about_mission_items', 'about_logo_philosophy_points', 'about_fast_values_items', 'about_idc_values_items'])) {
                    $value = json_encode($value);
                }

                Configuration::updateOrCreate(
                    ['key' => $key],
                    [
                        'key' => $key,
                        'value' => $value,
                        'type' => $this->getConfigurationType($key),
                        'group' => 'about',
                        'description' => $this->getConfigurationDescription($key),
                        'is_public' => true,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'About configurations updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update configurations: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all about configurations
     */
    private function getAboutConfigurations(): array
    {
        $keys = [
            'about_our_story_title_id',
            'about_our_story_title_en',
            'about_our_story_content_id',
            'about_our_story_content_en',
            'about_our_story_image',
            'about_vision_title_id',
            'about_vision_title_en',
            'about_vision_content_id',
            'about_vision_content_en',
            'about_mission_title_id',
            'about_mission_title_en',
            'about_mission_items',
            'about_logo_philosophy_title_id',
            'about_logo_philosophy_title_en',
            'about_logo_philosophy_image',
            'about_logo_philosophy_points',
            'about_fast_values_title_id',
            'about_fast_values_title_en',
            'about_fast_values_subtitle_id',
            'about_fast_values_subtitle_en',
            'about_fast_values_items',
            'about_idc_values_title_id',
            'about_idc_values_title_en',
            'about_idc_values_subtitle_id',
            'about_idc_values_subtitle_en',
            'about_idc_values_items',
            'about_closing_statement_id',
            'about_closing_statement_en',
        ];

        $configurations = Configuration::whereIn('key', $keys)->pluck('value', 'key')->toArray();

        // Decode JSON fields
        foreach (['about_mission_items', 'about_logo_philosophy_points', 'about_fast_values_items', 'about_idc_values_items'] as $jsonKey) {
            if (isset($configurations[$jsonKey])) {
                $configurations[$jsonKey] = json_decode($configurations[$jsonKey], true) ?: [];
            }
        }

        return $configurations;
    }

    /**
     * Get configuration type
     */
    private function getConfigurationType(string $key): string
    {
        if (in_array($key, ['about_mission_items', 'about_logo_philosophy_points', 'about_fast_values_items', 'about_idc_values_items'])) {
            return 'json';
        }
        
        if (str_contains($key, 'content') || str_contains($key, 'statement') || str_contains($key, 'subtitle')) {
            return 'text';
        }
        
        if (str_contains($key, 'image')) {
            return 'file';
        }
        
        return 'string';
    }

    /**
     * Get configuration description
     */
    private function getConfigurationDescription(string $key): string
    {
        $descriptions = [
            'about_our_story_title_id' => 'Our Story section title (Indonesian)',
            'about_our_story_title_en' => 'Our Story section title (English)',
            'about_our_story_content_id' => 'Our Story content (Indonesian)',
            'about_our_story_content_en' => 'Our Story content (English)',
            'about_our_story_image' => 'Our Story section image',
            'about_vision_title_id' => 'Vision section title (Indonesian)',
            'about_vision_title_en' => 'Vision section title (English)',
            'about_vision_content_id' => 'Vision content (Indonesian)',
            'about_vision_content_en' => 'Vision content (English)',
            'about_mission_title_id' => 'Mission section title (Indonesian)',
            'about_mission_title_en' => 'Mission section title (English)',
            'about_mission_items' => 'Mission items list',
            'about_logo_philosophy_title_id' => 'Logo Philosophy section title (Indonesian)',
            'about_logo_philosophy_title_en' => 'Logo Philosophy section title (English)',
            'about_logo_philosophy_image' => 'Logo Philosophy section image',
            'about_logo_philosophy_points' => 'Logo Philosophy points',
            'about_fast_values_title_id' => 'F.A.S.T Values section title (Indonesian)',
            'about_fast_values_title_en' => 'F.A.S.T Values section title (English)',
            'about_fast_values_subtitle_id' => 'F.A.S.T Values section subtitle (Indonesian)',
            'about_fast_values_subtitle_en' => 'F.A.S.T Values section subtitle (English)',
            'about_fast_values_items' => 'F.A.S.T Values items (Flexible, Agile, Solution-oriented, Trustworthy)',
            'about_idc_values_title_id' => 'I.D.C Values section title (Indonesian)',
            'about_idc_values_title_en' => 'I.D.C Values section title (English)',
            'about_idc_values_subtitle_id' => 'I.D.C Values section subtitle (Indonesian)',
            'about_idc_values_subtitle_en' => 'I.D.C Values section subtitle (English)',
            'about_idc_values_items' => 'I.D.C Values items (Integrity, Dependable, Competent)',
            'about_closing_statement_id' => 'About page closing statement (Indonesian)',
            'about_closing_statement_en' => 'About page closing statement (English)',
        ];

        return $descriptions[$key] ?? 'About page configuration';
    }
}