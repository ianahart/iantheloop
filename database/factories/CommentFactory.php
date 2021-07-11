<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * a comment that is a reply to another comment
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function replyComment()
    {
        return $this
            ->state(
                function (array $attributes) {
                    return [
                        'id' => 2,
                        'reply_to_comment_id' => 1,
                    ];
                }
            );
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'post_id' => Post::factory(),
            'comment_text' => $this->faker->text(50),
            'likes' => 0,
            'is_edited' => false,

        ];
    }
}
