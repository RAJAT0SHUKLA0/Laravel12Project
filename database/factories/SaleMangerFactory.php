<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class SaleMangerFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'rahul kumawat',
            'email' => "rahul@revateam.com",
            'password' => Hash::make('8005924966'),
            'staff_id' => 'TD-002' ,
            'role_id' => '2',
            'mobile' => "8005924966",
        ];
    }
}
