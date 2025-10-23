<?php

namespace Database\Factories;

use App\Models\MovieSearch;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovieSearchFactory extends Factory
{
    protected $model = MovieSearch::class;

    public function definition(): array
    {
        return [
            'search_request' => $this->faker->words(2, true),
            'ip_address' => $this->faker->ipv4(),
            'created_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the search was made recently.
     */
    public function recent(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'created_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
            ];
        });
    }

    /**
     * Indicate that the search was made with a specific term.
     */
    public function withSearchTerm(string $term): Factory
    {
        return $this->state(function (array $attributes) use ($term) {
            return [
                'search_request' => $term,
            ];
        });
    }

    /**
     * Indicate that the search was made from a specific IP.
     */
    public function fromIp(string $ip): Factory
    {
        return $this->state(function (array $attributes) use ($ip) {
            return [
                'ip_address' => $ip,
            ];
        });
    }
}
