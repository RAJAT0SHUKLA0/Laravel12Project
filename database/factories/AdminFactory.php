<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class AdminFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'admin',
            'email' => "admin@gmail.com",
            'password' => Hash::make('8302243646'),
            'staff_id' => 'TD-001' ,
            'role_id' => '1',
            'mobile' => "8302243646",
        ];
    }
}
