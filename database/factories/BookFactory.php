<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use DateMalformedStringException;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * @return array<string, mixed>
     * @throws DateMalformedStringException
     */
    public function definition(): array
    {
        $borrowedAt = $this->faker->optional()->dateTimeBetween('-30 days', '-3 days');
        $borrowedBy = $borrowedAt ? User::factory() : null;
        $returnedAt = $borrowedAt ? $this->faker->optional()->dateTimeBetween($borrowedAt, 'now') : null;
        $deadline   = $borrowedAt ? (clone $borrowedAt)->modify('+2 days') : null;

        return [
            'title'       => $this->faker->sentence(3),
            'author'      => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'register_by' => User::factory(),
            'borrowed_by' => $borrowedBy,
            'borrowed_at' => $borrowedAt,
            'returned_at' => $returnedAt,
            'deadline'    => $deadline,
            'notified_at' => null,
        ];
    }
}
