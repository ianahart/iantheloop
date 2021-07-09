<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    /**
     * a post with an uploaded photo
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function uploadedPhoto()
    {
        return $this->state(function (array $attributes) {
            return [
                'photo_link' => 'https://hart-looped.s3.amazonaws.com/60a6c999541f0test_image.jpg',
                'photo_filename' => 'posts/test_image.jpg',
            ];
        });
    }

    /**
     * a post with an uploaded video
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function uploadedVideo()
    {
        return $this->state(function (array $attributes) {
            return [
                'video_link' => 'https://hart-looped.s3.amazonaws.com/posts/1622899494sample_video_3mb.mp4',
                'video_filename' => 'posts/1622899494sample_video_3mb.mp4',
            ];
        });
    }



    public function definition()
    {
        return [
            'subject_user_id' => User::factory(),
            'author_user_id' => User::factory(),
            'post_text' => $this->faker->text(100),
            'photo_filename' => null,
            'video_filename' => null,
            'photo_link' => null,
            'video_link' => null,
            'likes' => 0,
            'comments_count' => 0,
            'like_records' => null,
        ];
    }
}
