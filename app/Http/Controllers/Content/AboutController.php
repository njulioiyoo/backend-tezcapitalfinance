<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    public function index(): Response
    {
        // Get all about configurations grouped
        $aboutConfigurations = Configuration::where('group', Configuration::GROUP_ABOUT)
            ->get()
            ->mapWithKeys(function ($config) {
                return [$config->key => [
                    'value' => $config->getValue(),
                    'type' => $config->type,
                    'description' => $config->description,
                    'is_public' => $config->is_public,
                ]];
            })
            ->toArray();

        return Inertia::render('content/AboutContent', [
            'configurations' => [
                'about' => $aboutConfigurations
            ],
        ]);
    }
}