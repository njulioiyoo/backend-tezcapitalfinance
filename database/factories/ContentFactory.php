<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content>
 */
class ContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['news', 'event']);
        $isEvent = $type === 'event';
        
        $newsCategories = ['update', 'announcement', 'general', 'press-release'];
        $eventCategories = ['seminar', 'workshop', 'conference', 'webinar'];
        
        return [
            'type' => $type,
            'category' => $isEvent 
                ? $this->faker->randomElement($eventCategories)
                : $this->faker->randomElement($newsCategories),
            'title_id' => $this->faker->sentence(4),
            'title_en' => $this->faker->sentence(4),
            'excerpt_id' => $this->faker->text(200),
            'excerpt_en' => $this->faker->text(200),
            'content_id' => $this->faker->paragraphs(3, true),
            'content_en' => $this->faker->paragraphs(3, true),
            'author' => $this->faker->name(),
            'location_id' => $isEvent ? $this->faker->address() : null,
            'location_en' => $isEvent ? $this->faker->address() : null,
            'start_date' => $isEvent ? $this->faker->dateTimeBetween('+1 day', '+30 days') : null,
            'end_date' => $isEvent ? $this->faker->dateTimeBetween('+1 day', '+30 days') : null,
            'organizer' => $isEvent ? $this->faker->company() : null,
            'price' => $isEvent && $this->faker->boolean(30) ? $this->faker->numberBetween(50000, 500000) : null,
            'max_participants' => $isEvent ? $this->faker->numberBetween(20, 200) : null,
            'registered_count' => $isEvent ? $this->faker->numberBetween(0, 50) : 0,
            'is_published' => $this->faker->boolean(80),
            'published_at' => $this->faker->boolean(80) ? $this->faker->dateTimeThisMonth() : null,
            'is_featured' => $this->faker->boolean(20),
            'status' => $this->faker->randomElement(['draft', 'published', 'archived']),
            'view_count' => $this->faker->numberBetween(0, 1000),
            'like_count' => $this->faker->numberBetween(0, 100),
            'share_count' => $this->faker->numberBetween(0, 50),
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }
}
