<?php

namespace App\Http\Requests;

use App\Models\Configuration;
use App\Models\Content;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $bilingualEnabled = Configuration::get('bilingual_enabled', false);
        $type = $this->get('type', 'news');
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $rules = [
            'type' => ['required', Rule::in(array_keys(Content::getTypes()))],
            'tags' => 'nullable|array',
            'author' => 'nullable|string|max:255',
            'source_url' => 'nullable|url',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'show_credit_simulation' => 'boolean',
            'published_at' => 'nullable|date|before_or_equal:now',
            'status' => ['required', Rule::in(array_keys(Content::getStatuses()))],
            'sort_order' => 'integer|min:0',
            'gallery' => 'nullable|array',
        ];

        // Add category validation based on type
        $rules['category'] = $this->getCategoryRule($type);

        // Add type-specific rules
        $rules = array_merge($rules, $this->getTypeSpecificRules($type));

        // Add bilingual rules
        $rules = array_merge($rules, $this->getBilingualRules($bilingualEnabled, $type));

        // Add file upload rules
        $rules = array_merge($rules, $this->getFileUploadRules($type, $isUpdate));

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'published_at.before_or_equal' => 'Tanggal publikasi tidak boleh lebih dari tanggal dan waktu saat ini.',
            'published_at.date' => 'Format tanggal publikasi tidak valid.',
        ];
    }

    /**
     * Get category validation rule for type
     */
    private function getCategoryRule(string $type): array
    {
        return match($type) {
            'announcement' => ['nullable'], // Announcements don't need category constraints
            'career' => ['nullable'],
            'event' => ['nullable', Rule::in(array_keys(Content::getEventCategories()))],
            'partner' => ['nullable', Rule::in(array_keys(Content::getPartnerCategories()))],
            'service' => ['nullable', Rule::in(array_keys(Content::getServiceCategories()))],
            'workplace' => ['nullable', Rule::in(array_keys(Content::getWorkplaceCategories()))],
            default => ['nullable', Rule::in(array_keys(Content::getNewsCategories()))],
        };
    }

    /**
     * Get type-specific validation rules
     */
    private function getTypeSpecificRules(string $type): array
    {
        if ($type === 'event') {
            return [
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'organizer' => 'nullable|string|max:255',
                'price' => 'nullable|numeric|min:0',
                'max_participants' => 'nullable|integer|min:1',
                'registered_count' => 'integer|min:0',
            ];
        }

        if ($type === 'partner') {
            return [
                'source_url' => 'nullable|url',
            ];
        }

        if ($type === 'service') {
            return [
                'interest_rate' => 'nullable|numeric|min:0|max:100',
                'service_duration' => 'nullable|string|max:255',
                'interest_list' => 'nullable|array',
                'document_list' => 'nullable|array',
                'requirements_id' => 'nullable|string',
                'requirements_en' => 'nullable|string',
                'benefits_id' => 'nullable|string',
                'benefits_en' => 'nullable|string',
            ];
        }

        if ($type === 'work-division') {
            return [
                'title_id' => 'required|string|max:25',
                'title_en' => 'nullable|string|max:25',
                'content_id' => 'nullable|string|max:100',
                'content_en' => 'nullable|string|max:100',
            ];
        }

        return [];
    }

    /**
     * Get bilingual validation rules
     */
    private function getBilingualRules(bool $bilingualEnabled, string $type): array
    {
        if ($bilingualEnabled) {
            $rules = [
                'title_id' => 'required|string|max:255',
                'title_en' => 'required|string|max:255',
                'excerpt_id' => 'nullable|string',
                'excerpt_en' => 'nullable|string',
                'content_id' => 'nullable|string',
                'content_en' => 'nullable|string',
                'meta_title_id' => 'nullable|string|max:60',
                'meta_title_en' => 'nullable|string|max:60',
                'meta_description_id' => 'nullable|string|max:160',
                'meta_description_en' => 'nullable|string|max:160',
            ];

            if ($type === 'event') {
                $rules['location_id'] = 'nullable|string|max:255';
                $rules['location_en'] = 'nullable|string|max:255';
            }

            return $rules;
        }

        $rules = [
            'title_id' => 'required|string|max:255',
            'excerpt_id' => 'nullable|string',
            'content_id' => 'nullable|string',
            'meta_title_id' => 'nullable|string|max:60',
            'meta_description_id' => 'nullable|string|max:160',
        ];

        if ($type === 'event') {
            $rules['location_id'] = 'nullable|string|max:255';
        }

        return $rules;
    }

    /**
     * Get file upload validation rules
     */
    private function getFileUploadRules(string $type, bool $isUpdate): array
    {
        $rules = [];

        // Always add featured_image validation rule when file is present
        if ($this->hasFile('featured_image')) {
            $rules['featured_image'] = 'image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120';
        } elseif ($type === 'partner' && !$isUpdate) {
            $rules['featured_image'] = 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120';
        } else {
            // For updates, don't validate featured_image field unless there's a file upload
            // This allows form submission without image validation errors
            if (!$isUpdate) {
                $rules['featured_image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120';
            }
        }

        return $rules;
    }
}