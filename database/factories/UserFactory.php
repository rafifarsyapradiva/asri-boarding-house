<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'no_hp' => fake()->unique()->numerify('08##########'),
            'nik' => fake()->unique()->numerify('################'),
            'nama_wali' => fake()->name(),
            'no_wali' => fake()->numerify('08##########'),
            'role' => User::ROLE_PENYEWA,
            'password' => static::$password ??= Hash::make('password'),
            'is_active' => 1,
            'require_password_change' => false,
        ];
    }

    /**
     * State untuk user dengan role Admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
        ]);
    }

    /**
     * State untuk user nonaktif.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => 0,
        ]);
    }

    /**
     * State untuk user yang diwajibkan mengubah password pada login pertama.
     */
    public function requirePasswordChange(): static
    {
        return $this->state(fn (array $attributes) => [
            'require_password_change' => true,
        ]);
    }

    /**
     * State untuk user dengan profil belum lengkap (no_hp, NIK, nama_wali kosong).
     */
    public function incompleteProfile(): static
    {
        return $this->state(fn (array $attributes) => [
            'no_hp' => null,
            'nik' => null,
            'nama_wali' => null,
            'no_wali' => null,
        ]);
    }
}
