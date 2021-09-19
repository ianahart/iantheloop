<?php

namespace Database\Factories;

use App\Models\Story;
use App\Models\User as authorUser;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Story::class;

    /**
     * Make a story a "photo story" opposed to "text story".
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function photoStory()
    {
        return $this->state(function (array $attributes) {
            return [
                'photo_link' => 'https://hart-looped.s3.amazonaws.com/60a6c999541f0test_image.jpg',
                'photo_filename' => 'stories/test_image.jpg',
            ];
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => authorUser::factory(),
            'profile_id' => Profile::factory(),
            'muted' => false,
            'read_at' => NULL,
            'photo_link' => NULL,
            'photo_filename' => NULL,
            'text' => $this->faker->text(50),
            'created_at_unix' => NULL,
            'expire_in_unix' => NULL,
            'duration' => 10000,
            'background' => 'linear-gradient(90deg, rgba(216, 160, 197, 0.8),rgba(201, 236, 167, 0.7)), linear-gradient(30deg, rgba(144, 154, 183, 0.8),rgba(167, 195, 130, 0.8)), linear-gradient(85deg, rgba(205, 189, 152, 0.8),rgba(191, 180, 164, 0.8)), linear-gradient(115deg, rgba(234, 163, 158, 0.8),rgba(164, 240, 198, 0.8));',
            'font_size' => '12px',
            'story_type' => 'text',
            'color' => 'black',
            'alignment' => 'center',
        ];
    }
}
