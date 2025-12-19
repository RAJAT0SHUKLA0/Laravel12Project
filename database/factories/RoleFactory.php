<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Role;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\role>
 */
class RoleFactory extends Factory
{
     protected $model = Role::class;

    protected static $roles = [
        'admin',
        'sale manager',
        'sale person',
        'rider',
    ];

    protected static $index = 0;

    public function definition(): array
    {
        $roleName = self::$roles[self::$index++] ?? 'admin';

        return [
            'name' => $roleName,
        ];
    }
}
